<?php

namespace App\Filament\Alumno\Pages;

use App\Models\Inscription;
use App\Models\CicloCourse;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class VirtualClassroom extends Page
{
    protected string $view = 'filament.alumno.pages.virtual-classroom';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPath;
    protected static ?string $navigationLabel = 'Mi Aula';
    protected static ?int $navigationSort = 5;

    public function getCyclesProperty(): Collection
    {
        $user    = Auth::user();
        $student = $user->student; // ya funciona con hasOne

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

    // Para cada ciclo necesitamos los cursos — lo exponemos como método helper.
    // Los cursos de TODOS los ciclos se traen en una sola consulta (ver
    // getCoursesByCycleProperty) y aquí solo hacemos una búsqueda en memoria,
    // en vez de lanzar una query nueva por cada ciclo dentro del @foreach.
    public function getCoursesForCycle(int $cycleId): Collection
    {
        return $this->coursesByCycle->get($cycleId) ?? collect();
    }

    public function getCoursesByCycleProperty(): Collection
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

    public function getInscripcionProperty(): ?Inscription
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
