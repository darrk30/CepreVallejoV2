<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Permission;
use App\Support\PanelPermissions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        $profesor = PanelPermissions::profesor();
        $alumno = PanelPermissions::alumno();
        $recursos = PanelPermissions::recursos();

        // Cada permiso aparece en UNA sola tab (así no se desincronizan los
        // checkboxes). Los recursos (videoteca, biblioteca, podcasts) van en
        // la tab Alumno, aunque también se pueden activar para el Profesor.
        $paneles = [
            'administrador' => [
                'label' => 'Administrador',
                'icon' => 'heroicon-o-shield-check',
                'filtro' => fn (Permission $p) => ! in_array($p->name, $profesor)
                    && ! in_array($p->name, $alumno)
                    && ! in_array($p->name, $recursos),
            ],
            'profesor' => [
                'label' => 'Profesor',
                'icon' => 'heroicon-o-academic-cap',
                'filtro' => fn (Permission $p) => in_array($p->name, $profesor)
                    && ! in_array($p->name, $recursos),
            ],
            'alumno' => [
                'label' => 'Alumno',
                'icon' => 'heroicon-o-user',
                'filtro' => fn (Permission $p) => in_array($p->name, $recursos)
                    || (in_array($p->name, $alumno) && ! in_array($p->name, $profesor)),
            ],
        ];

        $todos = Permission::query()->orderBy('label_model')->get();

        $tabs = [];

        foreach ($paneles as $clave => $panel) {
            $permisosDelPanel = $todos->filter($panel['filtro']);

            $secciones = $permisosDelPanel
                ->groupBy('label_model')
                ->map(function ($permisosGrupo, $grupo) use ($clave) {
                    $opciones = $permisosGrupo->pluck('label', 'id')->all();

                    return Section::make($grupo)
                        ->compact()
                        ->columnSpanFull()
                        ->schema([
                            CheckboxList::make('permissions_' . $clave . '_' . str($grupo)->slug('_'))
                                ->label('')
                                ->options($opciones)
                                ->afterStateHydrated(function ($component, $record) use ($opciones) {
                                    if ($record) {
                                        // Solo marcamos los permisos que pertenecen a ESTA lista
                                        $component->state(
                                            array_values(array_intersect(
                                                $record->permissions()->pluck('permissions.id')->all(),
                                                array_keys($opciones)
                                            ))
                                        );
                                    }
                                })
                                ->dehydrated(false)
                                ->bulkToggleable()
                                ->columns(3),
                        ]);
                })
                ->values()
                ->all();

            $tabs[] = Tab::make($panel['label'])
                ->icon($panel['icon'])
                ->schema([
                    Grid::make(2)->schema($secciones),
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
                    Tabs::make('Paneles')->tabs($tabs),
                ])->columnSpanFull(),
        ]);
    }
}
