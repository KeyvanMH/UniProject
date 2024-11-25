<?php

namespace App\Filament\Resources\AdminApprovalResource\Pages;

use App\Filament\Const\DefaultConst;
use App\Filament\Resources\AdminApprovalResource;
use App\Filament\Resources\ClientAnswerResource\Pages\EditClientAnswer;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditAdminApproval extends EditRecord
{
    protected static string $resource = AdminApprovalResource::class;
    protected $booleanQuestion = ['2_24','2_25','2_26','2_27','2_28','2_29'];
    protected $special = ['3_3_1'];
    public function getHeading(): \Illuminate\Contracts\Support\Htmlable|string {
        return $this->getRecord()->question->description.'('.$this->getRecord()->question->number_code.')';
    }
    public function form(Form $form): Form {
        $this->numberInput();
        return $form
            ->schema([
                $this->firstFormInput,
                $this->secondFormInput,
                Hidden::make('grant_price')
                    ->reactive(),
                Textarea::make('admin_response')->label('پاسخ')->maxLength(200)->columnSpanFull()
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
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void {
        $this->record->year_1401 = $this->data['year_1401'];
        $this->record->year_1402 = $this->data['year_1402'];
        if($this->record->question->grant == 2){
            $this->record['grant_price'] = (($this->data['year_1401']+$this->data['year_1402'])/2) * $this->record->question->coefficient*DefaultConst::grantTwo;
        }elseif($this->record->question->grant == 1){
            $this->record['grant_price'] = (($this->data['year_1401']+$this->data['year_1402'])/2) * $this->record->question->coefficient*DefaultConst::grantOne;
        }
        $this->record->admin_response = $this->data['admin_response'];
        $this->record->save();
        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();
    }
}
