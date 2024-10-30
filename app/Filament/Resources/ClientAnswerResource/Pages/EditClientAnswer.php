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
use App\Models\Image;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use App\Models\DissertationDoctor;

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
        info($this->data);
        return $form
            ->schema([
                $this->firstFormInput,
                $this->secondFormInput,
                Hidden::make('grant_price')->reactive(),
                // TextInput::make('dissertation_answer')
                // ->disabled()
                // ->label('پایان نامه های فعلی')
                // ->visible(fn ($record) => EditClientAnswer::shouldShowDissertationFields($record))
                // ->placeholder('تست')
                // ->columnSpanFull(),
                Select::make('dissertation_1401')
                    ->options(function($record){
                        return $this->dessertationType($record)?EditClientAnswer::dissertation1401():EditClientAnswer::doctorDissertation1401();
                    })
                    ->multiple()
                    ->visible(fn ($record) => EditClientAnswer::shouldShowDissertationFields($record))
                    ->label('پایان نامه های ۱۴۰۱')
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('dissertation_1401', $state);
                    }),

                Select::make('dissertation_1402')
                    ->options(function ($record){
                        return $this->dessertationType($record)?EditClientAnswer::dissertation1402():EditClientAnswer::doctorDissertation1402();
                    })
                    ->multiple()
                    ->visible(fn ($record) => EditClientAnswer::shouldShowDissertationFields($record))
                    ->label('پایان نامه های ۱۴۰۲')
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('dissertation_1402', $state);
                    }),
                    
                    Repeater::make('image401')
                    ->label('مستندات')
                    ->maxItems(5)
                    ->relationship('images401')
                    ->schema([
                        FileUpload::make('image_path')
                            ->image()
                            ->maxSize(1000)
                            ->directory('')
                            ->visibility('public')
                            ->afterStateUpdated(function ($record,$state){
                                if(isset($record->images->image_path) && Storage::disk('public')->exists($record->images->image_path)){
                                    Storage::disk('public')->delete($record->images());
                                }
                            })
                            ->imageEditor()
                            ->label('مستندات ۱۴۰۱'),
                            Hidden::make('year')->default('1401')
                ]),

                //TODO  old answer visible

                Repeater::make('images402')
                
                ->relationship('images402')
                ->label('مستندات')
                ->maxItems(5)
                ->schema([
                    FileUpload::make('image_path')
                    ->image()
                    ->visibility('public')
                    ->maxSize(1000)
                    ->directory('')
                    ->afterStateUpdated(function ($record,$state){
                        if(isset($record->image_path) && Storage::disk('public')->exists($record->image_path)){
                            Storage::disk('public')->delete($record);
                        }
                    })
                    ->imageEditor()
                    ->label('مستندات ۱۴۰۲'),
                    Hidden::make('year')->default('1402')
                ])
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
            $dissertation1401['سایر'] = 'سایر';
        }
        return $dissertation1401;
    }
    protected static function doctorDissertation1401() {
        $dbQuery1401 = DissertationDoctor::where('user_id','=',auth()->user()->id)->first();
        $dissertation1401['سایر'] = 'سایر';
        if (isset($dbQuery1401->dissertation_1401)){
            $flip401 = json_decode($dbQuery1401->dissertation_1401,true);
            $dissertation1401 = array_flip($flip401);
            foreach ($dissertation1401 as $key => $value){
                $dissertation1401[$key] = $flip401[$value];
            }
        }
        return $dissertation1401;
    }
    protected static function dissertation1402() {
        $dbQuery1402 = Dissertation::where('user_id','=',auth()->user()->id)->first();
        $dissertation1402['سایر'] = 'سایر';
        if(isset($dbQuery1402->dissertation_1402)){
            $flip402 =  json_decode($dbQuery1402->dissertation_1402,true);
            $dissertation1402 = array_flip($flip402);
            foreach ($dissertation1402 as $key => $value){
                $dissertation1402[$key] = $flip402[$value];
            }
        }
        return $dissertation1402;
    }
    protected static function doctorDissertation1402() {
        $dbQuery1402 = DissertationDoctor::where('user_id','=',auth()->user()->id)->first();
        $dissertation1402['سایر'] = 'سایر';
        if(isset($dbQuery1402->dissertation_1402)){
            $flip402 =  json_decode($dbQuery1402->dissertation_1402,true);
            $dissertation1402 = array_flip($flip402);
            foreach ($dissertation1402 as $key => $value){
                $dissertation1402[$key] = $flip402[$value];
            }
        }
        return $dissertation1402;
    }
    protected static function shouldShowDissertationFields($record) {
        return in_array($record->question_id,['2_10_1','2_10_2','2_10_3','2_11_1','2_11_2','2_11_3']);
    }

    protected function dessertationType($record){
        if (in_array($record->question_id,['2_10_1','2_10_2','2_10_3'])) {
            $this->dessertationType = true;
        }else{
            $this->dessertationType = false;
        }
    }
}
