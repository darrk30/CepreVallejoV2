<?php

namespace App\Filament\Profesor\Pages;

use Illuminate\Support\Facades\Auth;

class Podcasts extends \App\Filament\Alumno\Pages\Podcasts
{
    public static function canAccess(): bool
    {
        return Auth::user()?->can('view_podcasts') ?? false;
    }
}
