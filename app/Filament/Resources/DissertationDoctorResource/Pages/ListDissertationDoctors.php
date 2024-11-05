<?php

namespace App\Filament\Resources\DissertationDoctorResource\Pages;

use App\Filament\Resources\DissertationDoctorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDissertationDoctors extends ListRecords
{
    protected static string $resource = DissertationDoctorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
