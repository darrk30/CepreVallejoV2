<?php

namespace App\Filament\Profesor\Pages;

use Illuminate\Support\Facades\Auth;

class Biblioteca extends \App\Filament\Alumno\Pages\Biblioteca
{
    public static function canAccess(): bool
    {
        return Auth::user()?->can('view_biblioteca') ?? false;
    }
}
