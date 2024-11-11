<?php

namespace App\Filament\Resources\AllAnswerResource\Pages;

use App\Filament\Resources\AllAnswerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAllAnswer extends EditRecord
{
    protected static string $resource = AllAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
