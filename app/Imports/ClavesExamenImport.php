<?php

namespace App\Imports;

use App\Enums\AsignaturaExamen;
use App\Models\RespuestasCorrecta;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClavesExamenImport implements ToModel, WithHeadingRow
{
    protected $examenId;

    public function __construct($examenId)
    {
        $this->examenId = $examenId;
    }

    public function model(array $row)
    {
        // DEBUG — ver qué llega
        \Log::info('Fila Excel:', $row);

        if (!isset($row['pregunta']) || !isset($row['clave']) || !isset($row['asignatura'])) {
            \Log::warning('Fila ignorada — faltan columnas. Keys disponibles: ' . implode(', ', array_keys($row)));
            return null;
        }

        $asignaturaRaw = strtolower(trim($row['asignatura']));
        $asignatura    = AsignaturaExamen::tryFrom($asignaturaRaw);

        if (!$asignatura) {
            \Log::warning("Asignatura no reconocida: '{$asignaturaRaw}'");
            return null;
        }

        return new RespuestasCorrecta([
            'examen_ordinario_id' => $this->examenId,
            'numero_pregunta'     => (int) $row['pregunta'],
            'opcion_correcta'     => strtoupper(trim($row['clave'])),
            'asignatura'          => $asignatura->value,
            'bloque'              => $asignatura->bloque()->value,
        ]);
    }
}
