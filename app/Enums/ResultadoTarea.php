<?php

namespace App\Enums;

enum ResultadoTarea: string
{
    case EXITO = 'exito';
    case ERROR = 'error';

    public function label(): string
    {
        return match ($this) {
            self::EXITO => 'Éxito',
            self::ERROR => 'Error',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EXITO => 'success',
            self::ERROR => 'danger',
        };
    }
}
