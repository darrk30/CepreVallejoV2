<?php

namespace App\Enums;

enum FrecuenciaTarea: string
{
    case DIARIA = 'diaria';
    case CADA_HORA = 'cada_hora';
    case SEMANAL = 'semanal';
    case MENSUAL = 'mensual';
    case PERSONALIZADA = 'personalizada';

    public function label(): string
    {
        return match ($this) {
            self::DIARIA => 'Diaria',
            self::CADA_HORA => 'Cada hora',
            self::SEMANAL => 'Semanal',
            self::MENSUAL => 'Mensual',
            self::PERSONALIZADA => 'Personalizada (cron)',
        };
    }

    /**
     * Si esta frecuencia necesita el campo "hora".
     */
    public function requiereHora(): bool
    {
        return in_array($this, [self::DIARIA, self::SEMANAL, self::MENSUAL]);
    }

    public function requiereDiaSemana(): bool
    {
        return $this === self::SEMANAL;
    }

    public function requiereDiaMes(): bool
    {
        return $this === self::MENSUAL;
    }

    public function requiereCron(): bool
    {
        return $this === self::PERSONALIZADA;
    }
}
