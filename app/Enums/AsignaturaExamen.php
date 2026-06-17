<?php

namespace App\Enums;

enum AsignaturaExamen: string
{
    case VERBAL              = 'verbal';
    case MATEMATICA          = 'matematica';
    case ARITMETICA          = 'aritmetica';
    case GEOMETRIA           = 'geometria';
    case ALGEBRA             = 'algebra';
    case TRIGONOMETRIA       = 'trigonometria';
    case LENGUAJE            = 'lenguaje';
    case LITERATURA          = 'literatura';
    case PSICOLOGIA          = 'psicologia';
    case EDUCACION_CIVICA    = 'educacion_civica';
    case HISTORIA_PERU       = 'historia_peru';
    case GEOGRAFIA           = 'geografia';
    case ECONOMIA            = 'economia';
    case FILOSOFIA           = 'filosofia';
    case FISICA              = 'fisica';
    case QUIMICA             = 'quimica';
    case BIOLOGIA            = 'biologia';

    public function label(): string
    {
        return match($this) {
            self::VERBAL              => 'Verbal',
            self::MATEMATICA          => 'Matemática',
            self::ARITMETICA          => 'Aritmética',
            self::GEOMETRIA           => 'Geometría',
            self::ALGEBRA             => 'Álgebra',
            self::TRIGONOMETRIA       => 'Trigonometría',
            self::LENGUAJE            => 'Lenguaje',
            self::LITERATURA          => 'Literatura',
            self::PSICOLOGIA          => 'Psicología',
            self::EDUCACION_CIVICA    => 'Educación Cívica',
            self::HISTORIA_PERU       => 'Historia del Perú y Universal',
            self::GEOGRAFIA           => 'Geografía',
            self::ECONOMIA            => 'Economía',
            self::FILOSOFIA           => 'Filosofía',
            self::FISICA              => 'Física',
            self::QUIMICA             => 'Química',
            self::BIOLOGIA            => 'Biología',
        };
    }

    public function bloque(): BloqueExamen
    {
        return match($this) {
            self::VERBAL,
            self::MATEMATICA          => BloqueExamen::HABILIDADES,
            default                   => BloqueExamen::ESPECIALIDAD,
        };
    }
}