<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('نام کاربر'),

                TextInput::make('password')
                    ->required()
                    ->password()
                    ->label('رمز عبور')
                    ->minLength(8),

                Select::make('role')
                    ->options([
                        'user' => 'عضو هیئت علمی',
                        'admin' => 'کارشناس',
                    ])
                    ->default('user')
                    ->label('سطح دسترسی')
                    ->columnSpanFull(),
            ]);
    }

}
