<?php

namespace App\Providers\Filament;

use App\Filament\Profesor\Pages\ManageCourseContent;
use App\Filament\Profesor\Pages\TakeExam;
use App\Livewire\AnnouncementsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentAsset;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AlumnoPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('alumno')
            ->path('alumno')
            ->login()
            ->sidebarCollapsibleOnDesktop()
            ->brandLogo(function () {
                return asset('img/logocepre.png');
            })
            ->favicon('/img/cepreicono.ico')
            ->profile(isSimple: false)
            ->brandLogoHeight('3.5rem')
            ->colors([
                'primary' => '#46449e',
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
            ->discoverResources(in: app_path('Filament/Alumno/Resources'), for: 'App\Filament\Alumno\Resources')
            ->discoverPages(in: app_path('Filament/Alumno/Pages'), for: 'App\Filament\Alumno\Pages')
            ->pages([
                Dashboard::class,
                ManageCourseContent::class,
                TakeExam::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Alumno/Widgets'), for: 'App\Filament\Alumno\Widgets')
            ->widgets([
                AnnouncementsWidget::class,
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
            ])
            ->databaseTransactions()
            ->spa()
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function boot(): void
    {
        FilamentAsset::register([
            Css::make('virtual-classroom-styles', asset('css/virtual-classroom.css')),
            Css::make('virtual-classroom-styles', asset('css/biblioteca.css')),
            Css::make('virtual-classroom-styles', asset('css/video.css')),
            Css::make('virtual-classroom-styles', asset('css/announcements-widget.css')),
            Css::make('virtual-classroom-styles', asset('css/reproductor-video.css')),
            Css::make('virtual-classroom-styles', asset('css/take-exam.css')),
        ], 'alumno'); // 'alumno' debe ser el mismo ID que definiste en ->id('alumno')
    }
}
