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
use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentAsset;
use Filament\View\PanelsRenderHook;
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
            ->viteTheme('resources/css/filament/alumno/theme.css')
            ->colors([
                'primary' => '#46449e',
            ])
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn(): string => view('components.global-audio-player')->render(),
            )
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
        // Solo se registran aquí los assets realmente globales (usados en TODAS las
        // páginas del panel, como el reproductor de audio persistente). El resto de
        // CSS/JS específico de cada página se carga con @push('styles') en su propia
        // vista para no descargar estilos que esa página no necesita.
        //
        // Nota: managerCourseContent.css / take-exam.css / exam-results.css (usados
        // por "Mi Aula", exámenes y resultados, compartidos con el panel Profesor)
        // NO se registran aquí también: FilamentAsset::register() es global — no se
        // limita al panel donde se declara — así que ya quedan disponibles para
        // este panel con solo registrarlos una vez desde ProfesorPanelProvider.
        // Registrarlos en los dos providers los duplicaba (dos <link> idénticos).
        FilamentAsset::register([
            Css::make('play-podcast-styles', asset('css/play-podcast.css')),
            Js::make('podcast-player-script', asset('js/podcast-player.js')),
        ], 'alumno'); // 'alumno' debe ser el mismo ID que definiste en ->id('alumno')
    }
}
