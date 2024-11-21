<?php

namespace App\Filament\Pages;

use App\Filament\Const\DefaultConst;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Result;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminsAnswer extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static ?string $navigationIcon = 'heroicon-o-pencil';

    protected static string $view = 'filament.pages.admins-answer';
    protected static ?string $title = "پاسخگویی سئوالات به صورت تکی";
    protected static ?string $navigationLabel = "پاسخگویی سئوالات به صورت تکی";
    protected static ?string $navigationGroup = 'تکمیلی کارشناس';
    protected static ?int $navigationSort = 1;
    protected static string | array $routeMiddleware = [
        AdminMiddleware::class,
    ];
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user')
                    ->required()
                    ->searchable()
                    ->preload()
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
                        if($get('user') !== null and $get('question') !== null){
                            $answer = Answer::where([['user_id','=',$get('user')],['question_id','=',$get('question')]])->first();
                            if ($answer){
                                $set('year_1401',$answer->year_1401);
                                $set('year_1402', $answer->year_1402);
                            }else{
                                $set('year_1401',null);
                                $set('year_1402', null);
                            }
                        }
                    })
                    ->reactive(),

                Select::make('question')
                    ->reactive()
                    ->label('سئوال')
                    ->required()
                    ->afterStateUpdated(function ($get,$set){
                        if($get('user') !== null and $get('question') !== null){
                            $answer = Answer::where([['user_id','=',$get('user')],['question_id','=',$get('question')]])->first();
                            if ($answer){
                                $set('year_1401',$answer->year_1401);
                                $set('year_1402', $answer->year_1402);
                            }else{
                                $set('year_1401',null);
                                $set('year_1402', null);
                            }
                        }
                    })
                    ->options(fn ($get) => AdminsAnswer::getQuestions()),

                Section::make('پاسخ')->schema([
                    TextInput::make('year_1401')
                        ->label('سال ۱۴۰۱')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ->required(),

                    TextInput::make('year_1402')
                        ->label('سال ۱۴۰۲')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->reactive()
                        ->required(),
                ])->columns(2)
            ])->statePath('data');
    }
    protected static function getQuestions()
    {
        $question = Question::where('self_proclaimed','=',0)->pluck('number_code','description');
        $array = array();
        foreach ($question as $key => $value){
            $array[$value] = $key;
        }
        return $array;
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
        DB::transaction(function ()use($data){
            $question = Question::where('number_code',$data['question'])->first();
            $grant = (($data['year_1401'] + $data['year_1402'])/2)*$question->coefficient;
            switch ($question->grant){
                case 1:
                    $grant_price = $grant*DefaultConst::grantOne;
                    break;
                case 2:
                    $grant_price = $grant*210974088;
                    break;
                default:
                    $grant_price = 0;
            }
            $result = Result::where('user_id',$data['user'])->first();
            $answer = Answer::where([['user_id','=',$data['user']],['question_id','=',$data['question']]])->first();
            if(!empty($answer)){
                //update
                $result->sum_price -= $answer->grant_price;

                $answer->year_1401 = $data['year_1401'];
                $answer->year_1402 = $data['year_1402'];
                $answer->admin_response = 'تکمیل توسط کارشناس';
                $answer->admin_approval = 1;
                $answer->grant_price = $grant_price;
                $answer->save();
                //result update
                $result->sum_price += $answer->grant_price;
                $result->total_grant_price = $result->sum_price + ($result->sum_price*$result->child_number) + ($result->is_married_woman * $result->sum_price);
                $result->save();
            }else{
                //create
                $answer = Answer::create([
                    'user_id' => $data['user'],
                    'question_id' => $data['question'],
                    'year_1401' => $data['year_1401'],
                    'year_1402' => $data['year_1402'],
                    'admin_response' => 'تکمیل توسط کارشناس',
                    'admin_approval' => 1,
                    'grant_price' => $grant_price
                ]);
                //create or update result table
                if(!empty($result)){
                    $result->sum_price += $answer->grant_price;
                    $result->total_grant_price = $result->sum_price + ($result->sum_price*$result->child_number) + ($result->is_married_woman * $result->sum_price);
                    $result->save();
                }else{
                    //create result table
                    $result = Result::create([
                        'user_id' => $data['user'],
                       'sum_price' => $answer->grant_price,
                       'total_grant_price' => $answer->grant_price,
                    ]);
                }
                $answerCount = Answer::where([['admin_approval','=',1],['user_id','=',$data['user']]])->get();
                if(count($answerCount) == 58){
                    User::find($data['user'])->progress_finished = 1;
                }
            }
        });

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();
    }
    public static function canAccess(): bool {
        return auth()->user()->role == 'admin';
    }

    public static function canView(Model $record): bool {
        return auth()->user()->role == 'admin';
    }

}
