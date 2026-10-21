<?php

namespace App\Filament\Alumno\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action as NotificationAction;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;

/**
 * Igual que el Login base de Filament, salvo por un caso: cuando el usuario
 * y contraseña SÍ son correctos pero es un alumno sin matrícula activa
 * (User::canAccessPanel bloquea el login en ese caso, sin crear sesión), en
 * vez del mensaje genérico "estas credenciales no coinciden" le mostramos
 * una notificación explicando el motivo real, con un botón a WhatsApp.
 */
class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        /** @var SessionGuard $authGuard */
        $authGuard = Filament::auth();

        $authProvider = $authGuard->getProvider(); /** @phpstan-ignore-line */
        $credentials = $this->getCredentialsFromFormData($data);

        $user = $authProvider->retrieveByCredentials($credentials);

        if ((! $user) || (! $authProvider->validateCredentials($user, $credentials))) {
            $this->fireFailedEvent($authGuard, $user, $credentials);
            $this->throwFailureValidationException();
        }

        // Credenciales correctas. Si el único motivo de bloqueo es no tener
        // matrícula activa, se lo explicamos en vez del error genérico.
        if ($user->can('access_student_panel') && ! $user->tieneMatriculaActiva()) {
            $this->fireFailedEvent($authGuard, $user, $credentials);
            $this->getSinMatriculaNotification()->send();

            return null;
        }

        if (! $authGuard->attemptWhen($credentials, function (Authenticatable $user): bool {
            if (! ($user instanceof FilamentUser)) {
                return true;
            }

            return $user->canAccessPanel(Filament::getCurrentOrDefaultPanel());
        }, $data['remember'] ?? false)) {
            $this->fireFailedEvent($authGuard, $user, $credentials);
            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function getSinMatriculaNotification(): Notification
    {
        $whatsapp = Cache::remember('institution_whatsapp', now()->addDay(), function () {
            return \App\Models\Institution::first()?->whatsapp ?? '51987654321';
        });

        $cleanPhone = preg_replace('/[^0-9]/', '', $whatsapp);
        $whatsappUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode('Hola, quiero matricularme en Cepre Vallejo.');

        return Notification::make()
            ->title('No tienes una matrícula activa')
            ->body('Para acceder al aula virtual necesitas estar matriculado en Cepre Vallejo. Si ya pagaste, contáctanos para verificarlo.')
            ->warning()
            ->persistent()
            ->actions([
                NotificationAction::make('whatsapp')
                    ->label('Escríbenos por WhatsApp')
                    ->url($whatsappUrl)
                    ->openUrlInNewTab()
                    ->color('success'),
            ]);
    }
}
