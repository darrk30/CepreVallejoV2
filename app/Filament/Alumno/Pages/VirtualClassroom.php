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

    // Para cada ciclo necesitamos los cursos — lo exponemos como método helper
    public function getCoursesForCycle(int $cycleId): Collection
    {
        return CicloCourse::with(['course'])
            ->where('ciclo_id', $cycleId)
            ->whereHas('course', fn($q) => $q->where('estado', 'activo'))
            ->get();
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
