<?php

namespace App\Traits;

use App\Models\Favorito;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait Favoritable
{
    public function favoritos(): MorphMany
    {
        return $this->morphMany(Favorito::class, 'favoritable');
    }

    public function isFavoritedBy(?Model $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->favoritos()
            ->where('user_id', $user->id)
            ->exists();
    }
}