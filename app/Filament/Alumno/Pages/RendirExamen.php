<?php

namespace App\Filament\Alumno\Pages;

use App\Enums\AreaAcademica;
use App\Models\Carrera;
use App\Models\ExamenOrdinario;
use App\Models\Intento;
use App\Models\RespuestasAlumno;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;

class RendirExamen extends Page
{
    protected string $view = 'filament.alumno.pages.rendir-examen';

    protected static bool $shouldRegisterNavigation = false;

    // Propiedades de la URL (Recibiremos el ID del examen)
    #[Url]  // <-- esto hace que ?examen_id=5 llene la propiedad automáticamente
    public ?int $examen_id = null;

    // Estado de la página: 'configuracion', 'en_progreso', 'finalizado'
    public $estado_vista = 'configuracion';

    // Datos cargados de BD
    public $examen;
    public $carreras = [];
    public $total_preguntas = 0;

    // true si el alumno ya vio el detalle (respuestas correctas) de algún
    // intento anterior de este mismo examen: ya no puede volver a rendirlo.
    public bool $bloqueado = false;

    // Formulario de inicio
    public $carrera_seleccionada_id;

    // Datos del Intento en curso
    public $fecha_inicio;

    // Array para almacenar las respuestas seleccionadas por el alumno
    // Formato: [1 => 'A', 2 => 'C', ...]
    public $respuestas = [];

    // Resultado final
    public $resultado_intento;

    public function mount(): void  // sin parámetros
    {
        if (!$this->examen_id) {
            abort(404, 'Examen no encontrado.');
        }

        $this->examen = ExamenOrdinario::withCount('respuestasCorrectas')
            ->findOrFail($this->examen_id);

        $this->total_preguntas = $this->examen->respuestas_correctas_count;
        $this->carreras = Carrera::where('estado', 'activo')->get();

        $this->bloqueado = Intento::where('user_id', Auth::id())
            ->where('examen_ordinario_id', $this->examen_id)
            ->whereNotNull('detalles_vistos_at')
            ->exists();
    }

    public function iniciarExamen()
    {
        if ($this->bloqueado) {
            return;
        }

        $this->validate([
            'carrera_seleccionada_id' => 'required|exists:carreras,id',
        ], [
            'carrera_seleccionada_id.required' => 'Debes seleccionar la carrera a la que postulas.',
        ]);

        $this->fecha_inicio = now();
        $this->estado_vista = 'en_progreso';
    }

    public function marcarRespuesta($numeroPregunta, $opcion)
    {
        // Alternativas válidas: A, B, C, D, E
        $this->respuestas[$numeroPregunta] = $opcion;
    }

    public function finalizarExamen()
    {
        if ($this->bloqueado || $this->estado_vista !== 'en_progreso') {
            return;
        }

        $fecha_fin        = now();
        $inicio           = Carbon::parse($this->fecha_inicio);
        $tiempo_utilizado = $inicio->diffInSeconds($fecha_fin);

        // 1. Obtener área del alumno
        $carrera  = Carrera::find($this->carrera_seleccionada_id);
        $areaRaw  = $carrera->getRawOriginal('area');
        $area     = AreaAcademica::tryFrom($areaRaw);

        if (!$area) {
            $this->addError('carrera_seleccionada_id', "El área '{$areaRaw}' no es válida.");
            return;
        }

        // 2. Obtener respuestas correctas con bloque y asignatura
        $respuestas_correctas = $this->examen->respuestasCorrectas
            ->keyBy('numero_pregunta');

        // 3. Calcular puntaje según área, bloque y asignatura de cada pregunta
        $puntaje = 0;

        foreach ($this->respuestas as $num => $opcion_marcada) {
            if (!$respuestas_correctas->has($num)) continue;

            $pregunta   = $respuestas_correctas->get($num);
            $asignatura = \App\Enums\AsignaturaExamen::tryFrom($pregunta->asignatura);

            if ($opcion_marcada === $pregunta->opcion_correcta) {
                // Correcta: puntos según bloque
                if ($pregunta->bloque === \App\Enums\BloqueExamen::HABILIDADES->value) {
                    $puntaje += $area->puntosCorrectaBloqueI(); // 20.0000 para todos
                } else {
                    $puntaje += $asignatura
                        ? $area->puntosCorrectaBloqueII($asignatura)
                        : 0;
                }
            } else {
                // Incorrecta: -1.1250 para todos
                $puntaje += $area->puntosIncorrecta();
            }
        }

        $puntaje = max(0, $puntaje);

        // 4. Determinar si aprobó según el puntaje mínimo del área
        $puntaje_minimo = $area->puntajeMinimo();
        $es_aprobado    = $puntaje >= $puntaje_minimo;

        // 5. Crear el registro en la tabla intentos
        $intento = Intento::create([
            'user_id'             => Auth::id(),
            'examen_ordinario_id' => $this->examen_id,
            'carrera_id'          => $this->carrera_seleccionada_id,
            'puntaje_obtenido'    => $puntaje,
            'es_aprobado'         => $es_aprobado,
            'fecha_inicio'        => $this->fecha_inicio,
            'fecha_fin'           => $fecha_fin,
            'tiempo_utilizado'    => $tiempo_utilizado,
        ]);

        // 6. Guardar detalle de respuestas marcadas
        $detalles = [];
        foreach ($this->respuestas as $num => $opcion_marcada) {
            $pregunta   = $respuestas_correctas->get($num);
            $asignatura = $pregunta
                ? \App\Enums\AsignaturaExamen::tryFrom($pregunta->asignatura)
                : null;

            // Calcular puntos obtenidos en esta pregunta para el detalle
            if ($pregunta && $opcion_marcada === $pregunta->opcion_correcta) {
                $puntos_obtenidos = $pregunta->bloque === \App\Enums\BloqueExamen::HABILIDADES->value
                    ? $area->puntosCorrectaBloqueI()
                    : ($asignatura ? $area->puntosCorrectaBloqueII($asignatura) : 0);
            } else {
                $puntos_obtenidos = $area->puntosIncorrecta();
            }

            $detalles[] = [
                'intento_id'          => $intento->id,
                'numero_pregunta'     => $num,
                'opcion_seleccionada' => $opcion_marcada,
                'puntos_obtenidos'    => $puntos_obtenidos,
                'created_at'          => now(),
                'updated_at'          => now(),
            ];
        }
        RespuestasAlumno::insert($detalles);

        // 7. Mostrar resultados
        $this->resultado_intento = tap($intento)->load('carrera');
        $this->estado_vista      = 'finalizado';
    }
}
