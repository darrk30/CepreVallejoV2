<?php

namespace App\Providers\Filament;

use App\Http\Middleware\CheckUserStatus;
use App\Livewire\AnnouncementsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ProfesorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('profesor')
            ->path('profesor')
            ->profile(isSimple: false)
            ->login()
            ->sidebarCollapsibleOnDesktop()
            ->brandLogo(function () {
                return asset('img/logocepre.png');
            })
            ->databaseNotifications()
            ->databaseNotificationsPolling('60s')
            ->favicon('/img/cepreicono.ico')
            ->brandLogoHeight('3.5rem')
            ->colors([
                'primary' => '#46449e',
            ])
            ->discoverResources(in: app_path('Filament/Profesor/Resources'), for: 'App\Filament\Profesor\Resources')
            ->discoverPages(in: app_path('Filament/Profesor/Pages'), for: 'App\Filament\Profesor\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->renderHook('panels::body.start', fn() => '
                <style>
                    /* Aplicamos la sombra al contenedor principal de la barra lateral */
                    .fi-sidebar {
                        box-shadow: 4px 0 12px -4px rgba(0, 0, 0, 0.1);
                        border-inline-end: 1px solid rgba(var(--gray-200), 0.5);
                    }

                    /* En modo oscuro, ajustamos la intensidad para que se note el relieve */
                    .dark .fi-sidebar {
                        box-shadow: 4px 0 15px -5px rgba(0, 0, 0, 0.6);
                        border-inline-end: 1px solid rgba(var(--gray-800), 0.3);
                    }

                    /* Tu configuración de breadcrumbs */
                    .fi-breadcrumbs {
                        display: block !important;
                    }
                </style>
            ')
            ->discoverWidgets(in: app_path('Filament/Profesor/Widgets'), for: 'App\Filament\Profesor\Widgets')
            ->widgets([
                AnnouncementsWidget::class
                // AccountWidget::class,
                // FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                CheckUserStatus::class,
            ])
            ->spa()
            ->databaseTransactions()
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
