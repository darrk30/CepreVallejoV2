<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    // Aquí podrías añadir lógica personalizada en el futuro
    // Ej: Determinar si un rol es "protegido" para que no lo borren
}
