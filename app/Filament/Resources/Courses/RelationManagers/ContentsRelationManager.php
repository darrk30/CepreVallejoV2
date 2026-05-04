<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Filament\Resources\Courses\CourseResource;
use App\Models\CourseContent;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class ContentsRelationManager extends RelationManager
{
    protected static string $relationship = 'contents';

    // protected static ?string $relatedResource = CourseResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('titulo')
                    ->label('Título del Tema')
                    ->required()
                    ->columnSpanFull()
                    ->extraInputAttributes(['required' => false]),

                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(3)
                    ->columnSpanFull(),

                Select::make('estado')
                    ->options([
                        'Activo' => 'Activo',
                        'Inactivo' => 'Inactivo',
                    ])
                    ->default('Activo'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titulo')
            ->defaultSort('orden', 'asc') // <--- ESTO ORDENA LA VISTA POR DEFECTO
            ->columns([
                TextColumn::make('titulo')
                    ->label('Tema')
                    ->searchable(),
                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50) // Solo muestra los primeros 50 caracteres
                    ->tooltip(fn(CourseContent $record): string => $record->descripcion ?? '') // Muestra el texto completo al pasar el mouse
                    ->color('gray')
                    ->wrap(),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'Activo' ? 'success' : 'gray'),
            ])
            ->filters([
                // Filtros adicionales
            ])
            ->headerActions([
                CreateAction::make()->label('Nuevo Tema'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->reorderable('orden'); // Permite arrastrar y soltar para cambiar el orden
    }
}
