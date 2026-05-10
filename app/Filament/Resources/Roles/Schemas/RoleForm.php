<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Permission;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleForm
{
    // app/Filament/Resources/Roles/Schemas/RoleForm.php

    public static function configure(Schema $schema): Schema
    {
        $permissionGroups = Permission::query()
            ->select('label_model')
            ->distinct()
            ->orderBy('label_model')
            ->pluck('label_model');

        $dynamicSections = [];
        foreach ($permissionGroups as $group) {
            $dynamicSections[] = Section::make($group)
                ->compact()
                ->columnSpanFull() // <--- CAMBIA 'columnSpan(1)' POR ESTO
                ->schema([
                    CheckboxList::make('permissions_group_' . str($group)->slug('_'))
                        ->label('')
                        ->options(
                            Permission::where('label_model', $group)
                                ->pluck('label', 'id')
                        )
                        ->afterStateHydrated(function ($component, $record) use ($group) {
                            if ($record) {
                                $component->state(
                                    $record->permissions()
                                        ->where('label_model', $group)
                                        ->pluck('id')
                                        ->toArray()
                                );
                            }
                        })
                        ->dehydrated(false)
                        ->bulkToggleable()
                        ->columns(3), // Ahora que es FullSpan, puedes subir esto a 3 o 4 columnas internas
                ]);
        }

        return $schema->components([
            Section::make('Identificación del Rol')
                ->schema([
                    TextInput::make('name')->required()->unique(ignoreRecord: true),
                    TextInput::make('guard_name')->default('web')->disabled()->dehydrated(),
                ])->columns(2)->columnSpanFull(),

            Section::make('Permisos de Acceso')
                ->icon('heroicon-m-lock-open')
                ->schema([
                    Grid::make(2)->schema($dynamicSections)
                ])->columnSpanFull(),
        ]);
    }
}
