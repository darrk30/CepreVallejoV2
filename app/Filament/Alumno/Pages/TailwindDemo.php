<?php

namespace App\Filament\Alumno\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class TailwindDemo extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static ?string $navigationLabel = 'Demo Tailwind';
    protected static ?string $title = 'Demo: ¿Funciona Tailwind aquí?';
    protected string $view = 'filament.alumno.pages.tailwind-demo';
    protected static ?int $navigationSort = 999;

    public function getHeading(): string
    {
        return '';
    }
}
