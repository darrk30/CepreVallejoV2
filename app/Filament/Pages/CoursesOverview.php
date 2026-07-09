<?php

namespace App\Filament\Pages;

use App\Models\CicloCourse;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class CoursesOverview extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;
    protected static string|UnitEnum|null $navigationGroup = 'Aula Virtual';
    protected static ?string $navigationLabel = 'Cursos del Ciclo';
    protected static ?string $title = 'Cursos del Ciclo Activo';
    protected static ?int $navigationSort = 4;
    protected string $view = 'filament.pages.courses-overview';

    public static function canAccess(): bool
    {
        return Auth::user()->can('manage_all_course_content');
    }

    public function getHeading(): string
    {
        return '';
    }

    /**
     * Cursos de los ciclos académicos activos, con sus asignaciones de
     * profesor/turno agrupadas por curso (un curso puede tener varias).
     */
    public function getCoursesByCycleProperty(): Collection
    {
        return CicloCourse::query()
            ->whereHas('academicCycle', fn($q) => $q->where('estado', true))
            ->whereHas('course', fn($q) => $q->where('estado', 'activo'))
            ->with([
                'course',
                'academicCycle',
                'cicloCourseTeachers' => fn($q) => $q->with(['teacher.user', 'turno'])->withCount('contents'),
            ])
            ->get()
            ->groupBy(fn(CicloCourse $cc) => $cc->academicCycle->nombre ?? 'Ciclo');
    }
}
