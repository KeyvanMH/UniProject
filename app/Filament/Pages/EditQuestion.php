<?php

namespace App\Filament\Pages;

use App\Http\Middleware\PasswordChangedMiddleware;
use App\Http\Middleware\ProcessFinishedMiddleware;
use App\Http\Middleware\UserMiddleware;
use App\Models\Answer;
use App\Models\Dissertation;
use App\Models\DissertationDoctor;
use App\Models\FormData;
use App\Models\Question;
use App\Models\User;
use Faker\Provider\ar_EG\Text;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
//use Filament\TextInput;

use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class EditQuestion extends Page implements HasForms
{
    use InteractsWithForms;
    public ?array $data = [];
    protected static ?string $title = "فرم پژوهانه";
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = "فرم پژوهانه هیئت علمی";

    protected static string $view = 'filament.pages.edit-question';
    public $defaultAction = 'formTutorial';
    protected $messages = [
        'number_2_5_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_5_1401.max.' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_5_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_5_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',


        'number_2_6_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_6_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_6_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_6_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_7_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_7_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_7_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_7_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_9_1_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_9_1_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_9_1_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_9_1_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_9_2_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_9_2_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_9_2_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_9_2_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_24_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_24_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_24_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_24_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_25_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_25_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_25_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_25_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_26_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_26_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_26_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_26_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_27_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_27_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_27_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_27_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_28_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_28_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_28_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_28_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_29_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_29_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_29_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_29_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_36_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_36_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_36_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_36_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_37_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_37_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_37_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_37_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_38_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_38_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_38_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_38_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',


        'number_2_10_1_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_10_1_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_10_1_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_10_1_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_10_2_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_10_2_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_10_2_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_10_2_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_10_3_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_10_3_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_10_3_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_10_3_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_11_1_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_11_1_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_11_1_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_11_1_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_11_2_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_11_2_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_11_2_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_11_2_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_11_3_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_11_3_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_11_3_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_11_3_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_2_20_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_20_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_2_20_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_2_20_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',

        'number_3_3_2_1401.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_3_3_2_1401.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
        'number_3_3_2_1402.min' => 'لطفا عددی بزرگتر از ۰ وارد کنید.',
        'number_3_3_2_1402.max' => 'لطفا عددی کمتر از ۱۰۰ وارد کنید.',
    ];
    public $number_2_5_1401;
    public $number_2_5_1402;
    public $image_2_5_1401;
    public $image_2_5_1402;
    public $description_2_5_1401;
    public $description_2_5_1402;

    public $number_2_6_1401;
    public $number_2_6_1402;
    public $image_2_6_1401;
    public $image_2_6_1402;
    public $description_2_6_1401;
    public $description_2_6_1402;

    public $number_2_7_1401;
    public $number_2_7_1402;
    public $image_2_7_1401;
    public $image_2_7_1402;
    public $description_2_7_1401;
    public $description_2_7_1402;

    public $number_2_9_1_1401;
    public $number_2_9_1_1402;
    public $image_2_9_1_1401;
    public $image_2_9_1_1402;
    public $description_2_9_1_1401;
    public $description_2_9_1_1402;

    public $number_2_9_2_1401;
    public $number_2_9_2_1402;
    public $image_2_9_2_1401;
    public $image_2_9_2_1402;
    public $description_2_9_2_1401;
    public $description_2_9_2_1402;

    public $number_2_24_1401;
    public $number_2_24_1402;
    public $image_2_24_1401;
    public $image_2_24_1402;
    public $description_2_24_1401;
    public $description_2_24_1402;

    public $number_2_25_1401;
    public $number_2_25_1402;
    public $image_2_25_1401;
    public $image_2_25_1402;
    public $description_2_25_1401;
    public $description_2_25_1402;

    public $number_2_26_1401;
    public $number_2_26_1402;
    public $image_2_26_1401;
    public $image_2_26_1402;
    public $description_2_26_1401;
    public $description_2_26_1402;

    public $number_2_27_1401;
    public $number_2_27_1402;
    public $image_2_27_1401;
    public $image_2_27_1402;
    public $description_2_27_1401;
    public $description_2_27_1402;

    public $number_2_28_1401;
    public $number_2_28_1402;
    public $image_2_28_1401;
    public $image_2_28_1402;
    public $description_2_28_1401;
    public $description_2_28_1402;

    public $number_2_29_1401;
    public $number_2_29_1402;
    public $image_2_29_1401;
    public $image_2_29_1402;
    public $description_2_29_1401;
    public $description_2_29_1402;

    public $number_2_36_1401;
    public $number_2_36_1402;
    public $image_2_36_1401;
    public $image_2_36_1402;
    public $description_2_36_1401;
    public $description_2_36_1402;

    public $number_2_37_1401;
    public $number_2_37_1402;
    public $image_2_37_1401;
    public $image_2_37_1402;
    public $description_2_37_1401;
    public $description_2_37_1402;

    public $number_2_38_1401;
    public $number_2_38_1402;
    public $image_2_38_1401;
    public $image_2_38_1402;
    public $description_2_38_1401;
    public $description_2_38_1402;

    public $special_3_3_1;

    public $number_2_10_1_1401;
    public $number_2_10_1_1402;
    public $image_2_10_1_1401;
    public $image_2_10_1_1402;
    public $description_2_10_1_1401;
    public $description_2_10_1_1402;

    public $number_2_10_2_1401;
    public $number_2_10_2_1402;
    public $image_2_10_2_1401;
    public $image_2_10_2_1402;
    public $description_2_10_2_1401;
    public $description_2_10_2_1402;

    public $number_2_10_3_1401;
    public $number_2_10_3_1402;
    public $image_2_10_3_1401;
    public $image_2_10_3_1402;
    public $description_2_10_3_1401;
    public $description_2_10_3_1402;

    public $number_2_11_1_1401;
    public $number_2_11_1_1402;
    public $image_2_11_1_1401;
    public $image_2_11_1_1402;
    public $description_2_11_1_1401;
    public $description_2_11_1_1402;

    public $number_2_11_2_1401;
    public $number_2_11_2_1402;
    public $image_2_11_2_1401;
    public $image_2_11_2_1402;
    public $description_2_11_2_1401;
    public $description_2_11_2_1402;

    public $number_2_11_3_1401;
    public $number_2_11_3_1402;
    public $image_2_11_3_1401;
    public $image_2_11_3_1402;
    public $description_2_11_3_1401;
    public $description_2_11_3_1402;

    public $number_2_20_1401;
    public $number_2_20_1402;
    public $image_2_20_1401;
    public $image_2_20_1402;
    public $description_2_20_1401;
    public $description_2_20_1402;

    public $number_3_3_2_1401;
    public $number_3_3_2_1402;
    public $image_3_3_2_1401;
    public $description_3_3_2_1401;
    public $description_3_3_2_1402;
    public $image_3_3_2_1402;
    public $n0;
    public $n1;
    public $n2;
    public $n3;
    public $n4;
    public $n5;
    public $n6;
    public $n7;
    public $n75;
    public $n8;
    public $n9;
    public $n10;
    public $n11;
    public $n12;
    public $n13;
    public $n14;
    public $n15;
    public $n16;
    public $n17;
    public $n18;
    public $n19;
    public $n20;
    public $n21;
    public $n22;
    public $n23;
    public $n24;
    public $n25;
    public $n26;
    public $n27;
    public $n28;
    public $n29;
    public $n30;
    public $n31;
    public $n32;
    public $n33;
    public $n34;
    public function formTutorial(): Action {
        return Action::make('formTutorial')
            ->modalHeading('آموزش پر کردن فرم پژوهانه')
            ->modalDescription(new HtmlString("
        <p>لطفاً پاسخ‌ها را بر اساس تفکیک سال‌های ۱۴۰۱ و ۱۴۰۲ ارائه دهید و مستندات مرتبط را بارگذاری کنید.</p>
        <p>بعد از اتمام سوالات هر صفحه، بر روی دکمه <strong>بعدی</strong> کلیک کنید تا به صفحه بعدی سوالات بروید.</p>
        <p>توجه داشته باشید که اگر پاسخ‌های خود را ذخیره نکنید، باید دوباره به سوالات از ابتدا پاسخ دهید.</p>
        <p>در قسمت <strong>وضعیت فرم پژوهانه</strong> می‌توانید پاسخ‌های خود را اصلاح کنید.</p>
        سوال ها سه نوع مختلف امتیازی ، تعدادی و بله/خیر هستند
    "))
            ->modalCancelAction(false)
            ->modalSubmitAction(false)
            ->modalCloseButton()
            ->modalCancelAction()
            ->modalCancelActionLabel('بستن')
            ;
    }

    protected static string | array $routeMiddleware = [
        UserMiddleware::class,
        PasswordChangedMiddleware::class,
        //when the admin decides , the form filled gets 1 and user cant see this page anymore
        ProcessFinishedMiddleware::class,
    ];

    public function mount(): void
    {
        $formData = FormData::where('user_id', auth()->user()->id)->latest()->first();
        if(!empty($formData)){
            $this->form->fill($formData->toArray());
        }else{
            $this->form->fill();
        }
    }



    public function form(Form $form): Form{
        $dissertations = Dissertation::where('user_id','=',auth()->user()->id)->first();
        if(!empty($dissertations->dissertation_1401)){
            $unFlip401 = json_decode($dissertations->dissertation_1401,true);
            $result401 = array_flip($unFlip401);
            foreach($result401 as $key => $value){
                $result401[$key] = $unFlip401[$value];
            }
        }
        $result401['سایر'] = 'سایر';

        if(!empty($dissertations->dissertation_1402)) {
            $unFlip402 = json_decode($dissertations->dissertation_1402,true);
            $result402 = array_flip($unFlip402);
            foreach($result402 as $key => $value){
                $result402[$key] = $unFlip402[$value];
            }
        }
        $result402['سایر'] = 'سایر';

        $doctorDissertations = DissertationDoctor::where('user_id','=',auth()->user()->id)->first();
        if(!empty($doctorDissertations->dissertation_1401)){
            $unFlip401 = json_decode($dissertations->dissertation_1401,true);
            $doctorResult401 = array_flip($unFlip401);
            foreach($doctorResult401 as $key => $value){
                $doctorResult401[$key] = $unFlip401[$value];
            }
        }
        $doctorResult401['سایر'] = 'سایر';

        if(!empty($doctorDissertations->dissertation_1402)) {
            $unFlip402 = json_decode($doctorDissertations->dissertation_1402,true);
            $doctorResult402 = array_flip($unFlip402);
            foreach($doctorResult402 as $key => $value){
                $doctorResult402[$key] = $unFlip402[$value];
            }
        }
        $doctorResult402['سایر'] = 'سایر';

        return $form->schema([
            Wizard::make([
                Wizard\Step::make('سئوالات امتیازی')
                ->schema([
                    Section::make([
                        TextInput::make('number_2_5_1401')
                            ->label('امتیاز حاصل(طبق سامانه ژیرو) از بروندادهای مشترک بین حوزویان و اعضای موسسه(دارای مصوبه شورای پژوهشی)')
                            ->default(0)
                            ->required()
                            ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ,

                        TextInput::make('number_2_5_1402')
                            ->label('امتیاز حاصل(طبق سامانه ژیرو) از بروندادهای مشترک بین حوزویان و اعضای موسسه(دارای مصوبه شورای پژوهشی)')
                            ->default(0)
                            ->required()
                            ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ,

                        FileUpload::make('image_2_5_1401')
                            ->multiple()
                            ->label('مستندات')
                            ->image()
                                ->maxSize(1000)

                            ->helperText('مربوط به سال ۱۴۰۱')
                            ->imageEditor()
                        ,

                        FileUpload::make('image_2_5_1402')
                            ->multiple()
                            ->helperText('مربوط به ۱۴۰۲')
                            ->image()
                                ->maxSize(1000)
                            ->imageEditor()
                            ->label('مستندات'),



                    ])->columns(2)->description('شماره بند آیین نامه :۵-۲'),

                    Section::make([
                        TextInput::make('number_2_6_1401')
                            ->label('امتیاز حاصل (طبق سامانه ژیرو) از برونداد های مشترک بین دانشگاه فرهنگیان و اعضای موسسه(دارای مصوبه شورای پژوهشی)')
                            ->default(0)
                            ->required()
                            ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ,

                        TextInput::make('number_2_6_1402')
                            ->label('امتیاز حاصل (طبق سامانه ژیرو) از برونداد های مشترک بین دانشگاه فرهنگیان و اعضای موسسه(دارای مصوبه شورای پژوهشی)')
                            ->default(0)
                            ->required()
                            ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ,

                        FileUpload::make('image_2_6_1401')
                            ->multiple()
                            ->label('مستندات')
                            ->image()
                            ->maxSize(1000)
                            ->helperText('مربوط به سال ۱۴۰۱')
                            ->imageEditor()
                        ,

                        FileUpload::make('image_2_6_1402')
                            ->multiple()
                            ->helperText('مربوط به ۱۴۰۲')
                            ->image()
                                ->maxSize(1000)
                            ->imageEditor()
                            ->label('مستندات'),






                    ])->columns(2)->description('شماره بند آیین نامه :۶-۲'),

                    Section::make([
                        TextInput::make('number_2_7_1401')
                            ->label('امتیاز های حاصل (طبق سامانه ژیرو) از برونداد های مشترک برای هر یک از اعضای موسسه های گروه دو و یک (دارای مصوبه شورای پژوهشی) - موسسه گروه یک موسسه ای است که هیئت امنا و ممیزه مستقل دارد. درغیر این صورت گروه ۲ می باشد.')
                            ->required()
                            ->default(0)
                            ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ,
                        TextInput::make('number_2_7_1402')
                            ->label('امتیاز های حاصل (طبق سامانه ژیرو) از برونداد های مشترک برای هر یک از اعضای موسسه های گروه دو و یک (دارای مصوبه شورای پژوهشی) - موسسه گروه یک موسسه ای است که هیئت امنا و ممیزه مستقل دارد. درغیر این صورت گروه ۲ می باشد.')
                            ->required()
                            ->default(0)
                            ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ,

                        FileUpload::make('image_2_7_1401')
                            ->multiple()
                            ->label('مستندات')
                            ->image()
                            ->maxSize(1000)
                            ->helperText('مربوط به سال ۱۴۰۱')
                            ->imageEditor()
                        ,

                        FileUpload::make('image_2_7_1402')
                            ->multiple()
                            ->helperText('مربوط به ۱۴۰۲')
                            ->image()
                            ->maxSize(1000)
                            ->imageEditor()
                            ->label('مستندات'),





                    ])->columns(2)->description('شماره بند آیین نامه : ۷-۲'),

                    Section::make([
                        TextInput::make('number_2_9_1_1401')
                            ->label('امتیاز های حاصل (طبق سامانه ژیرو) از بروندادهای با مشارکت پژوهشگران کشور های همسایه(دارای مصوبه شورای پژوهشی)')
                            ->default(0)
                            ->required()
                            ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                            ->numeric()
                            ->minValue(0)
                                ->maxValue(100)
                            ,

                        TextInput::make('number_2_9_1_1402')
                            ->label('امتیاز های حاصل (طبق سامانه ژیرو) از بروندادهای با مشارکت پژوهشگران کشور های همسایه(دارای مصوبه شورای پژوهشی)')
                            ->default(0)
                            ->required()
                            ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                            ->numeric()
                            ->minValue(0)
                                ->maxValue(100)
                            ,


                        FileUpload::make('image_2_9_1_1401')
                            ->multiple()
                            ->label('مستندات')
                            ->image()
                                ->maxSize(1000)

                            ->helperText('مربوط به سال ۱۴۰۱')
                            ->imageEditor()
                        ,

                        FileUpload::make('image_2_9_1_1402')
                            ->multiple()
                            ->helperText('مربوط به ۱۴۰۲')
                            ->image()
                                ->maxSize(1000)

                            ->imageEditor()
                            ->label('مستندات'),





                    ])->columns(2)->description('شماره بند آیین نامه : ۸-۲'),

                    Section::make([
                        TextInput::make('number_2_9_2_1401')
                            ->label('امتیاز های حاصل (طبق سامانه ژیرو) از برونداد های با مشارکت پژوهشگران کشور های دیگر(دارای مصوبه شورای پژوهشی)')
                            ->default(0)
                            ->required()
                            ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)

                            ,
                        TextInput::make('number_2_9_2_1402')
                            ->label('امتیاز های حاصل (طبق سامانه ژیرو) از برونداد های با مشارکت پژوهشگران کشور های دیگر(دارای مصوبه شورای پژوهشی)')
                            ->default(0)
                            ->required()
                            ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)

                            ,

                        FileUpload::make('image_2_9_2_1401')
                            ->multiple()
                            ->label('مستندات')
                            ->image()
                                ->maxSize(1000)

                            ->helperText('مربوط به سال ۱۴۰۱')
                            ->imageEditor()
                        ,

                        FileUpload::make('image_2_9_2_1402')
                            ->multiple()
                            ->helperText('مربوط به ۱۴۰۲')
                            ->image()
                                ->maxSize(1000)

                            ->imageEditor()
                            ->label('مستندات'),





                    ])->columns(2)->description('شماره بند آیین نامه : ۱-۹-۲'),
                    Section::make([
                        TextInput::make('n7')
                            ->label('امتیاز هر فصل از کتاب تألیفی و تصنیفی مرجع جمعی فارسی طبق دستورالعمل وزارت')
                            ->helperText('توسط کارشناس تکمیل خواهد شد')
                            ->disabled(),
                    ])->columnSpanFull(),
                    Section::make([
                        TextInput::make('n75')
                            ->label('(امتیاز)چنانچه هر فصل از کتاب تألیفی و تصنیفی به زبانهای دیگر ترجمه شود')
                            ->helperText('توسط کارشناس تکمیل خواهد شد')
                            ->disabled(),
                    ])->columnSpanFull(),
                    Section::make([
                        TextInput::make('n8')
                            ->label('امتیازهای حاصل از برونداد های مشترک بین اعضای مستقر دائمی در مؤسسه‌های اقماری با اعضای مؤسسه مادر..')
                            ->helperText('توسط کارشناس تکمیل خواهد شد')
                            ->disabled(),
                    ])->columnSpanFull(),

                    Section::make([
                        TextInput::make('n12')
                            ->label('مرجعیت علمی، عضوی که دارای هر یک از مقام‌های استاد ممتازی یا نشان پژوهش یا دانش باشد')
                            ->helperText('توسط کارشناس تکمیل خواهد شد')
                            ->disabled(),
                    ])->columnSpanFull(),

                    Section::make([
                        TextInput::make('n16')
                            ->label('عضوی که نام وی همزمان در دو فهرست  پژوهشگران  1% پراستناد باشد در پژوهشهای نظری')
                            ->helperText('توسط کارشناس تکمیل خواهد شد')
                            ->disabled(),
                    ])->columnSpanFull(),

                    Section::make([
                        TextInput::make('n27')
                            ->label('برای هر عضو جدید الاستخدام یا تبدیل وضعیت از مربی به استادیاری و یا انتقالی  در پژوهش‌های میدانی')
                            ->helperText('توسط کارشناس تکمیل خواهد شد')
                            ->disabled(),
                    ])->columnSpanFull(),

                    Section::make([
                        TextInput::make('n26')
                            ->label('برای هر عضو جدید الاستخدام یا تبدیل وضعیت از مربی به استادیاری و یا انتقالی  در پژوهش‌های نظری(امتیاز)')

                            ->helperText('توسط کارشناس تکمیل خواهد شد')
                            ->disabled(),
                    ])->columnSpanFull(),

                    Section::make([
                        TextInput::make('n28')
                            ->label('برای هر عضو جدید الاستخدام یا تبدیل وضعیت از مربی به استادیاری و یا انتقالی  در پژوهش‌های تجربی')

                            ->helperText('توسط کارشناس تکمیل خواهد شد')
                            ->disabled(),
                    ])->columnSpanFull(),

                    Section::make([
                        TextInput::make('n34')
                            ->label('به عضوی که دارای امتیاز ارزشیابی تدریس سالانه بالاتر باشد')

                            ->helperText('توسط کارشناس تکمیل خواهد شد')
                            ->disabled(),
                    ])->columnSpanFull(),
                ]),
                Wizard\Step::make('سئوالات بله/خیر')
                    ->schema([
                        Section::make([
                            Toggle::make('number_2_24_1401')
                                ->label('استاد راهنمایی موثر سالانه هر دانشجو دوره کارشناسی')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                                ,
                            Toggle::make('number_2_24_1402')
                                ->label('استاد راهنمایی موثر سالانه هر دانشجو دوره کارشناسی')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                                ,

                            FileUpload::make('image_2_24_1401')
                                ->multiple()
                                ->label('مستندات')
                                ->image()
                                ->maxSize(1000)

                                ->helperText('مربوط به سال ۱۴۰۱')
                                ->imageEditor()
                            ,

                            FileUpload::make('image_2_24_1402')
                                ->multiple()
                                ->helperText('مربوط به ۱۴۰۲')
                                ->image()
                                ->maxSize(1000)

                                ->imageEditor()
                                ->label('مستندات'),






                        ])->columns(2)->description('شماره بند آیین نامه : ۲۴-۲'),

                        Section::make([
                            Toggle::make('number_2_25_1401')
                                ->label('استاد راهنمایی موثر هر انجمن علمی دانشجویی ، فناوری')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                                ,
                            Toggle::make('number_2_25_1402')
                                ->label('استاد راهنمایی موثر هر انجمن علمی دانشجویی ، فناوری')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                                ,

                            FileUpload::make('image_2_25_1401')
                                ->multiple()
                                ->label('مستندات')
                                ->image()
                                ->maxSize(1000)

                                ->helperText('مربوط به سال ۱۴۰۱')
                                ->imageEditor()
                            ,

                            FileUpload::make('image_2_25_1402')
                                ->multiple()
                                ->helperText('مربوط به ۱۴۰۲')
                                ->image()
                                ->maxSize(1000)

                                ->imageEditor()
                                ->label('مستندات'),





                        ])->columns(2)->description('شماره بند آیین نامه : ۲۵-۲'),

                        Section::make([
                            Toggle::make('number_2_26_1401')
                                ->label('استاد راهنمای موثر انجمن علمی دانشجویی/فناوری بوده و در جشنواره ای ملی <<حرکت>>،... مقام کسب کند (سه‌ سال)')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                                ,
                            Toggle::make('number_2_26_1402')
                                ->label('استاد راهنمای موثر انجمن علمی دانشجویی/فناوری بوده و در جشنواره ای ملی <<حرکت>>،... مقام کسب کند (سه سال)')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                                ,

                            FileUpload::make('image_2_26_1401')
                                ->multiple()
                                ->label('مستندات')
                                ->image()
                                ->maxSize(1000)
                                ->helperText('مربوط به سال ۱۴۰۱')
                                ->imageEditor()
                            ,

                            FileUpload::make('image_2_26_1402')
                                ->multiple()
                                ->helperText('مربوط به ۱۴۰۲')
                                ->image()
                                ->maxSize(1000)
                                ->imageEditor()
                                ->label('مستندات'),






                        ])->columns(2)->description('شماره بند آیین نامه : ۲۶-۲'),

                        Section::make([
                            Toggle::make('number_2_27_1401')
                                ->label('سمت مربی و یا سرپرست تیم ورزشی دانشجویی در مسابقه بین المللی مقام اول تا سوم کسب کند(۵ سال)')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                                ,
                            Toggle::make('number_2_27_1402')
                                ->label('سمت مربی و یا سرپرست تیم ورزشی دانشجویی در مسابقه بین المللی مقام اول تا سوم کسب کند(۵ سال)')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                                ,

                            FileUpload::make('image_2_27_1401')
                                ->multiple()
                                ->label('مستندات')
                                ->image()
                                ->maxSize(1000)

                                ->helperText('مربوط به سال ۱۴۰۱')
                                ->imageEditor()
                            ,

                            FileUpload::make('image_2_27_1402')
                                ->multiple()
                                ->helperText('مربوط به ۱۴۰۲')
                                ->image()
                                ->maxSize(1000)

                                ->imageEditor()
                                ->label('مستندات'),


                        ])->columns(2)->description('شماره بند آیین نامه : ۲۷-۲'),

                        Section::make([
                            Toggle::make('number_2_28_1401')
                                ->label('سرپرست تیمی که در مسابقه و با المپیاد علمی دانشجویی رتبه کسب کند(۵ سال)')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                                ,
                            Toggle::make('number_2_28_1402')
                                ->label('سرپرست تیمی که در مسابقه و با المپیاد علمی دانشجویی رتبه کسب کند(۵ سال)')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                                ,

                            FileUpload::make('image_2_28_1401')
                                ->multiple()
                                ->label('مستندات')
                                ->image()
                                ->maxSize(1000)
                                ->helperText('مربوط به سال ۱۴۰۱')
                                ->imageEditor()
                            ,

                            FileUpload::make('image_2_28_1402')
                                ->multiple()
                                ->helperText('مربوط به ۱۴۰۲')
                                ->image()
                                ->maxSize(1000)
                                ->imageEditor()
                                ->label('مستندات'),
                        ])->columns(2)->description('شماره بند آیین نامه : ۲۸-۲'),

                        Section::make([
                            Toggle::make('number_2_29_1401')
                                ->label('مربی یا سرپرست تیم ورزشی دانشجویی در مسابقه ملی مقام اول کسی کند(۳ سال)')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                                ,
                            Toggle::make('number_2_29_1402')
                                ->label('مربی یا سرپرست تیم ورزشی دانشجویی در مسابقه ملی مقام اول کسی کند(۳ سال)')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                                ,

                            FileUpload::make('image_2_29_1401')
                                ->multiple()
                                ->label('مستندات')
                                ->image()
                                ->maxSize(1000)

                                ->helperText('مربوط به سال ۱۴۰۱')
                                ->imageEditor()
                            ,

                            FileUpload::make('image_2_29_1402')
                                ->multiple()
                                ->helperText('مربوط به ۱۴۰۲')
                                ->image()
                                ->maxSize(1000)

                                ->imageEditor()
                                ->label('مستندات'),
                        ])->columns(2)->description('شماره بند آیین نامه : ۲۹-۲'),


                        Section::make([
                            Toggle::make('special_3_3_1')
                                ->label('عضو هیئت علمی خانم ')
                                ->helperText('وضعیت تاهل')
                                ->columnSpanFull(),
                        ])->description('شماره بند آیین نامه : ۳-۳'),

                        Section::make([
                            TextInput::make('n11')
                                ->label('عضو میزبان هر پژوهشگر پسا دکتری ')
                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n13')
                                ->label('پژوهشگران پر استناد 1% ملی (ای اس سی  و یا بین المللی بر اساس وبگاه‌ (ای اس ای) در پژوهش‌های نظری')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n14')
                                ->label('پژوهشگران پر استناد 1% ملی (ای اس سی  و یا بین المللی بر اساس وبگاه‌ (ای اس ای) میدانی')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n15')
                                ->label('پژوهشگران پر استناد 1% ملی (ای اس سی  و یا بین المللی بر اساس وبگاه‌ (ای اس ای) در پژوهش‌های تجربی')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),
                        Section::make([
                            TextInput::make('n17')
                                ->label('عضوی که نام وی همزمان در دو فهرست  پژوهشگران  1% پراستناد باشد در پژوهش‌های میدانی')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n18')
                                ->label('عضوی که نام وی همزمان در دو فهرست  پژوهشگران  1% پراستناد باشد پژوهش‌های تجربی')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n19')
                                ->label('پژوهشگران پر استناد 2% ')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n20')
                                ->label('کسب مقام در جشنواره‌های پژوهشگران و فناوران برتر، فارابی، بین امللی، جوان.... (5 سال)')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n21')
                                ->label('عضوی که موفق به ثبت اختراع ملی با سهم حداقل سی درصد ')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n22')
                                ->label('عضوی که موفق به ثبت اختراع بین المللی و یا تجاری سازی محصول / فرآیند با سهم حداقل بیست درصد')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n23')
                                ->label('عضوی که موفق به ایجاد فناوری منجر به تولید دانش/ تولید خدمت... (5 سال)')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n31')
                                ->label('به عضوی که هیئت ممیزه با ارتقای مرتبه علمی او به اتقاق آرا به دانشیاری موافقت کند.')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n32')
                                ->label('به عضوی که هیئت ممیزه با ارتقای مرتبه علمی او به اتقاق آرا به استادی موافقت کند. ')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n33')
                                ->label('به عضو منتخب سرآمد در هر یک از بخش‌‌های آموزشی، پژوهشی براساس دستورالعمل مصوب هیئت امنا')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),





                    ]),
                Wizard\Step::make('سئوالات تعدادی')
                    ->schema([
                        Section::make([
                            TextInput::make('number_2_10_1_1401')
                                ->label('تعداد پایان نامه دانشجوی کارشناسی ارشد در پژوهش های نظری')
                                ->required()
                                ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->maxValue(100)
                                ,
                            TextInput::make('number_2_10_1_1402')
                                ->label('تعداد پایان نامه دانشجوی کارشناسی ارشد در پژوهش های نظری')
                                ->required()
                                ->default(0)
                                ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ,


                            Select::make('description_2_10_1_1401')
                                ->label('توضیحات')
                                ->options($result401??null)
                                ->multiple()
                                ->reactive()
                                ->helperText('مربوط به سال ۱۴۰۱(هر پایان نامه می تواند فقط متعلق به یکی از سه زیر شاخه نظری ، میدانی و تجربی باشد.)')
                                ,

                            Select::make('description_2_10_1_1402')
                                ->label('توضیحات')
                                ->options($result402??null)
                                ->multiple()
                                ->reactive()
                                ->helperText('مربوط به ۱۴۰۲(هر پایان نامه می تواند فقط متعلق به یکی از سه زیر شاخه نظری ، میدانی و تجربی باشد.)')
                                ,

                            FileUpload::make('image_2_10_1_1401')
                            ->multiple()
                            ->label('مستندات')
                            ->image()
                            ->reactive()
                            ->columnSpanFull()
                            ->maxSize(1000)
                            ->visible(function ($get){
                                if(in_array('سایر',$get('description_2_10_1_1401'))){
                                    return true;
                                }else{
                                    return false;
                                }
                            })
                            ->helperText('مربوط به سال ۱۴۰۱')
                            ->imageEditor()
                        ,

                        FileUpload::make('image_2_10_1_1402')
                            ->multiple()
                            ->helperText('مربوط به ۱۴۰۲')
                            ->image()
                            ->reactive()
                            ->columnSpanFull()

                            ->visible(function ($get){
                                if(in_array('سایر',$get('description_2_10_1_1402'))){
                                    return true;
                                }else{
                                    return false;
                                }
                            })
                            ->maxSize(1000)
                            ->imageEditor()
                            ->label('مستندات'),

                        ])->columns(2)->description('شماره بند آیین نامه : ۱-۱۰-۲'),

                        Section::make([
                            TextInput::make('number_2_10_2_1401')
                                ->label('تعداد پایان نامه دانشجوی کارشناسی ارشد در پژوهش های میدانی')
                                ->required()
                                ->default(0)
                                ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)

                                ,
                            TextInput::make('number_2_10_2_1402')
                                ->label('تعداد پایان نامه دانشجوی کارشناسی ارشد در پژوهش های میدانی')
                                ->required()
                                ->default(0)
                                ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ,

                            Select::make('description_2_10_2_1401')
                                ->label('توضیحات')
                                ->options($result401??null)
                                ->multiple()
                                ->reactive()
                                ->helperText('مربوط به سال ۱۴۰۱(هر پایان نامه می تواند فقط متعلق به یکی از سه زیر شاخه نظری ، میدانی و تجربی باشد.)')
                                ,

                            Select::make('description_2_10_2_1402')
                                ->label('توضیحات')
                                ->options($result402??null)
                                ->multiple()
                                ->reactive()
                                ->helperText('مربوط به ۱۴۰۲(هر پایان نامه می تواند فقط متعلق به یکی از سه زیر شاخه نظری ، میدانی و تجربی باشد.)')
                                ,


                            FileUpload::make('image_2_10_2_1401')
                            ->multiple()
                            ->label('مستندات')
                            ->image()
                            ->maxSize(1000)
                            ->columnSpanFull()
                            ->visible(function ($get){
                                if(in_array('سایر',$get('description_2_10_2_1401'))){
                                    return true;
                                }else{
                                    return false;
                                }
                            })
                                 ->helperText('مربوط به سال ۱۴۰۱')
                            ->imageEditor()
                        ,

                        FileUpload::make('image_2_10_2_1402')
                        ->label('مستندات')

                            ->multiple()
                            ->helperText('مربوط به ۱۴۰۲')
                            ->columnSpanFull()
                            ->image()
                            ->maxSize(1000)
                            ->imageEditor()
                            ->visible(function ($get){
                                if(in_array('سایر',$get('description_2_10_2_1402'))){
                                    return true;
                                }else{
                                    return false;
                                }
                            })

                        ])->columns(2)->description('شماره بند آیین نامه : ۲-۱۰-۲'),

                        Section::make([
                            TextInput::make('number_2_10_3_1401')
                                ->label('تعداد پایان نامه دانشجوی کارشناسی ارشد در پژوهش های تجربی')
                                ->required()
                                ->default(0)
                                ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)

                                ,
                            TextInput::make('number_2_10_3_1402')
                                ->label('تعداد پایان نامه دانشجوی کارشناسی ارشد در پژوهش های تجربی')
                                ->required()
                                ->default(0)
                                ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)

                                ,

                            Select::make('description_2_10_3_1401')
                                ->label('توضیحات')
                                ->options($result401??null)
                                ->multiple()
                                ->reactive()
                                ->helperText('مربوط به سال ۱۴۰۱(هر پایان نامه می تواند فقط متعلق به یکی از سه زیر شاخه نظری ، میدانی و تجربی باشد.)')
                                ,

                            Select::make('description_2_10_3_1402')
                                ->label('توضیحات')
                                ->options($result402??null)
                                ->multiple()
                                ->reactive()
                                ->helperText('مربوط به ۱۴۰۲(هر پایان نامه می تواند فقط متعلق به یکی از سه زیر شاخه نظری ، میدانی و تجربی باشد.)')
                                ,

                            FileUpload::make('image_2_10_3_1401')
                            ->multiple()
                            ->label('مستندات')
                            ->image()
                            ->maxSize(1000)
                            ->columnSpanFull()
                            ->reactive()
                            ->visible(function ($get){
                                if(in_array('سایر',$get('description_2_10_3_1401'))){
                                    return true;
                                }else{
                                    return false;
                                }
                            })
                            ->helperText('مربوط به سال ۱۴۰۱')
                            ->imageEditor()
                        ,

                        FileUpload::make('image_2_10_3_1402')
                            ->multiple()
                            ->helperText('مربوط به ۱۴۰۲')
                            ->image()
                            ->maxSize(1000)
                            ->columnSpanFull()
                            ->reactive()
                            ->visible(function ($get){
                                if(in_array('سایر',$get('description_2_10_3_1402'))){
                                    return true;
                                }else{
                                    return false;
                                }
                            })
                            ->imageEditor()
                            ->label('مستندات'),



                        ])->columns(2)->description('شماره بند آیین نامه : ۳-۱۰-۲'),

                        Section::make([
                            TextInput::make('number_2_11_1_1401')
                                ->label('تعداد رساله دانشجوری دکتری در هر یک از پژوهش های نظری')
                                ->required()
                                ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                                ->default(0)
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ,
                            TextInput::make('number_2_11_1_1402')
                                ->label('تعداد رساله دانشجوری دکتری در هر یک از پژوهش های نظری')
                                ->default(0)
                                ->required()
                                ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ,



                            Select::make('description_2_11_1_1401')
                                ->label('توضیحات')
                            ->options($doctorResult401??null)
                                ->multiple()
                                ->reactive()
                                ->helperText('مربوط به سال ۱۴۰۱(هر پایان نامه می تواند فقط متعلق به یکی از سه زیر شاخه نظری ، میدانی و تجربی باشد.)')
                                ,

                            Select::make('description_2_11_1_1402')
                                ->label('توضیحات')
                            ->options($doctorResult402??null)
                                ->multiple()
                                ->reactive()
                                ->helperText('مربوط به ۱۴۰۲(هر پایان نامه می تواند فقط متعلق به یکی از سه زیر شاخه نظری ، میدانی و تجربی باشد.)')
                                ,
                                FileUpload::make('image_2_11_1_1401')
                                ->multiple()
                                ->label('مستندات')
                                ->image()
                                ->maxSize(1000)
                                ->columnSpanFull()
                                ->reactive()
                                ->visible(function ($get){
                                    if(in_array('سایر',$get('description_2_11_1_1401'))){
                                        return true;
                                    }else{
                                        return false;
                                    }
                                })
                                ->helperText('مربوط به سال ۱۴۰۱')
                                ->imageEditor()
                            ,

                            FileUpload::make('image_2_11_1_1402')
                                ->multiple()
                                ->helperText('مربوط به ۱۴۰۲')
                                ->image()
                                ->maxSize(1000)
                                ->reactive()
                                ->columnSpanFull()
                                ->visible(function ($get){
                                    if(in_array('سایر',$get('description_2_11_1_1402'))){
                                        return true;
                                    }else{
                                        return false;
                                    }
                                })
                                ->imageEditor()
                                ->label('مستندات'),


                        ])->columns(2)->description('شماره بند آیین نامه : ۱-۱۱-۲'),

                        Section::make([
                            TextInput::make('number_2_11_2_1401')
                                ->label('تعداد رساله دانشجوری دکتری در هر یک از پژوهش های میدانی')
                                ->default(0)
                                ->required()
                                ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)

                                ,
                            TextInput::make('number_2_11_2_1402')
                                ->label('تعداد رساله دانشجوری دکتری در هر یک از پژوهش های میدانی')
                                ->default(0)
                                ->required()
                                ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ,


                            Select::make('description_2_11_2_1401')
                                ->label('توضیحات')
                                ->options($doctorResult401??null)
                                ->multiple()
                                ->reactive()
                                ->helperText('مربوط به سال ۱۴۰۱(هر پایان نامه می تواند فقط متعلق به یکی از سه زیر شاخه نظری ، میدانی و تجربی باشد.)')
                                ,

                            Select::make('description_2_11_2_1402')
                                ->label('توضیحات')
                                ->options($doctorResult402??null)
                                ->multiple()
                                ->reactive()
                                ->helperText('مربوط به ۱۴۰۲(هر پایان نامه می تواند فقط متعلق به یکی از سه زیر شاخه نظری ، میدانی و تجربی باشد.)')
                                ,

                            FileUpload::make('image_2_11_2_1401')
                            ->multiple()
                            ->label('مستندات')
                            ->image()
                            ->columnSpanFull()
                            ->maxSize(1000)
                            ->reactive()
                            ->visible(function ($get){
                                if(in_array('سایر',$get('description_2_11_2_1401'))){
                                    return true;
                                }else{
                                    return false;
                                }
                            })
                            ->helperText('مربوط به سال ۱۴۰۱')
                            ->imageEditor()
                        ,

                        FileUpload::make('image_2_11_2_1402')
                            ->multiple()
                            ->helperText('مربوط به ۱۴۰۲')
                            ->image()
                            ->maxSize(1000)
                            ->reactive()
                            ->columnSpanFull()
                            ->visible(function ($get){
                                if(in_array('سایر',$get('description_2_11_2_1402'))){
                                    return true;
                                }else{
                                    return false;
                                }
                            })
                            ->imageEditor()
                            ->label('مستندات'),

                        ])->columns(2)->description('شماره بند آیین نامه : ۲-۱۱-۲'),

                        Section::make([
                            TextInput::make('number_2_11_3_1401')
                                ->label('تعداد رساله دانشجوری دکتری در هر یک از پژوهش های تجربی')
                                ->required()
                                ->default(0)
                                ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ,
                            TextInput::make('number_2_11_3_1402')
                                ->label('تعداد رساله دانشجوری دکتری در هر یک از پژوهش های تجربی')
                                ->required()
                                ->default(0)
                                ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)

                                ,



                            Select::make('description_2_11_3_1401')
                                ->label('توضیحات')
                                ->options($doctorResult401??null)
                                ->reactive()
                                ->multiple()
                                ->helperText('مربوط به سال ۱۴۰۱(هر پایان نامه می تواند فقط متعلق به یکی از سه زیر شاخه نظری ، میدانی و تجربی باشد.)')
                                ,

                            Select::make('description_2_11_3_1402')
                                ->label('توضیحات')
                                ->options($doctorResult402??null)
                                ->multiple()
                                ->reactive()
                                ->helperText('مربوط به ۱۴۰۲(هر پایان نامه می تواند فقط متعلق به یکی از سه زیر شاخه نظری ، میدانی و تجربی باشد.)')
                                ,
                                FileUpload::make('image_2_11_3_1401')
                                ->multiple()
                                ->label('مستندات')
                                ->image()
                                ->columnSpanFull()
                                ->maxSize(1000)
                                ->reactive()
                                ->visible(function ($get){
                                    if(in_array('سایر',$get('description_2_11_3_1401'))){
                                        return true;
                                    }else{
                                        return false;
                                    }
                                })
                                ->helperText('مربوط به سال ۱۴۰۱')
                                ->imageEditor()
                            ,

                            FileUpload::make('image_2_11_3_1402')
                                ->multiple()
                                ->label('مستندات')
                                ->image()
                                ->maxSize(1000)
                                ->columnSpanFull()
                                ->reactive()
                                ->visible(function ($get){
                                    if(in_array('سایر',$get('description_2_11_3_1402'))){
                                        return true;
                                    }else{
                                        return false;
                                    }
                                })
                                ->helperText('مربوط به ۱۴۰۲')
                                ->imageEditor(),

                        ])->columns(2)->description('شماره بند آیین نامه : ۳-۱۱-۲'),

                        Section::make([
                            TextInput::make('number_2_20_1401')
                                ->label(' تعداد دانشجویان تحت راهنمایی که افتخاراتی نظیر سرآمد علمی، جشنواره جوان خوارزمی،...را کسب کرده باشد.')
                                ->default(0)
                                ->required()
                                ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)

                                ,

                            TextInput::make('number_2_20_1402')
                                ->label(' تعداد دانشجویان تحت راهنمایی که افتخاراتی نظیر سرآمد علمی، جشنواره جوان خوارزمی،...را کسب کرده باشد.')
                                ->default(0)
                                ->required()
                                ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)

                                ,

                            FileUpload::make('image_2_20_1401')
                                ->multiple()
                                ->label('مستندات')
                                ->image()
                                ->maxSize(1000)

                                ->helperText('مربوط به سال ۱۴۰۱')
                                ->imageEditor()
                            ,

                            FileUpload::make('image_2_20_1402')
                                ->multiple()
                                ->helperText('مربوط به ۱۴۰۲')
                                ->image()
                                ->maxSize(1000)
                                ->imageEditor()
                                ->label('مستندات'),
                        ])->columns(2)->description('شماره بند آیین نامه : ۲۰-۲'),

                        Section::make([
                            TextInput::make('number_3_3_2_1401')
                                ->label('تعداد فرزندان زیر ۱۸ سال خانم های متاهل')
                                ->required()
                                ->default(0)
                                ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ,

                            TextInput::make('number_3_3_2_1402')
                                ->label('تعداد فرزندان زیر ۱۸ سال خانم های متاهل')
                                ->required()
                                ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                                ->default(0)
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ,

                            FileUpload::make('image_3_3_2_1401')
                                ->multiple()
                                ->label('مستندات')
                                ->image()
                                ->maxSize(1000)
                                ->helperText('مربوط به سال ۱۴۰۱')
                                ->imageEditor(),

                            FileUpload::make('image_3_3_2_1402')
                                ->multiple()
                                ->helperText('مربوط به ۱۴۰۲')
                                ->image()
                                ->maxSize(1000)
                                ->imageEditor()
                                ->label('مستندات'),
                        ])->columns(2)->description('شماره بند آیین نامه : ۳-۳'),

                        Section::make([
                            TextInput::make('number_2_36_1401')
                                ->label('سردبیر و مدیر مسئول نشریه علمی که در هر یک از پایگاه های <<اسکوپوس>> و یا <<واس>> نمایه شده باشد.')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                                ->default(0)
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->required()
                            ,
                            TextInput::make('number_2_36_1402')
                                ->label('سردبیر و مدیر مسئول نشریه علمی که در هر یک از پایگاه های <<اسکوپوس>> و یا <<واس>> نمایه شده باشد.')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                                ->default(0)
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->required()
                            ,
                            FileUpload::make('image_2_36_1401')
                                ->multiple()
                                ->label('مستندات')
                                ->image()
                                ->maxSize(1000)
                                ->helperText('مربوط به سال ۱۴۰۱')
                                ->imageEditor()
                            ,
                            FileUpload::make('image_2_36_1402')
                                ->multiple()
                                ->helperText('مربوط به ۱۴۰۲')
                                ->image()
                                ->maxSize(1000)
                                ->imageEditor()
                                ->label('مستندات'),
                        ])->columns(2)->description('شماره بند آیین نامه : ۲۶-۲'),


                        Section::make([
                            TextInput::make('number_2_37_1401')
                                ->label('سردبیر کمکی نشریه علمی که در هر یک از پایگاه ها <<اسکوپوس >> و یا << واس >> نمایه شده باشد و دارای ضریب تاثیر باشد')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                                ->default(0)
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->required()
                            ,
                            TextInput::make('number_2_37_1402')
                                ->label('سردبیر کمکی نشریه علمی که در هر یک از پایگاه ها <<اسکوپوس >> و یا << واس >> نمایه شده باشد و دارای ضریب تاثیر باشد')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                                ->default(0)
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->required()
                            ,
                            FileUpload::make('image_2_37_1401')
                                ->multiple()
                                ->label('مستندات')
                                ->image()
                                ->maxSize(1000)
                                ->helperText('مربوط به سال ۱۴۰۱')
                                ->imageEditor()
                            ,
                            FileUpload::make('image_2_37_1402')
                                ->multiple()
                                ->helperText('مربوط به ۱۴۰۲')
                                ->image()
                                ->maxSize(1000)
                                ->imageEditor()
                                ->label('مستندات'),
                        ])->columns(2)->description('شماره بند آیین نامه : ۳۷-۲'),


                        Section::make([
                            TextInput::make('number_2_38_1401')
                                ->label('سردبیر کمکی نشریه علمی که در هر یک از پایگاه ها <<اسکوپوس >> و یا << واس >> نمایه شده باشد و دارای ضریب تاثیر باشد')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۱')
                                ->default(0)
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->required()
                            ,
                            TextInput::make('number_2_38_1402')
                                ->label('سردبیر کمکی نشریه علمی که در هر یک از پایگاه ها <<اسکوپوس >> و یا << واس >> نمایه شده باشد و دارای ضریب تاثیر باشد')
                                ->helperText('امتیاز مربوط به سال ۱۴۰۲')
                                ->default(0)
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->required()
                            ,
                            FileUpload::make('image_2_38_1401')
                                ->multiple()
                                ->label('مستندات')
                                ->image()
                                ->maxSize(1000)
                                ->helperText('مربوط به سال ۱۴۰۱')
                                ->imageEditor()
                            ,
                            FileUpload::make('image_2_38_1402')
                                ->multiple()
                                ->helperText('مربوط به ۱۴۰۲')
                                ->image()
                                ->maxSize(1000)
                                ->imageEditor()
                                ->label('مستندات'),
                        ])->columns(2)->description('شماره بند آیین نامه :‌۳۸-۲'),


                        Section::make([
                            TextInput::make('n2')
                                ->label('بروندادهای پژوهش‌های میدانی')
                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n4')
                                ->label('بروندادهای تقاضا محور')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n5')
                                ->label('کتاب تألیفی و تصنیفی فارسی طبق دستوالعمل ارسال وزارت')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n9')
                                ->label('پارسا‌های نیازمحور که موضوعات آنها براساس نیازهای ثبت شده در سامانه نان باشد')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),
                        Section::make([
                            TextInput::make('n10')
                                ->label('پارسا‌های نیازمحور  دانشجویان استعداد درخشان که موضوعات آنها براساس نیازهای ثبت شده در سامانه نان باشد')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),



                        Section::make([
                            TextInput::make('n3')
                                ->label('بروندادهای پژوهش‌های تجربی')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n24')
                                ->label('به ازای ارائة ی هر مورد نظریه پردازی نوین در حوزه علوم انسانی و هنر(5 سال)')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n25')
                                ->label('به ازای سهم کامل هر اثر بدیع هنری، ثبت شده در موزه ملی و بین المللی (5 سال)')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n29')
                                ->label('به عضوی که در حداقل زمان ماندگاری پیش بینی شده براساس آیین نامه ارتقاء به مرتبه دانشیاری ارتقاء یابد')
                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),

                        Section::make([
                            TextInput::make('n30')
                                ->label('به عضوی که در حداقل زمان ماندگاری پیش بینی شده براساس آیین نامه ارتقاء به مرتبه  استادی به مرتبه دانشیاری ارتقاء یابد')

                                ->helperText('توسط کارشناس تکمیل خواهد شد')
                                ->disabled(),
                        ])->columnSpanFull(),
                    ]),
            ])->submitAction(new HtmlString(Blade::render(<<<BLADE
    <x-filament::button type="submit" size="sm">
        ذخیره
    </x-filament::button>
BLADE)))
        ]);
    }


    public function save(): void {
        $inputs = $this->form->getState();
        DB::transaction(function () use ($inputs){
            $questions = Question::where('self_proclaimed', '=', 1)->get();
            foreach ($questions as $question) {
                //parsing $data
                foreach ($inputs as $key => $input) {
                    $yearIs1402 = str_contains($key, '1402');//number_2_5_1402
                    $yearIs1401 = str_contains($key, '1401');
                    $typeIsNumber = str_contains($key, 'number_');
                    $typeIsImage = str_contains($key, 'image_');
                    $typeIsDescription = str_contains($key, 'description_');
                    $typeIsSpecial = str_contains($key,'special_');
                    $result = str_replace(['_1401', '_1402'], '', $key);
                    $number_code = preg_replace('/^(description_|image_|number_|special_)/', '', $result);
                    if ($question->number_code == $number_code) {
                        $answer = Answer::where([['user_id', '=', auth()->user()->id], ['question_id', '=', $number_code]])->first();
                        if (empty($answer)) {
                            //make new answer
                            if ($typeIsDescription) {
                                if ($yearIs1401) {
                                    $created = Answer::create([
                                        'user_id' => auth()->user()->id,
                                        'question_id' => $number_code,
                                        'dissertation_1401' => json_encode($input)
                                    ]);
                                } elseif ($yearIs1402) {
                                    $created = Answer::create([
                                        'user_id' => auth()->user()->id,
                                        'question_id' => $number_code,
                                        'dissertation_1402' => json_encode($input)
                                    ]);
                                }
                            } elseif ($typeIsImage) {
                                if ($yearIs1401) {
                                    $created = Answer::create([
                                        'user_id' => auth()->user()->id,
                                        'question_id' => $number_code,
                                    ]);
                                    foreach($input as $image){
                                        $created->images()->create([
                                            'year' => '1401',
                                            'image_path' => $image
                                        ]);
                                    }
                                } elseif ($yearIs1402) {
                                    $created = Answer::create([
                                        'user_id' => auth()->user()->id,
                                        'question_id' => $number_code,
                                    ]);
                                    $created->images()->create([
                                        'year' => '1402',
                                        'image_path' => $image
                                    ]);
                                }
                            } elseif ($typeIsNumber) {
                                if ($yearIs1401) {
                                    $created = Answer::create([
                                        'user_id' => auth()->user()->id,
                                        'question_id' => $number_code,
                                        'year_1401' => $input
                                    ]);
                                    if($question->grant == 1){
                                        $created->grant_price = (($created->year_1402 + $created->year_1401)/2)*$question->coefficient*8939580;
                                    }elseif($question->grant == 2){
                                        $created->grant_price = (($created->year_1402 + $created->year_1401)/2)*$question->coefficient*210974088;
                                    }
                                    $created->save();
                                } elseif ($yearIs1402) {
                                    $created = Answer::create([
                                        'user_id' => auth()->user()->id,
                                        'question_id' => $number_code,
                                        'year_1402' => $input
                                    ]);
                                    if($question->grant == 1){
                                        $created->grant_price = (($created->year_1402 + $created->year_1401)/2)*$question->coefficient*8939580;
                                    }elseif($question->grant == 2){
                                        $created->grant_price = (($created->year_1402 + $created->year_1401)/2)*$question->coefficient*210974088;
                                    }
                                    $created->save();
                                }
                            } elseif($typeIsSpecial){
                                $created = Answer::create([
                                    'user_id' => auth()->user()->id,
                                    'question_id' => $number_code,
                                    'year_1401' => $input,
                                    'year_1402' => $input,
                                ]);
                            }
                        } else {
                            //update the answer
                            if ($typeIsDescription) {
                                if ($yearIs1401) {
                                    $answer->dissertation_1401 = json_encode($input,true);
                                    $answer->save();
                                } elseif ($yearIs1402) {
                                    $answer->dissertation_1402 = json_encode($input,true);
                                    $answer->save();
                                }
                            } elseif ($typeIsImage) {
                                if ($yearIs1401) {
                                    foreach ($input as $image) {
                                        $answer->images()->create([
                                            'year' => '1401',
                                            'image_path' => $image
                                        ]);
                                    }
                            } elseif ($yearIs1402) {
                                foreach ($input as $image) {
                                    $answer->images()->create([
                                        'year' => '1402',
                                        'image_path' => $image
                                    ]);
                                }
                            }

                            } elseif ($typeIsNumber) {
                                if ($yearIs1401) {
                                    $answer->year_1401 = $input;
                                    $answer->save();
                                    if($question->grant == 1){
                                        $answer->grant_price = (($answer->year_1402 + $answer->year_1401)/2)*$question->coefficient*8939580;
                                    }elseif($question->grant == 2){
                                        $answer->grant_price = (($answer->year_1402 + $answer->year_1401)/2)*$question->coefficient*210974088;
                                    }
                                    $answer->save();
                                } elseif ($yearIs1402) {
                                    $answer->year_1402 = $input;
                                    $answer->save();
                                    if($question->grant == 1){
                                        $answer->grant_price = (($answer->year_1402 + $answer->year_1401)/2)*$question->coefficient*8939580;
                                    }elseif($question->grant == 2){
                                        $answer->grant_price = (($answer->year_1402 + $answer->year_1401)/2)*$question->coefficient*210974088;
                                    }
                                    $answer->save();
                                }
                            } elseif ($typeIsSpecial){
                                $answer->year_1401 = $input;
                                $answer->year_1402 = $input;
                                $answer->save();
                            }
                        }
                    }
                }
            }
            $formData = FormData::where('user_id','=',auth()->user()->id)->first();
            if (empty($formData)){
                FormData::create([
                    'user_id' => auth()->user()->id,
                    'number_2_5_1401' => $inputs['number_2_5_1401'],
                    'number_2_5_1402' => $inputs['number_2_5_1402'],
                    'number_2_6_1401' => $inputs['number_2_6_1401'],
                    'number_2_6_1402' => $inputs['number_2_6_1402'],
                    'number_2_7_1401' => $inputs['number_2_7_1401'],
                    'number_2_7_1402' => $inputs['number_2_7_1402'],
                    'number_2_9_1_1401' => $inputs['number_2_9_1_1401'],
                    'number_2_9_1_1402' => $inputs['number_2_9_1_1402'],
                    'number_2_9_2_1401' => $inputs['number_2_9_2_1401'],
                    'number_2_9_2_1402' => $inputs['number_2_9_2_1402'],
                    'number_2_24_1401' => $inputs['number_2_24_1401'],
                    'number_2_24_1402' => $inputs['number_2_24_1402'],
                    'number_2_25_1401' => $inputs['number_2_25_1401'],
                    'number_2_25_1402' => $inputs['number_2_25_1402'],
                    'number_2_26_1401' => $inputs['number_2_26_1401'],
                    'number_2_26_1402' => $inputs['number_2_26_1402'],
                    'number_2_27_1401' => $inputs['number_2_27_1401'],
                    'number_2_27_1402' => $inputs['number_2_27_1402'],
                    'number_2_28_1401' => $inputs['number_2_28_1401'],
                    'number_2_28_1402' => $inputs['number_2_28_1402'],
                    'number_2_29_1401' => $inputs['number_2_29_1401'],
                    'number_2_29_1402' => $inputs['number_2_29_1402'],
                    'number_2_36_1401' => $inputs['number_2_36_1401'],
                    'number_2_36_1402' => $inputs['number_2_36_1402'],
                    'number_2_37_1401' => $inputs['number_2_37_1401'],
                    'number_2_37_1402' => $inputs['number_2_37_1402'],
                    'number_2_38_1401' => $inputs['number_2_38_1401'],
                    'number_2_38_1402' => $inputs['number_2_38_1402'],
                    'special_3_3_1' => $inputs['special_3_3_1'],
                    'number_2_10_1_1401' => $inputs['number_2_10_1_1401'],
                    'number_2_10_1_1402' => $inputs['number_2_10_1_1402'],
                    'number_2_10_2_1401' => $inputs['number_2_10_2_1401'],
                    'number_2_10_2_1402' => $inputs['number_2_10_2_1402'],
                    'number_2_10_3_1401' => $inputs['number_2_10_3_1401'],
                    'number_2_10_3_1402' => $inputs['number_2_10_3_1402'],
                    'number_2_11_1_1401' => $inputs['number_2_11_1_1401'],
                    'number_2_11_1_1402' => $inputs['number_2_11_1_1402'],
                    'number_2_11_2_1401' => $inputs['number_2_11_2_1401'],
                    'number_2_11_2_1402' => $inputs['number_2_11_2_1402'],
                    'number_2_11_3_1401' => $inputs['number_2_11_3_1401'],
                    'number_2_11_3_1402' => $inputs['number_2_11_3_1402'],
                    'number_2_20_1401' => $inputs['number_2_20_1401'],
                    'number_2_20_1402' => $inputs['number_2_20_1402'],
                    'number_3_3_2_1401' => $inputs['number_3_3_2_1401'],
                    'number_3_3_2_1402' => $inputs['number_3_3_2_1402'],
                ]);
            } else {
                $formData->update([
                    'number_2_5_1401' => $inputs['number_2_5_1401'],
                    'number_2_5_1402' => $inputs['number_2_5_1402'],
                    'number_2_7_1401' => $inputs['number_2_7_1401'],
                    'number_2_7_1402' => $inputs['number_2_7_1402'],
                    'number_2_9_1_1401' => $inputs['number_2_9_1_1401'],
                    'number_2_9_1_1402' => $inputs['number_2_9_1_1402'],
                    'number_2_9_2_1401' => $inputs['number_2_9_2_1401'],
                    'number_2_9_2_1402' => $inputs['number_2_9_2_1402'],
                    'number_2_24_1401' => $inputs['number_2_24_1401'],
                    'number_2_24_1402' => $inputs['number_2_24_1402'],
                    'number_2_25_1401' => $inputs['number_2_25_1401'],
                    'number_2_25_1402' => $inputs['number_2_25_1402'],
                    'number_2_26_1401' => $inputs['number_2_26_1401'],
                    'number_2_26_1402' => $inputs['number_2_26_1402'],
                    'number_2_27_1401' => $inputs['number_2_27_1401'],
                    'number_2_27_1402' => $inputs['number_2_27_1402'],
                    'number_2_28_1401' => $inputs['number_2_28_1401'],
                    'number_2_28_1402' => $inputs['number_2_28_1402'],
                    'number_2_29_1401' => $inputs['number_2_29_1401'],
                    'number_2_29_1402' => $inputs['number_2_29_1402'],
                    'number_2_36_1401' => $inputs['number_2_36_1401'],
                    'number_2_36_1402' => $inputs['number_2_36_1402'],
                    'number_2_37_1401' => $inputs['number_2_37_1401'],
                    'number_2_37_1402' => $inputs['number_2_37_1402'],
                    'number_2_38_1401' => $inputs['number_2_38_1401'],
                    'number_2_38_1402' => $inputs['number_2_38_1402'],
                    'number_2_10_1_1401' => $inputs['number_2_10_1_1401'],
                    'number_2_10_1_1402' => $inputs['number_2_10_1_1402'],
                    'number_2_10_2_1401' => $inputs['number_2_10_2_1401'],
                    'number_2_10_2_1402' => $inputs['number_2_10_2_1402'],
                    'number_2_10_3_1401' => $inputs['number_2_10_3_1401'],
                    'number_2_10_3_1402' => $inputs['number_2_10_3_1402'],
                    'number_2_11_1_1401' => $inputs['number_2_11_1_1401'],
                    'number_2_11_1_1402' => $inputs['number_2_11_1_1402'],
                    'number_2_11_2_1401' => $inputs['number_2_11_2_1401'],
                    'number_2_11_2_1402' => $inputs['number_2_11_2_1402'],
                    'number_2_11_3_1401' => $inputs['number_2_11_3_1401'],
                    'number_2_11_3_1402' => $inputs['number_2_11_3_1402'],
                    'number_2_20_1401' => $inputs['number_2_20_1401'],
                    'number_2_20_1402' => $inputs['number_2_20_1402'],
                    'number_3_3_2_1401' => $inputs['number_3_3_2_1401'],
                    'number_3_3_2_1402' => $inputs['number_3_3_2_1402'],
                    ]);
            }
            auth()->user()->first_time_form_filled = 1;
            auth()->user()->save();
        });


        Notification::make()
                ->success()
                ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
                ->send();
            redirect()->to('/pazhoohane/client-answers');
    }
    public static function canAccess(): bool
    {
        return auth()->user()->role == 'user' and !Hash::check(auth()->user()->national_code, auth()->user()->password) and !auth()->user()->first_time_form_filled;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->role == 'user' and !Hash::check(auth()->user()->national_code, auth()->user()->password) and !auth()->user()->first_time_form_filled;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()->role == 'user' and !Hash::check(auth()->user()->national_code, auth()->user()->password) and !auth()->user()->first_time_form_filled;
    }

}

