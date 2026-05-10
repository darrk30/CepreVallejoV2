<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function afterCreate(): void
    {
        $data = $this->form->getRawState();

        $permissionIds = collect($data)
            ->filter(fn($value, $key) => str_starts_with($key, 'permissions_group_'))
            ->flatten()
            ->filter()
            ->values()
            ->toArray();

        $this->record->syncPermissions($permissionIds);
    }
}
