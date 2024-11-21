<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResultResource\Pages;
use App\Filament\Resources\ResultResource\RelationManagers;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Answer;
use App\Models\Result;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
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
    protected static ?string $navigationGroup = 'گزارش گیری';
    protected static string | array $routeMiddleware = [
        AdminMiddleware::class,
    ];
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

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
                Tables\Columns\TextColumn::make('dissertation')
                    ->label('پایان نامه ارشد')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default(function (Model $record){
                        $answers = Answer::where([['user_id', $record->user_id],['admin_approval','=',1]])
                            ->whereIn('question_id', ['2_10_1', '2_10_2', '2_10_3'])
                            ->get()
                            ->keyBy('question_id');

                        return ($answers->get('2_10_1')->year_1401 ?? 0) +
                            ($answers->get('2_10_2')->year_1401 ?? 0) +
                            ($answers->get('2_10_3')->year_1401 ?? 0) +
                            ($answers->get('2_10_1')->year_1402 ?? 0) +
                            ($answers->get('2_10_2')->year_1402 ?? 0) +
                            ($answers->get('2_10_3')->year_1402 ?? 0);
                    }),
                Tables\Columns\TextColumn::make('doctorDissertation')
                    ->label('پایان نامه دکتری')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default(function (Model $record){
                        $answers = Answer::where([['user_id', $record->user_id],['admin_approval','=',1]])
                            ->whereIn('question_id', ['2_11_1', '2_11_2', '2_11_3'])
                            ->get()
                            ->keyBy('question_id');

                        return ($answers->get('2_11_1')->year_1401 ?? 0) +
                            ($answers->get('2_11_2')->year_1401 ?? 0) +
                            ($answers->get('2_11_3')->year_1401 ?? 0) +
                            ($answers->get('2_11_1')->year_1402 ?? 0) +
                            ($answers->get('2_11_2')->year_1402 ?? 0) +
                            ($answers->get('2_11_3')->year_1402 ?? 0);
                    }),
                Tables\Columns\TextColumn::make('dissertationPrice')
                    ->label('مبلغ پایان نامه ها')
                    ->money('rial')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default(function (Model $record,Set $set){
                        $answers = Answer::where([['user_id', $record->user_id],['admin_approval','=',1]])
                            ->whereIn('question_id', ['2_11_1', '2_11_2', '2_11_3','2_10_1','2_10_2','2_10_3'])
                            ->get()
                            ->keyBy('question_id');

                        $dissertationPrice = ($answers->get('2_11_1')->grant_price ?? 0) +
                            ($answers->get('2_11_2')->grant_price ?? 0) +
                            ($answers->get('2_11_3')->grant_price ?? 0) +
                            ($answers->get('2_10_1')->grant_price ?? 0) +
                            ($answers->get('2_10_2')->grant_price ?? 0) +
                            ($answers->get('2_10_3')->grant_price ?? 0);
                        return $dissertationPrice;
                    }),
                Tables\Columns\TextColumn::make('otherPrice')
                    ->label('مبلغ سوالات بدون پایان نامه')
                    ->money('rial')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default(function (Model $record){
                        $answers = Answer::where([['user_id','=',$record->user_id],['admin_approval','=',1]])->get()->keyBy('question_id');
                        $totalPrice = 0;
                        foreach($answers as $answer){
                            $totalPrice += $answer->grant_price;
                        }
                        return $totalPrice - (($answers->get('2_11_1')->grant_price ?? 0) +
                                ($answers->get('2_11_2')->grant_price ?? 0) +
                                ($answers->get('2_11_3')->grant_price ?? 0) +
                                ($answers->get('2_10_1')->grant_price ?? 0) +
                                ($answers->get('2_10_2')->grant_price ?? 0) +
                                ($answers->get('2_10_3')->grant_price ?? 0));

                    }),
                Tables\Columns\TextColumn::make('sum_price')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('مبلغ بدون سئوالات درصدی')
                    ->money('rial'),
                Tables\Columns\TextColumn::make('child_number')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('گرنت تعداد فرزند'),
                Tables\Columns\TextColumn::make('is_married_woman')
                    ->toggleable(isToggledHiddenByDefault: true)
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
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user.name')->relationship('user','name')->label('هیئت علمی')->searchable()->preload(),
            ])
            ->paginationPageOptions(['5','10','20','50'])
            ->defaultPaginationPageOption(5)
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
                        Column::make('user.progress_finished')->heading('نهایی شدن فرایند')->formatStateUsing(function ($state){
                            return $state?'اتمام پروسه':'عدم اتمام پروسه';
                        }),
                    ])
                        ->rtl()
                        ->askForFilename(label: 'نام فایل اکسل')
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
    public static function canAccess(): bool {
        return auth()->user()->role == 'admin';
    }

    public static function canView(Model $record): bool {
        return auth()->user()->role == 'admin';
    }

    public static function canCreate(): bool {
        return false;
    }
    public static function canEdit(Model $record): bool {
        return false;
    }
}
