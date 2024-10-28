<?php

namespace App\Filament\Pages;

use App\Http\Middleware\AdminMiddleware;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class AdminAnswer extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.admin-answer';
    protected static string | array $routeMiddleware = [
        AdminMiddleware::class,
    ];
    public static function canAccess(): bool {
        return auth()->user()->role == 'admin';
    }

    public static function canView(Model $record): bool {
        return auth()->user()->role == 'admin';
    }

}
