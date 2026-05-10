<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class CustomDashboard extends BaseDashboard
{
    protected static ?string $title = 'Panel de Control';

    // Tu permiso de Spatie
    public static function canAccess(): bool
    {
        return auth()->user()->can('view_dashboard');
    }
}