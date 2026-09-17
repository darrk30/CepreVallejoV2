<?php

use App\Models\TareaProgramada;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Registra dinámicamente las tareas configuradas desde el panel admin
// (Configuración > Tareas Programadas) en vez de tenerlas hardcodeadas
// aquí. El Schema::hasTable evita romper comandos artisan que corren
// ANTES de que exista la tabla (ej. la primera vez que se hace `migrate`).
//
// Requiere que algo del sistema operativo dispare `php artisan schedule:run`
// cada minuto para que esto realmente se ejecute solo (en Windows, una
// Tarea Programada; en Linux, un cron). Sin eso, esta configuración queda
// guardada pero inactiva — igual que antes de tener esta tabla.
if (Schema::hasTable('tareas_programadas')) {
    foreach (TareaProgramada::where('activo', true)->get() as $tarea) {
        // Por seguridad, solo se registran comandos de la whitelist, aunque
        // el registro en BD tenga otro valor (manipulado a mano, por ejemplo).
        if (! $tarea->comandoEsValido()) {
            continue;
        }

        $logFile = storage_path("logs/tareas-programadas/tarea-{$tarea->id}.log");

        $event = Schedule::command($tarea->comando)->sendOutputTo($logFile);
        $tarea->aplicarFrecuencia($event);

        $registrarResultado = function (string $resultado) use ($tarea, $logFile) {
            $tarea->update([
                'ultima_ejecucion_at' => now(),
                'ultimo_resultado' => $resultado,
                'ultima_salida' => is_file($logFile) ? file_get_contents($logFile) : null,
            ]);
        };

        $event->onSuccess(fn () => $registrarResultado(\App\Enums\ResultadoTarea::EXITO->value));
        $event->onFailure(fn () => $registrarResultado(\App\Enums\ResultadoTarea::ERROR->value));
    }
}
