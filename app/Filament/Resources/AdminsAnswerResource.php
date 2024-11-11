<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminsAnswerResource\Pages;
use App\Filament\Resources\AdminsAnswerResource\RelationManagers;
use App\Http\Middleware\AdminMiddleware;
use App\Models\AdminsAnswer;
use App\Models\Answer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdminsAnswerResource extends Resource
{
    protected static ?string $model = Answer::class;

    protected static ?string $pluralLabel = 'وضعیت پاسخ های کارشناس';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'تکمیلی کارشناس';
    protected static ?int $navigationSort = 2;
    protected static string | array $routeMiddleware = [
        AdminMiddleware::class
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('نام هیئت علمی')
                    ->searchable(),

                Tables\Columns\TextColumn::make('question.number_code')
                    ->label('شماره آیین نامه'),

                Tables\Columns\TextColumn::make('question.description')
                    ->label('شرح سوال')
                    ->searchable(),

                Tables\Columns\TextColumn::make('year_1401')->label('سال ۱۴۰۱'),
                Tables\Columns\TextColumn::make('year_1402')->label('سال ۱۴۰۲'),
                TextColumn::make('grant_price')->label('مبلغ پژوهانه')->money('rial'),
            ])
            ->filters([
                //
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
            ])
            ->defaultPaginationPageOption(5)
            ->query(function(){
                return Answer::whereHas('question',function (Builder $query){
                    $query->where('self_proclaimed','=',0);
                });
            })
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
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
            'index' => Pages\ListAdminsAnswers::route('/'),
//            'create' => Pages\CreateAdminsAnswer::route('/create'),
//            'edit' => Pages\EditAdminsAnswer::route('/{record}/edit'),
        ];
    }
    public static function canEdit(Model $record): bool {
        return false;
    }
    public static function canCreate(): bool {
        return false;
    }
    public static function canAccess(): bool {
        return auth()->user()->role == 'admin';
    }

    public static function canView(Model $record): bool {
        return auth()->user()->role == 'admin';
    }

}
