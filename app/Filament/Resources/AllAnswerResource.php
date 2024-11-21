<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AllAnswerResource\Pages;
use App\Filament\Resources\AllAnswerResource\RelationManagers;
use App\Http\Middleware\AdminMiddleware;
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
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class AllAnswerResource extends Resource
{
    protected static ?string $model = Answer::class;
    protected static ?string $label = 'تجمیع پاسخ سئوالات';
    protected static ?string $navigationGroup = 'گزارش گیری';

    protected static ?string $pluralLabel = 'تجمیع پاسخ سئوالات';
    protected static string | array $routeMiddleware = [
        AdminMiddleware::class,
    ];

    protected static ?string $navigationIcon = 'heroicon-o-bars-4';

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
            ->paginationPageOptions(['5','10','15','23','50','58'])
            ->defaultPaginationPageOption(5)
            ->filters([
                Tables\Filters\SelectFilter::make('user')->relationship('user','name')->label('هیئت علمی')->searchable()->preload(),
                Tables\Filters\SelectFilter::make('question')->relationship('question','number_code')->label('شماره آیین نامه سوال')->searchable()->preload(),
            ])
            ->actions([
            ])
            ->bulkActions([
                ExportBulkAction::make()->exports([
                    ExcelExport::make()->withColumns([
                        Column::make('user.name')->heading('نام هيئت علمی'),
                        Column::make('question.number_code')->heading('شماره آیین نامه سئوال'),
                        Column::make('question.description')->heading('شرح مختصر سئوال'),
                        Column::make('year_1401')->heading('سال ۱۴۰۱'),
                        Column::make('year_1402')->heading('سال ۱۴۰۲'),
                        Column::make('grant_price')->heading('مبلغ پژوهانه'),
                    ])
                        ->rtl()
                        ->askForFilename(label: 'نام فایل اکسل')
                ])
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
            'index' => Pages\ListAllAnswers::route('/'),
            'create' => Pages\CreateAllAnswer::route('/create'),
            'edit' => Pages\EditAllAnswer::route('/{record}/edit'),
        ];
    }
    public static function canAccess(): bool {
        return auth()->user()->role == 'admin';
    }

    public static function canView(Model $record): bool {
        return auth()->user()->role == 'admin';
    }
    public static function canCreate(): bool {
        return false;
    }
    public static function canDelete(Model $record): bool {
        return false;
    }
    public static function canEdit(Model $record): bool {
        return false;
    }}
