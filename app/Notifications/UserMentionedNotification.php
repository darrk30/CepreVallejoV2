<?php

namespace App\Notifications;

use App\Filament\Alumno\Pages\ReproductorVideo as AlumnoReproductorVideo;
use App\Filament\Profesor\Pages\ReproductorVideo as ProfesorReproductorVideo;
use App\Models\Video;
use Filament\Actions\Action as NotificationAction;
use Filament\Notifications\Notification as FilamentNotification;
use Relaticle\Comments\Notifications\UserMentionedNotification as BaseUserMentionedNotification;

/**
 * Igual que la notificación del paquete relaticle/comments, pero agrega un
 * botón "Ver comentario" que lleva directo al video, al panel correcto
 * (alumno o profesor) según quién recibe la notificación. El enlace lleva
 * ?comentario={id} en la URL: el <script> de reproductor-video.blade.php lo
 * lee al cargar la página para hacer scroll y desplegar las respuestas
 * ocultas hasta llegar al comentario exacto.
 */
class UserMentionedNotification extends BaseUserMentionedNotification
{
    public function toDatabase(mixed $notifiable): array
    {
        $mentionerName = $this->mentionedBy->getCommentDisplayName();

        $notification = FilamentNotification::make()
            ->title(__('comments::comments.notifications.mention_body', ['name' => $mentionerName]))
            ->body($this->bodyExcerpt(100))
            ->icon('heroicon-o-at-symbol');

        if ($url = $this->commentUrl($notifiable)) {
            $notification->actions([
                NotificationAction::make('ver')
                    ->label('Ver comentario')
                    ->url($url),
            ]);
        }

        return array_merge(
            $notification->getDatabaseMessage(),
            [
                'comment_id' => $this->comment->id,
                'commentable_type' => $this->comment->commentable_type,
                'commentable_id' => $this->comment->commentable_id,
                'mentioner_name' => $mentionerName,
            ],
        );
    }

    protected function commentUrl(mixed $notifiable): ?string
    {
        $video = $this->comment->commentable;

        if (! $video instanceof Video) {
            return null;
        }

        $page = match (true) {
            $notifiable->can('access_teacher_panel') => ProfesorReproductorVideo::class,
            $notifiable->can('access_student_panel') => AlumnoReproductorVideo::class,
            default => null,
        };

        if (! $page) {
            return null;
        }

        return $page::getUrl(['videoSlug' => $video->slug]).'?comentario='.$this->comment->id;
    }
}
