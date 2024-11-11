<?php

namespace App\Filament\Resources\AllAnswerResource\Pages;

use App\Filament\Resources\AllAnswerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAllAnswers extends ListRecords
{
    protected static string $resource = AllAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
