<?php

namespace App\Filament\Resources\ClientAnswerResource\Pages;

use App\Filament\Resources\ClientAnswerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Closure;

class ListClientAnswers extends ListRecords
{
    protected static string $resource = ClientAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
    
}
