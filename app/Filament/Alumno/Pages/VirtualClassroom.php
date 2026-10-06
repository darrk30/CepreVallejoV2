<?php

namespace App\Filament\Alumno\Pages;

use App\Models\Inscription;
use App\Models\CicloCourse;
use App\Models\CicloCourseTeacher;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class VirtualClassroom extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.alumno.pages.virtual-classroom';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPath;
    protected static ?string $navigationLabel = 'Mi Aula';
    protected static ?int $navigationSort = 5;

    // Estado del formulario: ['ciclo' => id del ciclo elegido]
    public ?array $data = [];

    public function mount(): void
    {
        // Por defecto se muestra el ciclo más reciente
        $this->form->fill(['ciclo' => $this->cicloSeleccionadoId]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ciclo')
                    ->label('Ciclo')
                    // Array (no closure): Filament trata un closure como opciones dinámicas
                    // y pide las opciones al servidor cada vez que se abre el select.
                    ->options($this->ciclosOrdenados
                        ->mapWithKeys(fn ($grupo) => [
                            $grupo->first()->academic_cycle_id => $grupo->first()->academicCycle->nombre,
                        ])
                        ->all())
                    ->default(fn () => $this->cicloSeleccionadoId)
                    ->selectablePlaceholder(false)
                    ->native(false)
                    ->live(),
            ])
            ->statePath('data');
    }

    /**
     * Matrículas del alumno agrupadas por ciclo. Se consulta UNA sola vez por
     * petición (#[Computed]); antes cada acceso a la propiedad repetía la consulta.
     */
    #[Computed]
    public function cycles(): Collection
    {
        $user    = Auth::user();
        $student = $user->student;

        // Verificar que el student exista Y esté activo
        if (!$student || !$student->estado) {
            return collect();
        }

        return Inscription::with(['academicCycle'])
            ->where('student_id', $student->id)
            ->whereIn('estado_pago', ['pagado', 'parcial', 'pendiente'])
            ->whereHas('academicCycle', fn($q) => $q->where('estado', true))
            ->get()
            ->groupBy(fn($ins) => $ins->academicCycle->nombre);
    }

    /**
     * Ciclos del alumno (un grupo por ciclo), del más recientemente creado al más antiguo.
     */
    #[Computed]
    public function ciclosOrdenados(): Collection
    {
        // Desempate por id: dos ciclos creados en el mismo segundo siguen un orden estable
        return $this->cycles->sortByDesc(
            fn($grupo) => [$grupo->first()->academicCycle->created_at?->timestamp ?? 0, $grupo->first()->academic_cycle_id]
        );
    }

    /**
     * ID del ciclo que se muestra: el elegido en el select o, por defecto, el último creado.
     */
    #[Computed]
    public function cicloSeleccionadoId(): ?int
    {
        $ciclos = $this->ciclosOrdenados;

        if ($ciclos->isEmpty()) {
            return null;
        }

        $elegidoId = (int) ($this->data['ciclo'] ?? 0);
        $elegido = $ciclos->first(fn($grupo) => (int) $grupo->first()->academic_cycle_id === $elegidoId);

        return (int) ($elegido ?? $ciclos->first())->first()->academic_cycle_id;
    }

    /**
     * Grupo (nombre => inscripciones) del ciclo seleccionado. Solo se pinta uno.
     */
    #[Computed]
    public function cicloSeleccionadoGrupo(): Collection
    {
        $id = $this->cicloSeleccionadoId;

        return $this->ciclosOrdenados
            ->filter(fn($grupo) => (int) $grupo->first()->academic_cycle_id === $id)
            ->mapWithKeys(fn($grupo, $nombre) => [$nombre => $grupo]);
    }

    // Los cursos de TODOS los ciclos se traen en una sola consulta (coursesByCycle)
    // y aquí solo hacemos una búsqueda en memoria por ciclo.
    public function getCoursesForCycle(int $cycleId): Collection
    {
        return $this->coursesByCycle->get($cycleId) ?? collect();
    }

    #[Computed]
    public function coursesByCycle(): Collection
    {
        $cycleIds = $this->cycles
            ->map(fn($inscriptions) => $inscriptions->first()->academic_cycle_id)
            ->unique()
            ->values();

        if ($cycleIds->isEmpty()) {
            return collect();
        }

        return CicloCourse::with(['course'])
            ->whereIn('ciclo_id', $cycleIds)
            ->whereHas('course', fn($q) => $q->where('estado', 'activo'))
            ->get()
            ->groupBy('ciclo_id');
    }

    /**
     * Mapa "ciclo|turno|curso" => id de la asignación (ciclo_course_teacher).
     * Cada tarjeta abre la asignación de SU ciclo y SU turno, aunque el mismo
     * curso exista en otros ciclos o turnos.
     */
    #[Computed]
    public function asignacionesMap(): Collection
    {
        $inscripciones = $this->cycles->map(fn($grupo) => $grupo->first());

        if ($inscripciones->isEmpty()) {
            return collect();
        }

        return CicloCourseTeacher::query()
            ->whereIn('turno_id', $inscripciones->pluck('turno_id')->unique())
            ->whereHas('cicloCourse', fn($q) => $q->whereIn('ciclo_id', $inscripciones->pluck('academic_cycle_id')->unique()))
            ->with('cicloCourse:id,ciclo_id,course_id')
            ->orderBy('id')
            ->get()
            // Si hubiera varias asignaciones iguales, gana la más reciente
            ->mapWithKeys(fn($a) => [$a->cicloCourse->ciclo_id . '|' . $a->turno_id . '|' . $a->cicloCourse->course_id => $a->id]);
    }

    #[Computed]
    public function inscripcion(): ?Inscription
    {
        $student = Auth::user()->student;
        if (!$student || !$student->estado) return null;

        return Inscription::with('academicCycle')
            ->whereIn('estado_pago', ['pagado', 'parcial', 'pendiente'])
            ->where('student_id', $student->id)
            ->whereHas('academicCycle', fn($q) => $q->where('estado', true))
            ->latest()
            ->first();
    }

    public function getHeading(): string
    {
        return '';
    }
}
