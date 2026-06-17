<?php

namespace App\Enums;

enum BloqueExamen: int
{
    case HABILIDADES  = 1;
    case ESPECIALIDAD = 2;

    public function label(): string
    {
        return match($this) {
            self::HABILIDADES  => 'Bloque I — Habilidades',
            self::ESPECIALIDAD => 'Bloque II — Especialidad',
        };
    }
}