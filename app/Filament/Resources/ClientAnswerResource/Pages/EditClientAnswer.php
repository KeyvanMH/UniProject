<?php

namespace App\Filament\Resources\ClientAnswerResource\Pages;

use App\Filament\Resources\ClientAnswerResource;
use App\Models\Answer;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Panel;
use Filament\Resources\Pages\EditRecord;

class EditClientAnswer extends EditRecord {
    protected static string $resource = ClientAnswerResource::class;

    /**
     * @return string|null
     */
    public function getHeading(): \Illuminate\Contracts\Support\Htmlable|string {
        info($this->getRecord());
        return $this->getRecord()->question->description;
    }
    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

}
