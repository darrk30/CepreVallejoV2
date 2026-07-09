<?php

namespace App\Filament\Pages;

use App\Models\LoginHistory;
use App\Models\UserSession;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class ActiveSessions extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;
    protected static string|UnitEnum|null $navigationGroup = 'Configuración';
    protected static ?string $navigationLabel = 'Sesiones Activas';
    protected static ?int $navigationSort = 16;
    protected string $view = 'filament.pages.active-sessions';

    public static function canAccess(): bool
    {
        return auth()->user()->can('view_active_sessions');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LoginHistory::query()->with('user.roles')
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->default('(usuario eliminado)'),

                TextColumn::make('user.email')
                    ->label('Correo')
                    ->searchable(),

                TextColumn::make('user.roles.name')
                    ->label('Rol')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'Administrador' => 'danger',
                        'Profesor' => 'warning',
                        'Alumno' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->copyable(),

                TextColumn::make('user_agent')
                    ->label('Dispositivo')
                    ->formatStateUsing(fn(?string $state) => static::parseUserAgent($state))
                    ->wrap(),

                TextColumn::make('logged_in_at')
                    ->label('Fecha y hora de ingreso')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('logged_out_at')
                    ->label('Fecha y hora de cierre')
                    ->dateTime('d/m/Y H:i:s')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->state(fn(LoginHistory $record) => static::isLive($record) ? 'Activa' : 'Cerrada')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'Activa' ? 'success' : 'gray'),
            ])
            ->recordActions([
                Action::make('cerrarSesion')
                    ->label('Cerrar sesión')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('El usuario quedará desconectado en su próxima acción dentro del sistema. El registro de ingreso se conserva en el historial.')
                    ->visible(fn(LoginHistory $record) => static::isLive($record) && $record->user_id !== auth()->id())
                    ->action(function (LoginHistory $record) {
                        DB::table('sessions')->where('user_id', $record->user_id)->delete();

                        $record->update([
                            'logged_out_at' => now(),
                            'logout_reason' => 'cerrado_por_admin',
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Sesión cerrada correctamente')
                            ->send();
                    }),
            ])
            ->defaultSort('logged_in_at', 'desc')
            ->poll('30s');
    }

    protected static function isLive(LoginHistory $record): bool
    {
        return is_null($record->logged_out_at)
            && UserSession::where('user_id', $record->user_id)->exists();
    }

    protected static function parseUserAgent(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'Desconocido';
        }

        $os = match (true) {
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'iPhone'), str_contains($userAgent, 'iPad') => 'iOS',
            str_contains($userAgent, 'Mac OS') => 'macOS',
            str_contains($userAgent, 'Linux') => 'Linux',
            default => 'Otro',
        };

        $browser = match (true) {
            str_contains($userAgent, 'Edg/') => 'Edge',
            str_contains($userAgent, 'Chrome/') => 'Chrome',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Safari/') && !str_contains($userAgent, 'Chrome') => 'Safari',
            default => 'Navegador',
        };

        return "{$browser} · {$os}";
    }

    public function getHeading(): string
    {
        return 'Historial de Sesiones';
    }
}
