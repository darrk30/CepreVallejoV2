<?php

namespace App\Enums;

/**
 * Estado de una matrícula (Inscription), independiente del estado de pago
 * (App\Models\Inscription::estado_pago).
 *
 * Por ahora solo maneja Activa/Inactiva. Si en el futuro se necesitan más
 * estados (ej. "trasladada", "anulada"), se agregan aquí como nuevos cases.
 */
enum EstadoMatricula: string
{
    case ACTIVA = 'activa';
    case INACTIVA = 'inactiva';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVA => 'Activa',
            self::INACTIVA => 'Inactiva',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVA => 'success',
            self::INACTIVA => 'danger',
        };
    }
}
