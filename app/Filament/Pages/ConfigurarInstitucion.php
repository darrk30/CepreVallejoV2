<?php

namespace App\Filament\Pages;

use App\Models\Institution;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Schemas\Components\Tabs;
use UnitEnum;

class ConfigurarInstitucion extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon =  Heroicon::OutlinedBuildingLibrary;
    // 1. Agrupamos en "Usuarios"
    protected static string | UnitEnum | null $navigationGroup = 'Configuración';

    // 3. Personalizamos las etiquetas para que todo aparezca en español
    protected static ?string $navigationLabel = 'Empresa';

    protected static ?string $pluralModelLabel = 'Empresa';

    protected static ?string $modelLabel = 'Empresa';

    protected static ?string $recordTitleAttribute = 'Empresa';

    protected static ?int $navigationSort = 15;

    protected string $view = 'filament.pages.configurar-institucion'; // <--- EL ERROR ESTÁ AQUÍ

    // Propiedades para los datos
    public ?array $data = [];
    public ?Institution $institution = null;


    public function mount(): void
    {
        $this->institution = Institution::first() ?? new Institution();
        $this->form->fill($this->institution->toArray());
    }

    // public function form(Schema $form): Schema
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        // PESTAÑA 1: IDENTIDAD
                        Tab::make('Identidad Visual')
                            ->icon('heroicon-m-identification')
                            ->columns(2)
                            ->schema([
                                TextInput::make('razon_social')
                                    ->label('Nombre')
                                    ->required()
                                    ->prefixIcon('heroicon-m-building-storefront'),

                                TextInput::make('ruc')
                                    ->label('RUC')
                                    ->numeric()
                                    ->length(11)
                                    ->prefixIcon('heroicon-m-document-text'),

                                Select::make('estado')
                                    ->options([
                                        'Activo' => 'Activo',
                                        'Inactivo' => 'Inactivo',
                                    ])
                                    ->default('Activo')
                                    ->required(),

                                RichEditor::make('nosotros')
                                    ->label('Acerca de Nosotros')
                                    ->textColors([
                                        '#ef4444' => 'Red',
                                        '#10b981' => 'Green',
                                        '#0ea5e9' => 'Sky',
                                    ])
                                    ->toolbarButtons([
                                        ['bold', 'italic', 'underline', 'strike', 'link'],
                                        [ToolbarButtonGroup::make('Paragraph', ['paragraph', 'h1', 'h2', 'h3'])->textualButtons()],
                                        [ToolbarButtonGroup::make('Alignment', ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'])],
                                        ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                        ['undo', 'redo'],
                                    ])
                                    ->columnSpanFull(),

                                FileUpload::make('logo_path')
                                    ->label('Logo de la Institución')
                                    ->image()
                                    ->imageEditor()
                                    ->optimize('webp', 80)
                                    ->maxImageWidth(1200)
                                    ->directory('institucion/logo')
                                    ->columnSpanFull(),
                            ]),

                        // PESTAÑA 2: CONTACTO
                        Tab::make('Contacto')
                            ->icon('heroicon-m-phone')
                            ->columns(2)
                            ->schema([
                                TextInput::make('telefono')
                                    ->label('Teléfono Fijo o Celular')
                                    ->tel()
                                    ->prefixIcon('heroicon-m-phone'),

                                TextInput::make('whatsapp')
                                    ->label('Número de WhatsApp')
                                    ->tel()
                                    ->prefixIcon('heroicon-m-chat-bubble-left-ellipsis'),

                                TextInput::make('correo')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->prefixIcon('heroicon-m-envelope'),

                                TextInput::make('direccion')
                                    ->label('Dirección Física')
                                    ->prefixIcon('heroicon-m-map-pin')
                                    ->columnSpanFull(),
                            ]),

                        // PESTAÑA 3: REDES SOCIALES
                        Tab::make('Redes Sociales')
                            ->icon('heroicon-m-globe-alt')
                            ->columns(2)
                            ->schema([
                                TextInput::make('facebook_url')
                                    ->label('Enlace de Facebook')
                                    ->url()
                                    ->prefix('https://facebook.com/'),

                                TextInput::make('instagram_url')
                                    ->label('Enlace de Instagram')
                                    ->url()
                                    ->prefix('https://instagram.com/'),

                                TextInput::make('tiktok_url')
                                    ->label('Enlace de TikTok')
                                    ->url()
                                    ->prefix('https://tiktok.com/@'),
                            ]),
                    ])->columnSpanFull(),
            ])
            ->statePath('data'); // Guarda todo dentro del array $data
    }

    // Registramos la acción del formulario al estilo nativo de las páginas de Filament
    protected function getFormActions(): array
    {
        return [
            Action::make('guardar')
                ->label('Guardar Configuración')
                ->submit('guardar')
                ->color('primary'),
        ];
    }

    public function guardar(): void
    {
        $data = $this->form->getState();

        if (! $this->institution->exists) {
            $data['user_create_id'] = Auth::id();
            $this->institution = Institution::create($data);
        } else {
            $this->institution->update($data);
        }

        Notification::make()
            ->success()
            ->title('¡Excelente!')
            ->body('Los datos de la institución han sido actualizados correctamente.')
            ->send();
    }
}
