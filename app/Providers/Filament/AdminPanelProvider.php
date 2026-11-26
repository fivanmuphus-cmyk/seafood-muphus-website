<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('kelola-muphus')
            ->login()
            ->profile()
            ->brandName('Seafood Muphus')
            ->brandLogo(fn () => file_exists(public_path('images/logo.webp')) ? asset('images/logo.webp') : null)
            ->brandLogoHeight('2.25rem')
            ->favicon(asset('favicon.svg'))
            ->colors([
                'primary' => Color::Green,
                'gray' => Color::Slate,
            ])
            ->font('Inter')
            ->darkMode(false)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('15rem')
            ->maxContentWidth('7xl')
            ->unsavedChangesAlerts()
            ->navigationGroups([
                NavigationGroup::make('Konten'),
                NavigationGroup::make('Pesan & Karir'),
                NavigationGroup::make('Pengaturan'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => view('filament.admin-style'))
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                // Nama kelas CSRF berbeda antar versi Laravel; pilih yang tersedia.
                class_exists(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class)
                    ? \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class
                    : \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class]);
    }
}