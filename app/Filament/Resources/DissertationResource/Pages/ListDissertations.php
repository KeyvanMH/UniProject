<?php

namespace App\Filament\Resources\DissertationResource\Pages;

use App\Filament\Resources\DissertationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDissertations extends ListRecords
{
    protected static string $resource = DissertationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
