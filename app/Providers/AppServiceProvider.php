<?php

namespace App\Providers;

use App\Listeners\CloseLoginHistoryOnLogout;
use App\Listeners\EnforceSingleSession;
use App\Listeners\RecordLoginHistory;
use App\Livewire\CommentItemOverride;
use App\Livewire\CommentsOverride;
use App\Notifications\SendUserMentionedNotificationListener;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Relaticle\Comments\Events\UserMentioned;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, RecordLoginHistory::class);
        Event::listen(Login::class, EnforceSingleSession::class);
        Event::listen(Logout::class, CloseLoginHistoryOnLogout::class);

        // Pisa los componentes del paquete relaticle/comments con nuestras
        // subclases (ver App\Livewire\CommentItemOverride y CommentsOverride).
        // Esto debe ejecutarse DESPUÉS de que CommentsServiceProvider registre
        // los suyos (los providers de paquetes se registran antes que los de
        // la app).
        Livewire::component('comment-item', CommentItemOverride::class);
        Livewire::component('comments', CommentsOverride::class);

        // Igual que arriba: reemplaza el listener que envía la notificación
        // de "te mencionaron" por el nuestro (App\Notifications\
        // SendUserMentionedNotificationListener), que usa App\Notifications\
        // UserMentionedNotification para agregar el botón con el link al
        // comentario. Event::forget() quita el listener del paquete (ya
        // registrado en su packageBooted(), que corre antes que este boot())
        // antes de registrar el nuestro, para no mandar la notificación dos
        // veces.
        Event::forget(UserMentioned::class);
        Event::listen(UserMentioned::class, SendUserMentionedNotificationListener::class);
    }
}
