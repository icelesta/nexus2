<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\ChangePassword;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\Login;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use App\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use App\Filament\Resources\DirectMarkets\DirectMarketResource;

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel

            /*
            |--------------------------------------------------------------------------
            | PANEL
            |--------------------------------------------------------------------------
            */

            ->default()
            ->id('admin')
            ->path('admin')

            ->navigationGroups([
                'Purchasing',
                'Logistics',
                'Setup Master',
            ])            
            
            ->navigationItems([
                NavigationItem::make('Material Requisition')
                    ->label('Material Requisition')
                    ->icon('heroicon-o-document-text')
                    ->group('Purchasing')
                    ->sort(2)
                    ->url(fn (): string => PurchaseRequisitionResource::getUrl()),

                NavigationItem::make('Direct Market')
                    ->label('Direct Market')
                    ->icon('heroicon-o-shopping-cart')
                    ->group('Purchasing')
                    ->sort(3)
                    ->url(fn (): string => DirectMarketResource::getUrl()),
            ])




            /*
            |--------------------------------------------------------------------------
            | THEME
            |--------------------------------------------------------------------------
            */

            ->viteTheme(
                'resources/css/filament/admin/theme.css'
            )


            /*
            |--------------------------------------------------------------------------
            | AUTHENTICATION
            |--------------------------------------------------------------------------
            */

            ->login(Login::class)


            /*
            |--------------------------------------------------------------------------
            | USER PROFILE
            |--------------------------------------------------------------------------
            */

            ->profile(
                EditProfile::class
            )


            /*
            |--------------------------------------------------------------------------
            | LIGHT / DARK MODE
            |--------------------------------------------------------------------------
            |
            | Filament provides the native appearance switcher
            | inside the user menu.
            |
            */

            ->darkMode()


            /*
            |--------------------------------------------------------------------------
            | SIDEBAR
            |--------------------------------------------------------------------------
            */

            ->sidebarCollapsibleOnDesktop()


            /*
            |--------------------------------------------------------------------------
            | BRANDING
            |--------------------------------------------------------------------------
            */

            ->brandLogo(
                asset('images/nexus_1.png')
            )

            ->brandLogoHeight('52px')

            ->brandName('')


            /*
            |--------------------------------------------------------------------------
            | TOPBAR - NOTIFICATION BELL
            |--------------------------------------------------------------------------
            */

            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn (): string => view(
                    'filament.components.notification-bell'
                )->render(),
            )


            /*
            |--------------------------------------------------------------------------
            | TOPBAR - USER NAME
            |--------------------------------------------------------------------------
            */

            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                fn (): string => view(
                    'filament.components.topbar-user-name'
                )->render(),
            )


            /*
            |--------------------------------------------------------------------------
            | PRIMARY COLOR
            |--------------------------------------------------------------------------
            */

            ->colors([
                'primary' => Color::Amber,
            ])


            /*
            |--------------------------------------------------------------------------
            | RESOURCE DISCOVERY
            |--------------------------------------------------------------------------
            */

            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources',
            )


            /*
            |--------------------------------------------------------------------------
            | PAGE DISCOVERY
            |--------------------------------------------------------------------------
            */

            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages',
            )


            /*
            |--------------------------------------------------------------------------
            | EXPLICIT PANEL PAGES
            |--------------------------------------------------------------------------
            |
            | Keep only the existing explicitly configured pages here.
            |
            */

            ->pages([
                \App\Filament\Pages\Dashboard::class,
                \App\Filament\Pages\RolePermissionMatrix::class,
            ])


            /*
            |--------------------------------------------------------------------------
            | WIDGET DISCOVERY
            |--------------------------------------------------------------------------
            */

            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets',
            )


            /*
            |--------------------------------------------------------------------------
            | MIDDLEWARE
            |--------------------------------------------------------------------------
            */

            ->middleware([

                EncryptCookies::class,

                AddQueuedCookiesToResponse::class,

                StartSession::class,

                AuthenticateSession::class,

                ShareErrorsFromSession::class,

                VerifyCsrfToken::class,

                SubstituteBindings::class,

                DisableBladeIconComponents::class,

                DispatchServingFilamentEvent::class,

            ])


            /*
            |--------------------------------------------------------------------------
            | FILAMENT SHIELD
            |--------------------------------------------------------------------------
            */

            ->plugins([

                FilamentShieldPlugin::make(),

            ])


            /*
            |--------------------------------------------------------------------------
            | AUTH MIDDLEWARE
            |--------------------------------------------------------------------------
            */

            ->authMiddleware([
                Authenticate::class,
            ])


            /*
            |--------------------------------------------------------------------------
            | USER MENU
            |--------------------------------------------------------------------------
            |
            | Nexus ERP 2.0 User Menu
            |
            |   User Header
            |   Light / Dark
            |   My Profile
            |   Change Password
            |   Sign Out
            |
            */

            ->userMenuItems([

                /*
                |--------------------------------------------------------------------------
                | MY PROFILE
                |--------------------------------------------------------------------------
                */

                'profile' => MenuItem::make()
                    ->label('My Profile')
                    ->icon('heroicon-o-user')
                    ->url(
                        fn (): string =>
                            EditProfile::getUrl()
                    ),


                /*
                |--------------------------------------------------------------------------
                | CHANGE PASSWORD
                |--------------------------------------------------------------------------
                */

                'change-password' => MenuItem::make()
                    ->label('Change Password')
                    ->icon('heroicon-o-lock-closed')
                    ->url(
                        fn (): string =>
                            ChangePassword::getUrl()
                    ),

            ]);



    }
}