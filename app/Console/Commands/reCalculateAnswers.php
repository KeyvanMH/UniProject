<?php

namespace App\Console\Commands;

use App\Filament\Const\DefaultConst;
use App\Models\Answer;
use Illuminate\Console\Command;

class reCalculateAnswers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:re-calculate-answers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle() {
        $answers = Answer::whereHas('question', function($query) {
            return $query->where('grant', '=', 2);
        })->get();
        $array = [];
        foreach ($answers as $answer) {
            // If the entry for this user_id and question_id already exists, skip or update it
                $temp = [];
                // Logic to calculate grant_price
                $temp['id'] = $answer->id;
                $temp['user_id'] = $answer->user_id;
                $temp['question_id'] = $answer->question_id;
                $temp['year_1401'] = $answer->year_1401;
                $temp['year_1402'] = $answer->year_1402;
                $temp['grant_price'] = (($answer->year_1401 + $answer->year_1402) / 2) * $answer->question->coefficient * DefaultConst::grantTwo;
                $temp['admin_approval'] = 0;
                $temp['admin_response'] = null;

                // Store the temp data in the array with the unique key
                $array[] = $temp;

        }
        // Ensure the combination of 'user_id' and 'question_id' is unique
        Answer::upsert($array, ['id'], ['grant_price', 'admin_approval', 'admin_response']);
    }

}
