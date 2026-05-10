<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $data = $this->form->getRawState();

        $permissionIds = collect($data)
            ->filter(fn($value, $key) => str_starts_with($key, 'permissions_group_'))
            ->flatten()
            ->filter()
            // Convertimos cada ID de string a entero para que Spatie lo reconozca como ID
            ->map(fn($id) => (int) $id)
            ->values()
            ->toArray();

        $this->record->syncPermissions($permissionIds);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
