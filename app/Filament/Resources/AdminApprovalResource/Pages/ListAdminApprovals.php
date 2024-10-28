<?php

namespace App\Filament\Resources\AdminApprovalResource\Pages;

use App\Filament\Resources\AdminApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Closure;

class ListAdminApprovals extends ListRecords
{
    protected static string $resource = AdminApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
