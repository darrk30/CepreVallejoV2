<?php

namespace App\Filament\Profesor\Pages;

use Illuminate\Support\Facades\Auth;

class PodcastsAutor extends \App\Filament\Alumno\Pages\PodcastsAutor
{
    public static function canAccess(): bool
    {
        return Auth::user()?->can('view_podcasts') ?? false;
    }
}
