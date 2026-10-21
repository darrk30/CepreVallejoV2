<?php

namespace App\Livewire;

use Relaticle\Comments\Livewire\CommentItem;

/**
 * Arregla un bug del paquete relaticle/comments: la propiedad pública
 * `public Comment $comment` se hidrata SIN relaciones cargadas en cada
 * petición (Livewire solo serializa la clase + el id del modelo, nunca sus
 * relaciones — ver Livewire\Features\SupportModels\ModelSynth). Eso significa
 * que, al llamar a CUALQUIER acción de este componente (responder, cancelar
 * respuesta, editar, cancelar edición, guardar, etc.), `$this->comment`
 * empieza SIN la relación "replies" — y a menos que esa acción la vuelva a
 * cargar explícitamente, el bloque de "N respuestas" desaparece de la vista
 * hasta la próxima recarga completa de la página.
 *
 * Solo addReply() del paquete original hacía ese reload. Por eso el bug no
 * aparecía al responder (ese caso sí recargaba), pero sí al cancelar una
 * respuesta a un comentario ajeno, o al editar/cancelar/guardar — cualquier
 * otra acción.
 *
 * En vez de parchar cada método uno por uno, usamos el hook hydrate() de
 * Livewire: corre automáticamente ANTES de cualquier acción, en cada
 * petición, así que cubre todos los casos de una sola vez.
 *
 * Registrado en AppServiceProvider::boot() por encima del Livewire::component
 * del propio paquete, para no tocar el código de vendor/.
 */
class CommentItemOverride extends CommentItem
{
    public function hydrate(): void
    {
        $this->comment->loadMissing([
            'replies.commenter',
            'replies.mentions',
            'replies.attachments',
            'replies.reactions.commenter',
            'replies.replies.commenter',
            'replies.replies.mentions',
            'replies.replies.attachments',
            'replies.replies.reactions.commenter',
        ]);
    }
}
