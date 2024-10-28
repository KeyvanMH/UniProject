<?php

namespace App\Filament\Resources\AdminApprovalResource\Pages;

use App\Filament\Resources\AdminApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminApproval extends EditRecord
{
    protected static string $resource = AdminApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
