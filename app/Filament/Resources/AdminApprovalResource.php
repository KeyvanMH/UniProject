<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminApprovalResource\Pages;
use App\Filament\Resources\AdminApprovalResource\RelationManagers;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Answer;
use App\Models\Result;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use Closure;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class AdminApprovalResource extends Resource
{
    protected static ?string $model = Answer::class;
    protected static ?string $pluralLabel = 'تاییدیه خوداظهاری';
    protected static ?string $label = 'تاییدیه';
    protected static ?int $navigationSort = 0;
    protected static ?string $navigationGroup = 'تکمیلی کارشناس';
    protected static string | array $routeMiddleware = [
        AdminMiddleware::class,
    ];
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

//    public static function form(Form $form): Form
//    {
//        return $form
//            ->schema([
//                Forms\Components\Textarea::make('admin_response')->label('پاسخ')->maxLength(200)->columnSpanFull()
//            ]);
//    }

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

                Tables\Columns\ImageColumn::make('images401.image_path')
                    ->label('مستندات ۱۴۰۱')
                    ->url(function($record){
                        if($record->image402){
                            Storage::url($record->image401()->where('image_path','!=',null)->get());
                        }
                    })
                    ->disk('public')
                    ->stacked()
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: false),

                    Tables\Columns\ImageColumn::make('images402.image_path')
                    ->label('مستندات ۱۴۰۲')
                    ->disk('public')
                    ->circular()
                    ->stacked()
                    ->url(function($record){
                        if($record->image402){
                            Storage::url($record->image402()->where('image_path','!=',null)->get());
                        }
                    })
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('dissertation_1401')
                    ->label('پایان نامه های ۱۴۰۱')
                    ->formatStateUsing(fn ($record,$state) => implode("</br>",json_decode($record->dissertation_1401, true)))
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('dissertation_1402')
                    ->label('پایان نامه های ۱۴۰۲')
                    ->formatStateUsing(fn ($record,$state) => implode("</br>",json_decode($record->dissertation_1402, true)))
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\BooleanColumn::make('admin_approval')
                    ->label('تاییده کارشناس')
                    ->sortable()
                    ->action(function($record, $column) {
                        //todo maybe put it in db transaction
                        $name = $column->getName();
                        $record->update([
                            $name => !$record->$name
                        ]);
                        $result = Result::firstOrCreate(['user_id' => $record->user_id]);
                        $answer = Answer::where([['user_id','=',$record->user_id],['admin_approval','=',1]])->get()->toArray();
                        if($record->$name){
                            //add to result
                            if($record->question_id == '3_3_1') {
                                $result->is_married_woman = ($record->year_1401 * 0.1);
                                $result->total_grant_price = $result->sum_price + ($result->sum_price*$result->child_number) + ($result->is_married_woman * $result->sum_price);
                                $result->save();
                            }elseif($record->question_id == '3_3_2'){
                                $result->child_number = (($record->year_1401+$record->year_1402)/2)*0.1;
                                $result->total_grant_price = $result->sum_price + ($result->sum_price*$result->child_number) + ($result->is_married_woman * $result->sum_price);
                                $result->save();
                            }else{
                                $result->sum_price += $record->grant_price;
                                $result->total_grant_price = $result->sum_price + ($result->sum_price*$result->child_number) + ($result->is_married_woman * $result->sum_price);
                                $result->save();
                            }
                            //check for finished progress
                            $user = User::where('id','=',$record->user_id)->first();
                            if(count($answer) == 58){
                                $user->progress_finished = 1;
                            }else{
                                $user->progress_finished = 0;
                            }
                            $user->save();
                        }else{
                            //minus from result
                            if($record->question_id == '3_3_1'){
                                $result->total_grant_price -= $result->sum_price * $result->is_married_woman;
                                $result->is_married_woman = 0;
                                $result->save();
                            }elseif($record->question_id == '3_3_2'){
                                $result->total_grant_price -= $result->sum_price * $result->child_number;
                                $result->child_number = 0;
                                $result->save();
                            }else{
                                $result->sum_price -= $record->grant_price;
                                $result->total_grant_price = $result->sum_price + ($result->sum_price*$result->child_number) + ($result->is_married_woman * $result->sum_price);
                                $result->save();
                            }
                            //todo take back the progress_finished
                        }
                    }),
                TextColumn::make('admin_response')
                    ->label('پاسخ')
            ])
            ->defaultPaginationPageOption(5)
            ->query(function(){
                return Answer::whereHas('question',function (Builder $query){
                    $query->where('self_proclaimed','=',1);
                });
            })
            ->recordUrl(null)
            ->filters([
                Tables\Filters\SelectFilter::make('user')->relationship('user','name')->label('هیئت علمی')->searchable()->preload(),
                Tables\Filters\SelectFilter::make('question')->relationship('question','number_code',fn (Builder $query) => $query->where('self_proclaimed','=',1))->label('شماره آیین نامه سوال')->searchable()->preload(),
                Tables\Filters\SelectFilter::make('admin_approval')
                    ->label('تاییده کارشناس')
                    ->options([
                        '' => 'همه',
                        '1' => 'تایید شده',
                        '0' => 'تایید نشده',
                    ])
                    ->placeholder('انتخاب وضعیت تایید'),
                ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make()->exports([
                    ExcelExport::make()->withColumns([
                        Column::make('question.number_code')->heading('شماره آیین نامه سئوال'),
                        Column::make('question.description')->heading('شرح مختصر سئوال'),
                        Column::make('year_1401')->heading('سال ۱۴۰۱'),
                        Column::make('year_1402')->heading('سال ۱۴۰۲'),
                        Column::make('grant_price')->heading('مبلغ پژوهانه'),
                        Column::make('admin_approval')->heading('تاییده کارشناس'),
                        Column::make('dissertation_1401')->heading('پایان نامه های ۱۴۰۱'),
                        Column::make('dissertation_1402')->heading('پایان نامه های ۱۴۰۲'),
                    ])
                        ->rtl()
                        ->withFilename(date('Y - M - D').'-وضعیت فرم پژوهانه'),
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
            'index' => Pages\ListAdminApprovals::route('/'),
//            'create' => Pages\CreateAdminApproval::route('/create'),
            'edit' => Pages\EditAdminApproval::route('/{record}/edit'),
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
        return !$record->admin_approval;
    }
}

