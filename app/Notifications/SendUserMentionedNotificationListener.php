<?php

namespace App\Notifications;

use Relaticle\Comments\CommentsConfig;
use Relaticle\Comments\Events\UserMentioned;
use Relaticle\Comments\Models\Subscription;

/**
 * Reemplaza al listener del paquete relaticle/comments (ver
 * AppServiceProvider::boot(), que hace Event::forget() + Event::listen()
 * con este) solo para usar nuestra propia notificación
 * (App\Notifications\UserMentionedNotification), que agrega el botón "Ver
 * comentario" con el link directo al video. El resto de la lógica
 * (auto-suscripción, no notificarse a uno mismo) es igual a
 * Relaticle\Comments\Listeners\SendUserMentionedNotification.
 *
 * IMPORTANTE: esta clase vive fuera de app/Listeners a propósito. Laravel
 * auto-descubre cualquier clase con un método handle(Evento $e) dentro de
 * app/Listeners y la registra SOLA, sin pedírselo — si esta clase viviera
 * ahí, quedaría registrada DOS veces (la automática + la que agregamos a
 * mano más abajo) y cada mención mandaría la notificación duplicada (fue
 * justo el bug reportado: la notificación aparecía dos veces).
 */
class SendUserMentionedNotificationListener
{
    public function handle(UserMentioned $event): void
    {
        if (! CommentsConfig::areNotificationsEnabled()) {
            return;
        }

        $comment = $event->comment;
        $mentionedUser = $event->mentionedUser;

        if (CommentsConfig::shouldAutoSubscribe()) {
            Subscription::subscribe($comment->commentable, $mentionedUser);
        }

        $isSelf = $mentionedUser->getMorphClass() === $comment->commenter->getMorphClass()
            && $mentionedUser->getKey() === $comment->commenter->getKey();

        if ($isSelf) {
            return;
        }

        $mentionedUser->notify(new UserMentionedNotification($comment, $comment->commenter));
    }
}
