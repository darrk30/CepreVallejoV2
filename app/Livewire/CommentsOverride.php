<?php

namespace App\Livewire;

use Relaticle\Comments\Livewire\Comments;

/**
 * El paquete relaticle/comments arranca con sortDirection = 'asc' (más
 * antiguos primero). Se pidió que por defecto se vea al revés: más
 * recientes primero.
 */
class CommentsOverride extends Comments
{
    public string $sortDirection = 'desc';
}
