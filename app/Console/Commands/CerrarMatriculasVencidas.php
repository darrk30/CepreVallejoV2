<?php

namespace App\Console\Commands;

use App\Enums\EstadoMatricula;
use App\Models\AcademicCycle;
use App\Models\Inscription;
use Illuminate\Console\Command;

/**
 * Cuando un ciclo académico llega a su fecha_fin:
 *   1. Todas sus matrículas (Inscription) activas pasan a "inactiva".
 *   2. El propio ciclo se desactiva (estado = false), para que deje de
 *      aparecer como vigente en el sitio público, la matrícula y el aula
 *      virtual (ver app/Filament/Pages/CoursesOverview.php y demás lugares
 *      que ya filtran por AcademicCycle::estado).
 *
 * Es idempotente: correrlo varias veces sobre el mismo ciclo no hace nada
 * la segunda vez (ya no encuentra matrículas "activa" que cambiar).
 *
 * Uso manual (antes de programarlo):
 *   php artisan matriculas:cerrar-vencidas
 *   php artisan matriculas:cerrar-vencidas --dry-run   (solo muestra qué haría)
 */
class CerrarMatriculasVencidas extends Command
{
    protected $signature = 'matriculas:cerrar-vencidas {--dry-run : Solo muestra qué se actualizaría, sin tocar la BD}';

    protected $description = 'Inactiva las matrículas y desactiva los ciclos académicos cuya fecha_fin ya pasó';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $ciclosVencidos = AcademicCycle::where('estado', true)
            ->whereDate('fecha_fin', '<', now())
            ->get();

        if ($ciclosVencidos->isEmpty()) {
            $this->info('No hay ciclos vencidos pendientes de cerrar.');

            return self::SUCCESS;
        }

        $totalMatriculas = 0;

        foreach ($ciclosVencidos as $ciclo) {
            $matriculasActivas = Inscription::where('academic_cycle_id', $ciclo->id)
                ->where('estado_matricula', EstadoMatricula::ACTIVA->value);

            $cantidad = $matriculasActivas->count();

            $this->line(sprintf(
                'Ciclo "%s" (fin: %s): %d matrícula(s) activa(s)%s.',
                $ciclo->nombre,
                $ciclo->fecha_fin->format('d/m/Y'),
                $cantidad,
                $dryRun ? ' [dry-run, no se modifica nada]' : ''
            ));

            if (! $dryRun && $cantidad > 0) {
                $matriculasActivas->update(['estado_matricula' => EstadoMatricula::INACTIVA->value]);
            }

            if (! $dryRun) {
                $ciclo->update(['estado' => false]);
            }

            $totalMatriculas += $cantidad;
        }

        $this->info("Total: {$totalMatriculas} matrícula(s) " . ($dryRun ? 'a inactivar' : 'inactivadas') . ' en ' . $ciclosVencidos->count() . ' ciclo(s).');

        return self::SUCCESS;
    }
}
