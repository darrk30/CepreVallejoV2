<?php

// app/Filament/Profesor/Pages/MyPayments.php

namespace App\Filament\Profesor\Pages;

use App\Models\Payment;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Support\Facades\Auth;

class MyPayments extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected string $view = 'filament.profesor.pages.my-payments';
    protected static ?string $title = 'Mis Pagos';
    protected static ?string $slug = 'mis-pagos';

    public static function canAccess(): bool
    {
        return Auth::user()->can('view_pagos_teacher');
    }

    public function getPayments()
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            return collect(); // Retorna una colección vacía si no hay profesor vinculado
        }

        // Usamos la relación 'payments' definida en el modelo Teacher
        return $teacher->payments()
            ->where('estado', 'Completado')
            ->latest('fecha_pago') // Ordena por fecha de la más nueva a la más antigua
            ->orderBy('id', 'desc') // Si la fecha es igual, pone el último ID arriba
            ->get();
    }

    public function getStats()
    {
        $payments = $this->getPayments();
        return [
            'total' => $payments->sum('monto'),
            'count' => $payments->count(),
            'last_amount' => $payments->first()?->monto ?? 0,
            'last_date' => $payments->first()?->fecha_pago?->format('d/m/Y') ?? '—',
        ];
    }
}
