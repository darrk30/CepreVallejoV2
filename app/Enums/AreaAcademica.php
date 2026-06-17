<?php

namespace App\Enums;

enum AreaAcademica: string
{
    case CIENCIAS_MEDICAS           = 'Ciencias Médicas';
    case CIENCIA_POLITICA_DERECHO   = 'Ciencia Política y Derecho';
    case CIENCIAS_ECONOMICAS        = 'Ciencias Económicas';
    case CIENCIAS_SOCIALES          = 'Ciencias Sociales';
    case INGENIERIAS                = 'Ciencias e Ingenierías';
    case INGENIERIAS_AGROPECUARIAS  = 'Ingenierías Agropecuarias';

    public function label(): string
    {
        return $this->value;
    }

    public function puntajeMinimo(): float
    {
        return match($this) {
            self::CIENCIAS_MEDICAS          => 1,
            self::CIENCIA_POLITICA_DERECHO  => 1,
            self::CIENCIAS_ECONOMICAS       => 1,
            self::CIENCIAS_SOCIALES         => 1,
            self::INGENIERIAS               => 1,
            self::INGENIERIAS_AGROPECUARIAS => 1,
        };
    }

    public function puntosCorrectaBloqueI(): float
    {
        return 20.0000; // igual para todas las áreas (Verbal y Matemática)
    }

    public function puntosIncorrecta(): float
    {
        return -1.1250; // igual para todas las áreas
    }

    public function puntosCorrectaBloqueII(AsignaturaExamen $asignatura): float
    {
        return match($this) {

            self::CIENCIAS_MEDICAS => match($asignatura) {
                AsignaturaExamen::ARITMETICA,
                AsignaturaExamen::GEOMETRIA,
                AsignaturaExamen::ALGEBRA,
                AsignaturaExamen::TRIGONOMETRIA   => 14.3014,

                AsignaturaExamen::LENGUAJE,
                AsignaturaExamen::LITERATURA,
                AsignaturaExamen::PSICOLOGIA,
                AsignaturaExamen::EDUCACION_CIVICA,
                AsignaturaExamen::HISTORIA_PERU,
                AsignaturaExamen::GEOGRAFIA,
                AsignaturaExamen::ECONOMIA,
                AsignaturaExamen::FILOSOFIA       => 14.2770,

                AsignaturaExamen::FISICA,
                AsignaturaExamen::QUIMICA,
                AsignaturaExamen::BIOLOGIA        => 25.0000,

                default => 0.0,
            },

            self::CIENCIA_POLITICA_DERECHO,
            self::CIENCIAS_ECONOMICAS,
            self::CIENCIAS_SOCIALES => match($asignatura) {
                AsignaturaExamen::ARITMETICA,
                AsignaturaExamen::GEOMETRIA,
                AsignaturaExamen::ALGEBRA,
                AsignaturaExamen::TRIGONOMETRIA   => 16.0012,

                AsignaturaExamen::LENGUAJE,
                AsignaturaExamen::LITERATURA,
                AsignaturaExamen::PSICOLOGIA,
                AsignaturaExamen::EDUCACION_CIVICA,
                AsignaturaExamen::HISTORIA_PERU,
                AsignaturaExamen::GEOGRAFIA,
                AsignaturaExamen::ECONOMIA,
                AsignaturaExamen::FILOSOFIA       => 23.5290,

                AsignaturaExamen::FISICA,
                AsignaturaExamen::QUIMICA,
                AsignaturaExamen::BIOLOGIA        => 14.5450,

                default => 0.0,
            },

            self::INGENIERIAS,
            self::INGENIERIAS_AGROPECUARIAS => match($asignatura) {
                AsignaturaExamen::ARITMETICA,
                AsignaturaExamen::GEOMETRIA,
                AsignaturaExamen::ALGEBRA,
                AsignaturaExamen::TRIGONOMETRIA   => 22.2220,

                AsignaturaExamen::LENGUAJE,
                AsignaturaExamen::LITERATURA,
                AsignaturaExamen::PSICOLOGIA,
                AsignaturaExamen::EDUCACION_CIVICA,
                AsignaturaExamen::HISTORIA_PERU,
                AsignaturaExamen::GEOGRAFIA,
                AsignaturaExamen::ECONOMIA,
                AsignaturaExamen::FILOSOFIA       => 17.6310,

                AsignaturaExamen::FISICA,
                AsignaturaExamen::QUIMICA         => 22.2220,
                AsignaturaExamen::BIOLOGIA        => 13.0038,

                default => 0.0,
            },
        };
    }
}