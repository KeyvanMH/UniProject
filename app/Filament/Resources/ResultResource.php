<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResultResource\Pages;
use App\Filament\Resources\ResultResource\RelationManagers;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Result;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;
    protected static ?string $pluralLabel = 'مبلغ نهایی';
    protected static ?string $label = 'مبلغ نهایی';
    protected static string | array $routeMiddleware = [
        AdminMiddleware::class,
    ];
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                    ->label('نام هیئت علمی'),
                Tables\Columns\TextColumn::make('sum_price')
                    ->label('مبلغ بدون سئوالات درصدی')
                    ->money('rial'),
                Tables\Columns\TextColumn::make('child_number')
                    ->label('گرنت تعداد فرزند'),
                Tables\Columns\TextColumn::make('is_married_woman')
                    ->label('گرنت وضعیت تاهل'),
                Tables\Columns\TextColumn::make('total_grant_price')
                    ->money('rial')
                    ->label('مبلغ نهایی'),
                Tables\Columns\IconColumn::make('user.progress_finished')
                    ->label('نهایی شدن فرایند')
                    ->alignCenter()
                    ->icon(fn ($state) =>  match ($state){
                        1 => 'heroicon-o-check-circle',
                        0 => 'heroicon-o-x-mark',
                        default => 'heroicon-o-rectangle-stack'
                    })
                    ->color(fn ($state) => match($state){
                        0 => 'danger',
                        1 => 'success',
                        default => 'gray'
                    })
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user.name')->relationship('user','name')->label('هیئت علمی')->searchable()->preload(),
            ])
            ->actions([
            ])
            ->bulkActions([
                ExportBulkAction::make()->exports([
                    ExcelExport::make()->withColumns([
                        Column::make('user.name')->heading('نام هیئت علمی'),
                        Column::make('user.national_code')->heading('کد ملی هیئت علمی'),
                        Column::make('sum_price')->heading('مبلغ بدون سئوالات درصدی'),
                        Column::make('child_number')->heading('گرنت تعداد فرزند'),
                        Column::make('is_married_woman')->heading('گرنت وضعیت تاهل'),
                        Column::make('total_grant_price')->heading('مبلغ نهایی '),
                        Column::make('user.progress_finished')->heading('نهایی شدن فرایند'),
                    ])
                        ->rtl()
                        ->withFilename(date('Y - M - D').'مبلغ نهایی اعضا'),
            ]),
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
            'index' => Pages\ListResults::route('/'),
//            'create' => Pages\CreateResult::route('/create'),
//            'edit' => Pages\EditResult::route('/{record}/edit'),
        ];
    }
    public static function canCreate(): bool {
        return false;
    }
    public static function canEdit(Model $record): bool {
        return false;
    }
}
