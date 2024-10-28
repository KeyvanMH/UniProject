<?php

namespace App\Filament\Resources;

use App\Filament\Excel\CompleteExport;
use App\Filament\Resources\ClientAnswerResource\Pages;
use App\Http\Middleware\PasswordChangedMiddleware;
use App\Http\Middleware\ProcessFinishedMiddleware;
use App\Http\Middleware\UserMiddleware;
use App\Models\Answer;
use App\Models\Dissertation;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Closure;

use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ClientAnswerResource extends Resource
{
    protected static ?string $model = Answer::class;
    protected static ?string $pluralLabel = 'وضعیت فرم پژوهانه';
    protected static ?string $label = 'وضعیت فرم پژوهانه';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static string | array $routeMiddleware = [
        UserMiddleware::class,
//        ProcessFinishedMiddleware::class,
        PasswordChangedMiddleware::class
    ];

    public static function form(Form $form): Form
    {
        $dbQuery1401 = Dissertation::where('user_id','=',auth()->user()->id)->first();
        $dbQuery1402 = Dissertation::where('user_id','=',auth()->user()->id)->first();
        if (isset($dbQuery1401->dissertation_1401)){
            $flip401 = json_decode($dbQuery1401->dissertation_1401,true);
            $dissertation1401 = array_flip($flip401);
            foreach ($dissertation1401 as $key => $value){
                $dissertation1401[$key] = $flip401[$value];
            }
        }
        if(isset($dbQuery1402->dissertation_1402)){
            $flip402 =  json_decode($dbQuery1402->dissertation_1402,true);
            $dissertation1402 = array_flip($flip402);
            foreach ($dissertation1402 as $key => $value){
                $dissertation1402[$key] = $flip402[$value];
            }
        }
        return $form
            ->schema([
                Forms\Components\TextInput::make('year_1401')->default(0)
                ->numeric()
                ->label('مربوط به سال ۱۴۰۱')
                ->required()
                ->minValue(0)
                ->default(0)
                ->maxValue(100)
                ->reactive()
                ->afterStateUpdated(function ($set,$get,$state,$record){
                    if($record->question->grant == 2){
                        $set('grant_price',(($get('year_1401')+$get('year_1402'))/2)*$record->question->coefficient*210974088);

                    }elseif($record->question->grant == 1){

                        $set('grant_price',(($get('year_1401')+$get('year_1402'))/2)*$record->question->coefficient*8939580);
                    }
                }),

                Forms\Components\TextInput::make('year_1402')
                ->default(0)
                ->numeric()
                ->label('مربوط به سال ۱۴۰۲')
                ->required()
                ->minValue(0)
                ->maxValue(100)
                ->reactive()
                ->afterStateUpdated(function ($set,$get,$state,$record){
                    if($record->question->grant == 2){
                        $set('grant_price',(($get('year_1401')+$get('year_1402'))/2)*$record->question->coefficient*210974088);

                    }elseif($record->question->grant == 1){

                        $set('grant_price',(($get('year_1401')+$get('year_1402'))/2)*$record->question->coefficient*8939580);
                    }
                }),

                Forms\Components\Hidden::make('grant_price')->reactive(),
                
                Forms\Components\Select::make('dissertation_1401')
                    ->options($dissertation1401??null)
                    ->multiple()
                    ->visible(fn ($record) => ClientAnswerResource::shouldShowDissertationFields($record))
                    ->label('پایان نامه های ۱۴۰۱')
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('dissertation_1401', $state);
                    }),

                Forms\Components\Select::make('dissertation_1402')
                    ->options($dissertation1402??null)
                    ->multiple()
                    ->visible(fn ($record) => ClientAnswerResource::shouldShowDissertationFields($record))
                    ->label('پایان نامه های ۱۴۰۲')
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('dissertation_1402', $state);
                    }),

                Forms\Components\FileUpload::make('image_path_1401')
                    ->image()
                    ->maxSize(1000)
                    ->directory('')
                    ->visibility('public')
                    ->afterStateUpdated(function ($state, $record) {
                        if ($record->image_path_1401 && Storage::disk('public')->exists($record->image_path_1401)) {
                            Storage::disk('public')->delete($record->image_path_1401);
                        }
                    })
                    ->imageEditor()
                    ->label('مستندات ۱۴۰۱'),

                Forms\Components\FileUpload::make('image_path_1402')
                    ->image()
                    ->visibility('public')
                    ->maxSize(1000)
                    ->directory('')
                    ->afterStateUpdated(function ($record){
                        if ($record->image_path_1402 && Storage::disk('public')->exists($record->image_path_1402)) {
                            Storage::disk('public')->delete($record->image_path_1402);
                        }
                    })
                    ->imageEditor()->label('مستندات ۱۴۰۲')
            ]);
    }
    protected static function shouldShowDissertationFields($record) {
        return in_array($record->question_id,['2_10_1','2_10_2','2_10_3','2_11_1','2_11_2','2_11_3']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question.number_code')->label('شماره آیین نامه سئوال'),
                Tables\Columns\TextColumn::make('question.description')->label('شرح مختصر سئوال'),
                Tables\Columns\TextColumn::make('year_1401')->label('سال ۱۴۰۱'),
                Tables\Columns\TextColumn::make('year_1402')->label('سال ۱۴۰۲'),
                Tables\Columns\BooleanColumn::make('admin_approval')->label('تاییده کارشناس'),
                TextColumn::make('admin_response')
                    ->label('نظر کارشناس'),
                Tables\Columns\ImageColumn::make('image_path_1401')->label('مستندات ۱۴۰۱')->disk('public')->url(fn ($record) => $record->image_path_1401 ? Storage::url($record->image_path_1401) : null)->circular(),
                Tables\Columns\ImageColumn::make('image_path_1402')->label('مستندات ۱۴۰۲')->disk('public')->url(fn ($record) => $record->image_path_1401 ? Storage::url($record->image_path_1402) : null)->circular(),
                TextColumn::make('dissertation_1401')
                    ->label('پایان نامه های ۱۴۰۱')
                    ->getStateUsing(fn ($record) => json_decode($record->dissertation_1401, true)),
                TextColumn::make('dissertation_1402')
                    ->label('پایان نامه های ۱۴۰۲')
                    ->getStateUsing(fn ($record) => json_decode($record->dissertation_1402, true)),
            ])
            ->filters([

            ])
            ->query(function (){
                return Answer::where('user_id','=',auth()->user()->id);
            })
//            ->
            ->actions([
                Tables\Actions\EditAction::make(),
//                ExportAction::make()->exports([
//                    ExcelExport::make('table')->fromTable()
//                        ->withColumns([
//                            Column::make('grant_price'),
//                            Column::make('user.name'),
//                        ])
//                        ->modifyQueryUsing(fn ($query) => $query->with('user'))->rtl(),
//                ])
            ])
            ->bulkActions([
                ExportBulkAction::make()->exports([
                    ExcelExport::make()->withColumns([
                        Column::make('question.number_code')->heading('شماره آیین نامه سئوال'),
                        Column::make('question.description')->heading('شرح مختصر سئوال'),
                        Column::make('year_1401')->heading('سال ۱۴۰۱'),
                        Column::make('year_1402')->heading('سال ۱۴۰۲'),
                        Column::make('admin_approval')->heading('تاییده کارشناس'),
                        Column::make('dissertation_1401')->heading('پایان نامه های ۱۴۰۱'),
                        Column::make('dissertation_1402')->heading('پایان نامه های ۱۴۰۲'),
                    ])
                        ->rtl()
                        ->withFilename(date('Y - M - D').'-وضعیت فرم پژوهانه'),
                ])
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientAnswers::route('/'),
//            'create' => Pages\CreateClientAnswer::route('/create'),
            'edit' => Pages\EditClientAnswer::route('/{record}/edit'),
        ];
    }
    public static function canAccess(): bool {
        return !Hash::check(auth()->user()->national_code,auth()->user()->password);
    }

    public static function canView(Model $record): bool {
        return !Hash::check(auth()->user()->national_code,auth()->user()->password);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return !$record->admin_approval;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
