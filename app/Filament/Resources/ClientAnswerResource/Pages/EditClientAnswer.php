<?php

namespace App\Filament\Resources\ClientAnswerResource\Pages;

use App\Filament\Const\DefaultConst;
use App\Filament\Resources\ClientAnswerResource;
use App\Models\Answer;
use App\Models\Dissertation;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
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
        return $form
            ->schema([
                $this->firstFormInput,
                $this->secondFormInput,
                Hidden::make('grant_price')
                    ->reactive(),

                Textarea::make('dissertation_answer_1401')
                 ->disabled()
                 ->rows(function ($record){
                     if(is_array($record->dissertation_1401)){
                         return count($record->dissertation_1401)+1;
                     }
                     else{
                         return count(json_decode($record->dissertation_1401))+1;
                     }
                 })
                 ->label('پایان نامه های انتخابی فعلی ۱۴۰۱')
                 ->visible(fn ($record) => EditClientAnswer::shouldShowDissertationFields($record))
                 ->placeholder(function($record) {
                         if(is_array($record->dissertation_1401)){
                             return implode("\n",$record->dissertation_1401);
                         }else{
                             return implode("\n",json_decode($record->dissertation_1401));
                         }
                 }),

                Textarea::make('dissertation_answer_1402')
                 ->disabled()
                ->rows(function ($record){
                    if(is_array($record->dissertation_1402)){
                        return count($record->dissertation_1402)+1;
                    }
                    else{
                        return count(json_decode($record->dissertation_1402))+1;
                    }
                })
                 ->label(' پایان نامه های انتخابی فعلی ۱۴۰۲')
                 ->visible(fn ($record) => EditClientAnswer::shouldShowDissertationFields($record))
                 ->placeholder(function($record){
                     if(is_array($record->dissertation_1402)){
                        return implode("\n",$record->dissertation_1402);
                     }else{
                         return implode("\n",json_decode($record->dissertation_1402));
                     }
                     }),

                Placeholder::make('no label')
                    ->hiddenLabel()
                    ->columnSpanFull()
                    ->visible(fn ($record) => EditClientAnswer::shouldShowDissertationFields($record))
                    ->content('استاد راهنمای محترم، در صورتی که دارای پایان نامه کارشناسی ارشد یا رساله دکتری تحت راهنمایی می باشید که در تاریخ تصویب پروپوزال آنها  در سال ۱۴۰۱ و ۱۴۰۲  می باشد و در لیست پیش فرض نمایش داده نمی شود، لطفا با انتخاب گزینه سایر نسبت به بارگذاری حکم تصویب آن اقدام نمایید.'),

                Select::make('dissertation_1401')
                    ->options(function($record) {
                        return $this->dessertationType($record) ? EditClientAnswer::dissertation1401():EditClientAnswer::doctorDissertation1401();
                    })
                    ->reactive()
                    ->default(function($record){
                        if(is_array($record->dissertation_1401)){
                            return implode("\n",$record->dissertation_1401);
                        }else{
                            return implode("\n",json_decode($record->dissertation_1401));
                        }
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
                    ->reactive()
                    ->default(function ($record) {
                        if (is_array($record->dissertation_1402)) {
                            return implode("\n", $record->dissertation_1402);
                        } else {
                            return implode("\n", json_decode($record->dissertation_1402));
                        }
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
                    ->reactive()
                        ->visible(function($get,$record){
                            if(EditClientAnswer::shouldShowDissertationFields($record)){
                                return in_array('سایر',$get('dissertation_1401'));
                            }
                            return true;
                        })
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


                Repeater::make('images402')
                ->relationship('images402')
                ->label('مستندات')
                ->maxItems(5)
                ->reactive()
                ->visible(function($get,$record){
                    if(EditClientAnswer::shouldShowDissertationFields($record)){
                        return in_array('سایر',$get('dissertation_1402'));
                    }
                    return true;
                })
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
                        $set('grant_price',(($get('year_1401')+$get('year_1402'))/2)*$record->question->coefficient*DefaultConst::grantTwo);
                    }elseif($record->question->grant == 1){
                        $set('grant_price',(($get('year_1401')+$get('year_1402'))/2)*$record->question->coefficient*DefaultConst::grantOne);
                    }
                });
            $this->secondFormInput = Toggle::make('year_1402')
                ->label('مربوط به سال ۱۴۰۲')
                ->afterStateUpdated(function ($set,$get,$state,$record){
                    if($record->question->grant == 2){
                        $set('grant_price',(($get('year_1401')+$get('year_1402'))/2)*$record->question->coefficient*DefaultConst::grantTwo);
                    }elseif($record->question->grant == 1){
                        $set('grant_price',(($get('year_1401')+$get('year_1402'))/2)*$record->question->coefficient*DefaultConst::grantOne);
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
                ->maxValue(DefaultConst::maxInput)
                ->reactive()
                ->afterStateUpdated(function ($set,$get,$state,$record){
                    if($record->question->grant == 2){
                        $set('grant_price',(((float)$get('year_1401')+(float)$get('year_1402'))/2)*$record->question->coefficient*DefaultConst::grantTwo);
                    }elseif($record->question->grant == 1 ){
                        $set('grant_price',(((float)$get('year_1401')+(float)$get('year_1402'))/2)*$record->question->coefficient*DefaultConst::grantOne);
                    }
                });
            $this->secondFormInput = TextInput::make('year_1402')
                ->default(0)
                ->numeric()
                ->label('مربوط به سال ۱۴۰۲')
                ->required()
                ->minValue(0)
                ->maxValue(DefaultConst::maxInput)
                ->reactive()
                ->afterStateUpdated(function ($set,$get,$state,$record){
                    if($record->question->grant == 2  ){
                        $set('grant_price',(((float)$get('year_1401')+(float)$get('year_1402'))/2)*$record->question->coefficient*DefaultConst::grantTwo);
                    }elseif($record->question->grant == 1){
                        $set('grant_price',(((float)$get('year_1401')+(float)$get('year_1402'))/2)*$record->question->coefficient*DefaultConst::grantOne);
                    }
                });
        }
    }
    protected static function dissertation1401() {
        $dbQuery1401 = Dissertation::where('user_id','=',auth()->user()->id)->first();
        $dissertation1401 = [];
        if (isset($dbQuery1401->dissertation_1401)){
            $flip401 = json_decode($dbQuery1401->dissertation_1401,true);
            $dissertation1401 = array_flip($flip401);
            foreach ($dissertation1401 as $key => $value){
                $dissertation1401[$key] = $flip401[$value];
            }
        }
        $dissertation1401['سایر'] = 'سایر';
        return $dissertation1401;
    }
    protected static function doctorDissertation1401() {
        $dbQuery1401 = DissertationDoctor::where('user_id','=',auth()->user()->id)->first();
        $dissertation1401 = [];
        if (isset($dbQuery1401->dissertation_1401)){
            $flip401 = json_decode($dbQuery1401->dissertation_1401,true);
            $dissertation1401 = array_flip($flip401);
            foreach ($dissertation1401 as $key => $value){
                $dissertation1401[$key] = $flip401[$value];
            }
        }
        $dissertation1401['سایر'] = 'سایر';
        return $dissertation1401;
    }
    protected static function dissertation1402() {
        $dbQuery1402 = Dissertation::where('user_id','=',auth()->user()->id)->first();
        $dissertation1402 = [];
        if(isset($dbQuery1402->dissertation_1402)){
            $flip402 =  json_decode($dbQuery1402->dissertation_1402,true);
            $dissertation1402 = array_flip($flip402);
            foreach ($dissertation1402 as $key => $value){
                $dissertation1402[$key] = $flip402[$value];
            }
        }
        $dissertation1402['سایر'] = 'سایر';
        return $dissertation1402;
    }
    protected static function doctorDissertation1402() {
        $dbQuery1402 = DissertationDoctor::where('user_id','=',auth()->user()->id)->first();
        $dissertation1402 = [];
        if(isset($dbQuery1402->dissertation_1402)){
            $flip402 =  json_decode($dbQuery1402->dissertation_1402,true);
            $dissertation1402 = array_flip($flip402);
            foreach ($dissertation1402 as $key => $value){
                $dissertation1402[$key] = $flip402[$value];
            }
        }
        $dissertation1402['سایر'] = 'سایر';
        return $dissertation1402;
    }
    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void {
        $this->record->year_1401 = $this->data['year_1401'];
        $this->record->year_1402 = $this->data['year_1402'];
        if($this->record->question->grant == 2){
            $this->record['grant_price'] = (($this->data['year_1401']+$this->data['year_1402'])/2) * $this->record->question->coefficient*DefaultConst::grantTwo;
        }elseif($this->record->question->grant == 1){
            $this->record['grant_price'] = (($this->data['year_1401']+$this->data['year_1402'])/2) * $this->record->question->coefficient*DefaultConst::grantOne;
        }
        $this->record->save();
        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();
    }

    protected static function shouldShowDissertationFields($record) {
        return in_array($record->question_id,['2_10_1','2_10_2','2_10_3','2_11_1','2_11_2','2_11_3']);
    }

    protected function dessertationType($record){
        return in_array($record->question_id,['2_10_1','2_10_2','2_10_3']);
    }
}
