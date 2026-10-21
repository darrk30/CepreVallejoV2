<?php

namespace App\Models;

use App\Enums\FrecuenciaTarea;
use App\Enums\ResultadoTarea;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Database\Eloquent\Model;

class TareaProgramada extends Model
{
    // Eloquent adivinaría "tarea_programadas" (solo pluraliza la última
    // palabra); la tabla real es "tareas_programadas".
    protected $table = 'tareas_programadas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'comando',
        'frecuencia',
        'hora',
        'dia_semana',
        'dia_mes',
        'expresion_cron',
        'activo',
        'ultima_ejecucion_at',
        'ultimo_resultado',
        'ultima_salida',
    ];

    protected function casts(): array
    {
        return [
            'frecuencia' => FrecuenciaTarea::class,
            'ultimo_resultado' => ResultadoTarea::class,
            'activo' => 'boolean',
            'hora' => 'datetime:H:i',
            'ultima_ejecucion_at' => 'datetime',
        ];
    }

    /**
     * Whitelist de comandos artisan que se pueden programar desde este panel.
     * routes/console.php solo registra tareas cuyo `comando` esté aquí: así
     * un registro manipulado directo en la BD no puede disparar un comando
     * arbitrario (migrate:fresh, db:seed, etc.).
     *
     * @return array<string, string> comando => etiqueta legible
     */
    public static function comandosDisponibles(): array
    {
        return [
            'matriculas:cerrar-vencidas' => 'Cerrar matrículas vencidas (ciclos con fecha fin pasada)',
        ];
    }

    public function comandoEsValido(): bool
    {
        return array_key_exists($this->comando, self::comandosDisponibles());
    }

    /**
     * Aplica la frecuencia configurada sobre el Event del scheduler de Laravel.
     */
    public function aplicarFrecuencia(Event $event): Event
    {
        $hora = $this->hora?->format('H:i') ?? '00:00';

        return match ($this->frecuencia) {
            FrecuenciaTarea::CADA_HORA => $event->hourly(),
            FrecuenciaTarea::SEMANAL => $event->weeklyOn($this->dia_semana ?? 1, $hora),
            FrecuenciaTarea::MENSUAL => $event->monthlyOn($this->dia_mes ?? 1, $hora),
            FrecuenciaTarea::PERSONALIZADA => $event->cron($this->expresion_cron ?? '* * * * *'),
            default => $event->dailyAt($hora), // DIARIA
        };
    }

    /**
     * Descripción legible de la frecuencia, para mostrar en la tabla.
     */
    public function descripcionFrecuencia(): string
    {
        $hora = $this->hora?->format('H:i');

        return match ($this->frecuencia) {
            FrecuenciaTarea::CADA_HORA => 'Cada hora',
            FrecuenciaTarea::SEMANAL => 'Semanal (' . self::nombreDiaSemana($this->dia_semana) . ") a las {$hora}",
            FrecuenciaTarea::MENSUAL => "Mensual (día {$this->dia_mes}) a las {$hora}",
            FrecuenciaTarea::PERSONALIZADA => "Cron: {$this->expresion_cron}",
            default => "Diaria a las {$hora}",
        };
    }

    public static function nombreDiaSemana(?int $dia): string
    {
        return match ($dia) {
            0 => 'domingo',
            1 => 'lunes',
            2 => 'martes',
            3 => 'miércoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sábado',
            default => '—',
        };
    }
}
