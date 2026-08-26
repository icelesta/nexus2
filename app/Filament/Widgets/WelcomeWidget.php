<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    |
    | Custom dashboard welcome / hero widget.
    |
    */

    protected string $view =
        'filament.widgets.welcome-widget';


    /*
    |--------------------------------------------------------------------------
    | LAYOUT
    |--------------------------------------------------------------------------
    |
    | Allow the widget to occupy the full dashboard content width.
    |
    | The internal Blade view will then control the split between:
    | - Greeting Card
    | - BMS Hero Image
    |
    */

    protected int | string | array $columnSpan = 'full';


    /*
    |--------------------------------------------------------------------------
    | SORT ORDER
    |--------------------------------------------------------------------------
    |
    | Welcome / Hero section appears first on dashboard.
    |
    */

    protected static ?int $sort = 1;
}