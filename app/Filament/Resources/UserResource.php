<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Answer;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $label = 'کاربر';
    protected static ?string $pluralLabel = 'مدیریت کاربران';
    protected static string | array $routeMiddleware = [
        \App\Http\Middleware\AdminMiddleware::class,
    ];

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('نام کاربر'),

                Forms\Components\TextInput::make('national_code')
                    ->required()
                    ->numeric()
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'این کد ملی قبلا ثبت شده است.',
                    ])
                    ->label('کد ملی'),

                Forms\Components\TextInput::make('password')
                    ->required()
                    ->password()
                    ->label('رمز عبور')
                    ->minLength(8),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->label('رایانامه'),

                Forms\Components\Select::make('role')
                    ->options([
                        'user' => 'عضو هیئت علمی',
                        'admin' => 'کارشناس',
                    ])
                    ->required()
                    ->columnSpanFull()
                    ->label('سطح دسترسی'),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')
                ->label('آیدی')
                ->sortable(),

            Tables\Columns\TextColumn::make('name')
                ->label('نام کاربر')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('national_code')
                ->label('کد ملی')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('role')
                ->label('سطح دسترسی')
                ->formatStateUsing(fn ($state) => $state === 'user' ? 'کاربر' : 'کارشناس')
                ->sortable(),

            Tables\Columns\TextColumn::make('first_time_form_filled')
                ->label('پر کردن فرم')
                ->formatStateUsing(function ($state){
                    if($state == 1){
                        return ' پر کرده است';
                    }else{
                        return 'پر نکرده است';
                    }
                })
                ->color(fn ($state) => match($state){
                    0 => 'danger',
                    1 => 'success',
                    default => 'gray'
                })
//                ->toggleable(isToggledHiddenByDefault: true)
                ->sortable(),

        ])
            ->recordUrl(null)
            ->filters([
                // Add any filters if needed
            ])
            ->defaultPaginationPageOption(5)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->role == 'admin';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->role == 'admin';
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()->role == 'admin';
    }
}
