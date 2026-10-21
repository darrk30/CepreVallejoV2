<?php

namespace App\Filament\Profesor\Pages;

use Illuminate\Support\Facades\Auth;

class ReproductorVideo extends \App\Filament\Alumno\Pages\ReproductorVideo
{
    public static function canAccess(): bool
    {
        return Auth::user()?->can('view_videoteca') ?? false;
    }
}
