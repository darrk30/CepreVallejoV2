<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use Illuminate\Auth\Events\Login;

class RecordLoginHistory
{
    public function handle(Login $event): void
    {
        $userId = $event->user->getAuthIdentifier();

        $openRow = LoginHistory::where('user_id', $userId)
            ->whereNull('logged_out_at')
            ->latest('id')
            ->first();

        // Filament/Livewire puede disparar el evento Login más de una vez
        // para una misma autenticación (p. ej. el POST del formulario y la
        // redirección posterior). Si ya hay un registro abierto de hace
        // instantes, es la misma sesión: no lo duplicamos ni lo cerramos.
        if ($openRow && $openRow->logged_in_at->diffInSeconds(now()) < 5) {
            return;
        }

        // Cierra (sin borrar) el registro abierto de un dispositivo anterior,
        // ya que EnforceSingleSession invalidará esa sesión.
        if ($openRow) {
            $openRow->update([
                'logged_out_at' => now(),
                'logout_reason' => 'otro_dispositivo',
            ]);
        }

        LoginHistory::create([
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'logged_in_at' => now(),
        ]);
    }
}
