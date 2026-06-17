<?php

namespace App\Filament\Alumno\Pages;

use App\Models\ExamenOrdinario;
use App\Models\Intento;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;

class MisIntentos extends Page
{
    protected string $view = 'filament.alumno.pages.mis-intentos';
    // protected static ?string $navigationLabel = 'Mis Intentos';
    protected static bool $shouldRegisterNavigation = false;

    // ── Estado ───────────────────────────────────────────────────
    public array  $examenes     = [];
    public array  $intentos     = [];
    public string $examenTitulo = '';

    #[Url(as: 'examen_id')]
    public ?int $examenId = null;

    // Modal detalle
    public bool   $modalOpen         = false;
    public array  $modalDetalle      = [];
    public int    $modalIntentoId    = 0;
    public string $modalExamenTitulo = '';
    public float  $modalPuntaje      = 0;
    public bool   $modalAprobado     = false;
    public float  $modalPuntajeMinimo = 0;
    public string $modalAreaNombre   = '';

    // ── Mount ────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->examenes = ExamenOrdinario::where('estado', 'activo')
            ->whereHas('intentos', fn($q) => $q->where('user_id', Auth::id()))
            ->withCount(['intentos' => fn($q) => $q->where('user_id', Auth::id())])
            ->latest()
            ->get()
            ->map(fn($e) => [
                'id'       => $e->id,
                'titulo'   => $e->titulo,
                'intentos' => $e->intentos_count,
            ])
            ->toArray();

        // Si viene con examenId en la URL, seleccionarlo automáticamente
        if ($this->examenId) {
            $this->seleccionarExamen($this->examenId);
        }
    }

    // ── Seleccionar examen ───────────────────────────────────────
    public function seleccionarExamen(int $id): void
    {
        $this->examenId     = $id;
        $this->examenTitulo = collect($this->examenes)->firstWhere('id', $id)['titulo'] ?? '';
        $this->cargarIntentos();
    }

    // ── Cargar intentos del alumno ───────────────────────────────
    private function cargarIntentos(): void
    {
        $this->intentos = Intento::where('user_id', Auth::id())
            ->where('examen_ordinario_id', $this->examenId)
            ->with(['carrera', 'examen'])
            ->latest('fecha_inicio')
            ->get()
            ->map(fn($i) => [
                'id'         => $i->id,
                'carrera'    => $i->carrera->nombre ?? 'Sin carrera',
                'puntaje'    => (float) $i->puntaje_obtenido,
                'aprobado'   => (bool) $i->es_aprobado,
                'fecha'      => $i->fecha_inicio
                    ? \Carbon\Carbon::parse($i->fecha_inicio)->format('d/m/Y H:i')
                    : '—',
                'tiempo_min' => intdiv((int) $i->tiempo_utilizado, 60),
                'tiempo_seg' => str_pad((int) $i->tiempo_utilizado % 60, 2, '0', STR_PAD_LEFT),
                'pdf_path'   => $i->examen->pdf_path ?? null,
            ])
            ->toArray();
    }

    // ── Abrir modal de detalle ───────────────────────────────────
    public function verDetalle(int $intentoId): void
    {
        $intento = Intento::with(['respuestasAlumno', 'examen.respuestasCorrectas', 'carrera'])
            ->findOrFail($intentoId);

        abort_if($intento->user_id !== Auth::id(), 403);

        $this->modalIntentoId    = $intentoId;
        $this->modalExamenTitulo = $intento->examen->titulo ?? '';
        $this->modalPuntaje      = (float) $intento->puntaje_obtenido;
        $this->modalAprobado     = (bool) $intento->es_aprobado;

        // Puntaje mínimo del área
        $areaRaw = $intento->carrera?->getRawOriginal('area');
        $area    = $areaRaw ? \App\Enums\AreaAcademica::tryFrom($areaRaw) : null;

        $this->modalPuntajeMinimo = $area?->puntajeMinimo() ?? 0;
        $this->modalAreaNombre    = $areaRaw ?? 'Sin área';

        // Mapear respuestas correctas para comparar
        $correctas = $intento->examen->respuestasCorrectas
            ->keyBy('numero_pregunta');

        $this->modalDetalle = $intento->respuestasAlumno // ✅ nombre correcto
            ->sortBy('numero_pregunta')
            ->map(function ($r) use ($correctas) {
                $correcta   = $correctas->get($r->numero_pregunta);
                $esCorrecta = $correcta && $r->opcion_seleccionada === $correcta->opcion_correcta;

                return [
                    'numero'      => $r->numero_pregunta,
                    'marcada'     => $r->opcion_seleccionada,
                    'correcta'    => $correcta->opcion_correcta ?? '—',
                    'asignatura'  => $correcta->asignatura ?? '—',
                    'es_correcta' => $esCorrecta,
                    'puntos'      => (float) $r->puntos_obtenidos,
                ];
            })
            ->values()
            ->toArray();

        $this->modalOpen = true;
    }

    public function cerrarModal(): void
    {
        $this->modalOpen    = false;
        $this->modalDetalle = [];
    }
}
