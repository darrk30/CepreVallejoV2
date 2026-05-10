<?php

namespace App\Providers\Filament;

use App\Filament\Pages\CustomDashboard;
use App\Http\Middleware\CheckUserStatus;
use App\Livewire\AnnouncementsWidget;
use Filament\Enums\UserMenuPosition;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentAsset;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->profile(isSimple: false)
            ->login()
            ->sidebarCollapsibleOnDesktop()
            ->brandLogo(function () {
                return asset('img/logocepre.png');
            })
            ->brandLogoHeight('3.5rem')
            ->favicon('/img/cepreicono.ico')
            ->colors([
                'primary' => Color::Amber,
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
                        margin-bottom: 1rem;
                    }
                </style>
            ')
            ->profile(isSimple: false)
            ->subNavigationPosition(SubNavigationPosition::End)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                CustomDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                AnnouncementsWidget::class
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
            ->errorNotifications(false)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function boot(): void
    {
        FilamentAsset::register([
            Css::make('virtual-classroom-styles', asset('css/virtual-classroom.css')),
            Css::make('virtual-classroom-styles', asset('css/create-exam.css')),
            Css::make('virtual-classroom-styles', asset('css/take-exam.css')),
            Css::make('virtual-classroom-styles', asset('css/exam-results.css')),
            Css::make('virtual-classroom-styles', asset('css/managerCourseContent.css')),
        ], 'alumno');
    }
}
