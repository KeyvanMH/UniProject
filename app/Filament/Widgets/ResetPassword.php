<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Hash;

class ResetPassword extends Widget
{
    protected static string $view = 'filament.widgets.reset-password';
    protected int | string | array $columnSpan = 1;

//    protected static bool $isDiscovered = false;
    public static function canView(): bool
    {
        return auth()->check() && Hash::check(auth()->user()->national_code,auth()->user()->password) && auth()->user()->role == 'user';
    }
}
