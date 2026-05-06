<?php

namespace App\Filament\Profesor\Pages;

use App\Models\CicloCourseTeacher;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VirtualClassroom extends Page
{
    protected string $view = 'filament.profesor.pages.virtual-classroom';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPath;

    // protected static ?string $title = 'Mi Aula Virtual';

    public function getCyclesProperty(): Collection
    {
        // 1. Obtenemos el profesor vinculado al usuario actual
        $teacher = Auth::user()->teacher;

        // 2. Si no hay profesor, retornamos una colección vacía
        if (!$teacher) {
            return collect();
        }

        // 3. Consulta con validación de estados
        return CicloCourseTeacher::with([
            'cicloCourse.academicCycle',
            'cicloCourse.course'
        ])
            ->where('teacher_id', $teacher->id)
            // Validar que el Ciclo Académico esté activo (true)
            ->whereHas('cicloCourse.academicCycle', function ($query) {
                $query->where('estado', true);
            })
            // Validar que el Curso tenga estado "activo" (string)
            ->whereHas('cicloCourse.course', function ($query) {
                $query->where('estado', 'activo');
            })
            // Opcional: Validar que la asignación misma esté activa si tienes ese campo
            // ->where('estado', 'activo') 
            ->get()
            ->groupBy(fn($item) => $item->cicloCourse->academicCycle->nombre);
    }

    public function getHeading(): string
    {
        return ''; // Esto quita el texto del título, pero mantiene el espacio si es necesario
    }
}
