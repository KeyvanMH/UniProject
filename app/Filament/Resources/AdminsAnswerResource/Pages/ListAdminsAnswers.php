<?php

namespace App\Filament\Resources\AdminsAnswerResource\Pages;

use App\Filament\Resources\AdminsAnswerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminsAnswers extends ListRecords
{
    protected static string $resource = AdminsAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
