<?php

namespace App\Filament\Resources\ClientAnswerResource\Pages;

use App\Filament\Resources\ClientAnswerResource;
use App\Models\Answer;
use App\Models\Dissertation;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Panel;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditClientAnswer extends EditRecord {
    protected static string $resource = ClientAnswerResource::class;
    protected $booleanQuestion = ['2_24','2_25','2_26','2_27','2_28','2_29'];
    protected $special = ['3_3_1'];
    protected  $firstFormInput ;
    protected  $secondFormInput ;

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
    public function form(Form $form): Form {
        $this->numberInput();
        return $form
            ->schema([
                $this->firstFormInput,
                $this->secondFormInput,
                Hidden::make('grant_price')->reactive(),

                Select::make('dissertation_1401')
                    ->options(EditClientAnswer::dissertation1401()??null)
                    ->multiple()
                    ->visible(fn ($record) => EditClientAnswer::shouldShowDissertationFields($record))
                    ->label('پایان نامه های ۱۴۰۱')
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('dissertation_1401', $state);
                    }),

                Select::make('dissertation_1402')
                    ->options(EditClientAnswer::dissertation1402()??null)
                    ->multiple()
                    ->visible(fn ($record) => EditClientAnswer::shouldShowDissertationFields($record))
                    ->label('پایان نامه های ۱۴۰۲')
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('dissertation_1402', $state);
                    }),

                FileUpload::make('image_path_1401')
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

                FileUpload::make('image_path_1402')
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

    protected  function numberInput(){
        if (in_array($this->record->question->number_code,$this->booleanQuestion)){
            //2 toggle input
            $this->firstFormInput = Toggle::make('year_1401')
                ->label('مربوط به سال ۱۴۰۱')
                ->afterStateUpdated(function ($set,$get,$state,$record){
                    if($record->question->grant == 2){
                        $set('grant_price',(($get('year_1401')+$get('year_1402'))/2)*$record->question->coefficient*210974088);
                    }elseif($record->question->grant == 1){
                        $set('grant_price',(($get('year_1401')+$get('year_1402'))/2)*$record->question->coefficient*8939580);
                    }
                });
            $this->secondFormInput = Toggle::make('year_1402')
                ->label('مربوط به سال ۱۴۰۲')
                ->afterStateUpdated(function ($set,$get,$state,$record){
                    if($record->question->grant == 2){
                        $set('grant_price',(($get('year_1401')+$get('year_1402'))/2)*$record->question->coefficient*210974088);
                    }elseif($record->question->grant == 1){
                        $set('grant_price',(($get('year_1401')+$get('year_1402'))/2)*$record->question->coefficient*8939580);
                    }
                });
        } elseif (in_array($this->record->question->number_code,$this->special)){
            //1 toggle input
            $this->firstFormInput = Toggle::make('year_1401')
                ->label('متاهل بودن یا نبودن')
                ->columnSpanFull()
                ->afterStateUpdated(function($set,$get,$record){
                    $set('grant_price',0);
                    $set('year_1402',$get('year_1401'));
                });
            $this->secondFormInput = Hidden::make('year_1402')
                ->reactive();
        } else{
            //2 text input
            $this->firstFormInput = TextInput::make('year_1401')->default(0)
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
                });
            $this->secondFormInput = TextInput::make('year_1402')
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
                });
        }
    }
    protected static function dissertation1401() {
        $dbQuery1401 = Dissertation::where('user_id','=',auth()->user()->id)->first();
        if (isset($dbQuery1401->dissertation_1401)){
            $flip401 = json_decode($dbQuery1401->dissertation_1401,true);
            $dissertation1401 = array_flip($flip401);
            foreach ($dissertation1401 as $key => $value){
                $dissertation1401[$key] = $flip401[$value];
            }
            return $dissertation1401;
        }
        return null;
    }
    protected static function dissertation1402() {
        $dbQuery1402 = Dissertation::where('user_id','=',auth()->user()->id)->first();
        if(isset($dbQuery1402->dissertation_1402)){
            $flip402 =  json_decode($dbQuery1402->dissertation_1402,true);
            $dissertation1402 = array_flip($flip402);
            foreach ($dissertation1402 as $key => $value){
                $dissertation1402[$key] = $flip402[$value];
            }
            return $dissertation1402;
        }
        return null;
    }
    protected static function shouldShowDissertationFields($record) {
        return in_array($record->question_id,['2_10_1','2_10_2','2_10_3','2_11_1','2_11_2','2_11_3']);
    }

}
