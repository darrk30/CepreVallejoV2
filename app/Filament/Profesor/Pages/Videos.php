<?php

namespace App\Filament\Profesor\Pages;

use Illuminate\Support\Facades\Auth;

/**
 * Videoteca para el profesor: reutiliza la lógica y la vista del alumno
 * (no se toca el panel del alumno), pero con su propio permiso.
 */
class Videos extends \App\Filament\Alumno\Pages\Videos
{
    public static function canAccess(): bool
    {
        return Auth::user()?->can('view_videoteca') ?? false;
    }
}
