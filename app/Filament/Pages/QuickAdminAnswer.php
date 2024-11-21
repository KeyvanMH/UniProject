<?php

namespace App\Filament\Pages;

use App\Filament\Const\DefaultConst;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Result;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QuickAdminAnswer extends Page
{
    use InteractsWithForms;

    public ?array $data = [];
    protected static ?string $navigationIcon = 'heroicon-o-pencil';
    protected static ?string $navigationGroup = 'تکمیلی کارشناس';
    protected static ?string $title = "پاسخگویی سئوالات به صورت تجمیعی ";
    protected static ?string $navigationLabel = "پاسخگویی سئوالات به صورت تجمیعی";
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.quick-admin-answer';
    public function mount(): void
    {
        $this->form->fill();
    }
    public function form(Form $form): Form {
        return $form->schema([
            Select::make('user')
                ->searchable()
                ->preload()
                ->required()
                ->label('نام هیئت علمی')
                ->options(function (){
                    $users = User::where('role','user')->get();
                    $array = [];
                    foreach ($users as $user){
                        $array[$user['id']] = $user['name'];
                    }
                    return $array;
                })
                ->afterStateUpdated(function ($get,$set){
                    $answers = Answer::whereHas('question',function (Builder $query){
                        return $query->where('self_proclaimed',0);
                    })->where('user_id',$get('user'))->get();
                    $questions = Question::where('self_proclaimed',0)->pluck('number_code');
                    if($answers !== null){
                        foreach ($questions as $question){
                            foreach ($answers as $answer){
                                $value401 = null ;
                                $value402 = null ;
                                if ($answer->question_id == $question){
                                    $value401 = $answer->year_1401;
                                    $value402 = $answer->year_1402;
                                    break;
                                }
                            }
                            $set('q'.$question.'_1401',$value401 ?? null);
                            $set('q'.$question.'_1402',$value402 ?? null);
                        }
                    }else{
                        foreach ($questions as $question){
                            $set('q'.$question->number_code.'_1401',null);
                            $set('q'.$question->number_code.'_1402',null);
                        }
                    }
                })
                ->reactive(),
            Section::make('سئوالات')->hiddenLabel()->schema([
                Section::make('بروندادهای پژوهش‌های نظری ')->aside()->schema([
                    TextInput::make('q1_2_1_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q1_2_1_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('بروندادهای پژوهش‌های میدانی')->aside()->schema([
                    TextInput::make('q1_2_2_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q1_2_2_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('بروندادهای پژوهش‌های تجربی')->aside()->schema([
                    TextInput::make('q1_2_3_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q1_2_3_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('بروندادهای تقاضا محور')->aside()->schema([
                    TextInput::make('q2_2_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_2_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('کتاب تألیفی و تصنیفی فارسی طبق دستوالعمل ارسال وزارت')->aside()->schema([
                    TextInput::make('q2_3_1_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_3_1_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('ترجمه کتاب فارسی به زبانهای دیگر')->aside()->schema([
                    TextInput::make('q2_3_2_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_3_2_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('امتیاز هر فصل از کتاب تألیفی و تصنیفی مرجع جمعی فارسی طبق دستورالعمل وزارت')->aside()->schema([
                    TextInput::make('q2_4_1_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_4_1_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('چنانچه هر فصل از کتاب تألیفی و تصنیفی به زبانهای دیگر ترجمه شود')->aside()->schema([
                    TextInput::make('q2_4_2_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_4_2_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('امتیازهای حاصل از برونداد های مشترک بین اعضای مستقر دائمی در مؤسسه‌های اقماری با اعضای مؤسسه مادر..')->aside()->schema([
                    TextInput::make('q2_8_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_8_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('پارسا‌های نیازمحور که موضوعات آنها براساس نیازهای ثبت شده در سامانه نان باشد')->aside()->schema([
                    TextInput::make('q2_12_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_12_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('پارسا‌های نیازمحور  دانشجویان استعداد درخشان که موضوعات آنها براساس نیازهای ثبت شده در سامانه نان باشد')->aside()->schema([
                    TextInput::make('q2_13_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_13_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('عضو میزبان هر پژوهشگر پسا دکتری ')->aside()->schema([
                    TextInput::make('q2_14_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_14_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('مرجعیت علمی، عضوی که دارای هر یک از مقام‌های استاد ممتازی یا نشان پژوهش یا دانش باشد')->aside()->schema([
                    TextInput::make('q2_15_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_15_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('پژوهشگران پر استناد 1% ملی (ای اس سی  و یا بین المللی بر اساس وبگاه‌ (ای اس ای) در پژوهش‌های نظری')->aside()->schema([
                    TextInput::make('q2_16_1_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_16_1_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('پژوهشگران پر استناد 1% ملی (ای اس سی  و یا بین المللی بر اساس وبگاه‌ (ای اس ای) میدانی')->aside()->schema([
                    TextInput::make('q2_16_2_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_16_2_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('پژوهشگران پر استناد 1% ملی (ای اس سی  و یا بین المللی بر اساس وبگاه‌ (ای اس ای) در پژوهش‌های تجربی')->aside()->schema([
                    TextInput::make('q2_16_3_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_16_3_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('عضوی که نام وی همزمان در دو فهرست  پژوهشگران  1% پراستناد باشد در پژوهشهای نظری')->aside()->schema([
                    TextInput::make('q2_17_1_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_17_1_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('عضوی که نام وی همزمان در دو فهرست  پژوهشگران  1% پراستناد باشد در پژوهش‌های میدانی')->aside()->schema([
                    TextInput::make('q2_17_2_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_17_2_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('عضوی که نام وی همزمان در دو فهرست  پژوهشگران  1% پراستناد باشد پژوهش‌های تجربی')->aside()->schema([
                    TextInput::make('q2_17_3_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_17_3_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('پژوهشگران پر استناد 2% ')->aside()->schema([
                    TextInput::make('q2_18_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_18_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('کسب مقام در جشنواره‌های پژوهشگران و فناوران برتر، فارابی، بین امللی، جوان.... (5 سال)')->aside()->schema([
                    TextInput::make('q2_19_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_19_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('عضوی که موفق به ثبت اختراع ملی با سهم حداقل سی درصد ')->aside()->schema([
                    TextInput::make('q2_21_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_21_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('عضوی که موفق به ثبت اختراع بین المللی و یا تجاری سازی محصول / فرآیند با سهم حداقل بیست درصد')->aside()->schema([
                    TextInput::make('q2_22_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_22_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('عضوی که موفق به ایجاد فناوری منجر به تولید دانش/ تولید خدمت... (5 سال)')->aside()->schema([
                    TextInput::make('q2_23_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_23_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('به ازای ارائة ی هر مورد نظریه پردازی نوین در حوزه علوم انسانی و هنر(5 سال)')->aside()->schema([
                    TextInput::make('q2_30_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_30_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('به ازای سهم کامل هر اثر بدیع هنری، ثبت شده در موزه ملی و بین المللی (5 سال)')->aside()->schema([
                    TextInput::make('q2_31_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_31_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('برای هر عضو جدید الاستخدام یا تبدیل وضعیت از مربی به استادیاری و یا انتقالی  در پژوهش‌های نظری')->aside()->schema([
                    TextInput::make('q2_32_1_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_32_1_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('برای هر عضو جدید الاستخدام یا تبدیل وضعیت از مربی به استادیاری و یا انتقالی  در پژوهش‌های میدانی')->aside()->schema([
                    TextInput::make('q2_32_2_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_32_2_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('برای هر عضو جدید الاستخدام یا تبدیل وضعیت از مربی به استادیاری و یا انتقالی  در پژوهش‌های تجربی')->aside()->schema([
                    TextInput::make('q2_32_3_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_32_3_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('به عضوی که در حداقل زمان ماندگاری پیش بینی شده براساس آیین نامه ارتقاء به مرتبه دانشیاری ارتقاء یابد')->aside()->schema([
                    TextInput::make('q2_33_1_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_33_1_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('به عضوی که در حداقل زمان ماندگاری پیش بینی شده براساس آیین نامه ارتقاء به مرتبه  استادی به مرتبه دانشیاری ارتقاء یابد')->aside()->schema([
                    TextInput::make('q2_33_2_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_33_2_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('به عضوی که هیئت ممیزه با ارتقای مرتبه علمی او به اتقاق آرا به دانشیاری موافقت کند.')->aside()->schema([
                    TextInput::make('q2_34_1_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_34_1_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('به عضوی که هیئت ممیزه با ارتقای مرتبه علمی او به اتقاق آرا به استادی موافقت کند. ')->aside()->schema([
                    TextInput::make('q2_34_2_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_34_2_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('به عضو منتخب سرآمد در هر یک از بخش‌‌های آموزشی، پژوهشی براساس دستورالعمل مصوب هیئت امنا')->aside()->schema([
                    TextInput::make('q2_35_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_35_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),

                Section::make('به عضوی که دارای امتیاز ارزشیابی تدریس سالانه بالاتر باشد')->aside()->schema([
                    TextInput::make('q2_39_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                    TextInput::make('q2_39_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ,
                ])->columns(2),
            ]),
            ])->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function save(): void {
        $data = $this->form->getState();
        $user = $data['user'];
        unset($data['user']);
        foreach($data as $key => $value){
            if ($value == null){
                unset($data[$key]);
            }
        }
        DB::transaction(function () use ($data,$user){
            foreach ($data as $key => $value){
                //fetch question id
                $temp = preg_replace('/(_1401|_1402)/','',$key);
                $question = str_replace('q','',$temp);
                //fetch year
                $yearIs1401 = str_contains($key, '1401');
                //fetch answer and old grant price
                $answer = Answer::where([['user_id','=',$user],['question_id','=',$question]])->first();
                $questionData = Question::where('number_code','=',$question)->first();
                if($answer !== null){
                    //update answer
                    $oldGrantPrice = $answer->grant_price;
                    if($yearIs1401){
                        $answer->year_1401 = $value;
                        if ($questionData->grant == 1){
                            $answer->grant_price = (($value+$answer->year_1402)/2) * $questionData->coefficient * DefaultConst::grantOne;
                        }elseif ($questionData->grant == 2){
                            $answer->grant_price = (($value + $answer->year_1402)/2) * $questionData->coefficient * DefaultConst::grantTwo;
                        }
                    }else{
                        $answer->year_1402 = $value;
                        if ($questionData->grant == 1){
                            $answer->grant_price = (($value + $answer->year_1401)/2) * $questionData->coefficient * DefaultConst::grantOne;
                        }elseif ($questionData->grant == 2){
                            $answer->grant_price = (($value + $answer->year_1401)/2) * $questionData->coefficient * DefaultConst::grantTwo;
                        }
                    }
                    $answer->save();
                    //update result
                    $result = Result::where('user_id','=',$user)->first();
                    $result->sum_price = $result->sum_price - $oldGrantPrice;
                    $result->sum_price = $result->sum_price + $answer->grant_price;
                    $result->total_grant_price = $result->sum_price + ($result->sum_price*$result->child_number) + ($result->is_married_woman * $result->sum_price);
                    $result->save();
                }else{
                    if($yearIs1401){
                        //create answer
                        if ($questionData->grant == 1) {
                            $answer = Answer::create([
                                'user_id' => $user,
                                'question_id' => $question,
                                'year_1401' => $value,
                                'year_1402' => 0,
                                'admin_approval' => 1,
                                'grant_price' => ($value/2) * $questionData->conefficient * DefaultConst::grantOne,
//                                'admin_response' => 'تکمیل توسط کارشناس',
                            ]);
                        }elseif($questionData->grant == 2){
                            $answer = Answer::create([
                                'user_id' => $user,
                                'question_id' => $question,
                                'year_1401' => $value,
                                'year_1402' => 0,
                                'admin_approval' => 1,
                                'grant_price' => ($value/2)* $questionData->conefficient * DefaultConst::grantTwo,
//                                'admin_response' => 'تکمیل توسط کارشناس',
                            ]);
                        }
                    }else{
                        //create answer
                        if ($questionData->grant == 1) {
                            $answer = Answer::create([
                                'user_id' => $user,
                                'question_id' => $question,
                                'year_1401' => 0,
                                'year_1402' => $value,
                                'admin_approval' => 1,
                                'grant_price' => ($value/2)* $questionData->conefficient * DefaultConst::grantOne,
//                                'admin_response' => 'تکمیل توسط کارشناس',
                            ]);
                        }elseif($questionData->grant == 2){
                            $answer = Answer::create([
                                'user_id' => $user,
                                'question_id' => $question,
                                'year_1401' => 0,
                                'year_1402' => $value,
                                'admin_approval' => 1,
                                //todo
                                'grant_price' => ($value/2)* $questionData->conefficient * DefaultConst::grantTwo,
//                                'admin_response' => 'تکمیل توسط کارشناس',
                            ]);
                        }
                    }
                    $result = Result::where('user_id','=',$user)->first();
                    if ($result){
                        //update result
                        $result->sum_price += $answer->grant_price;
                        $result->total_grant_price = $result->sum_price + ($result->sum_price*$result->child_number) + ($result->is_married_woman * $result->sum_price);
                        $result->save();
                    }else{
                        //create result
                        Result::create([
                            'user_id' => $user,
                            'sum_price' => $answer->grant_price,
                            'total_grant_price' => $answer->grant_price
                        ]);
                    }
                }
            }
            $answerCount = Answer::where([['admin_approval','=',1],['user_id','=',$user]])->get();
            info(count($answerCount));
            if(count($answerCount) == 58){
                $progress_finished = User::find($user);
                $progress_finished->progress_finished = 1;
                $progress_finished->save();
            }
        });
        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();
        // calculate progress finished
    }
    public static function canAccess(): bool {
        return auth()->user()->role == 'admin';
    }

    public static function canView(Model $record): bool {
        return auth()->user()->role == 'admin';
    }
}
