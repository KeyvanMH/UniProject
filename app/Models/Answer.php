<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function question() {
        return $this->belongsTo(Question::class,'question_id','number_code');
    }
    public function images(){
        return $this->hasMany(Image::class);
    }
    public function images401(){
        return $this->images()->where('year','=','1401');
    }
    public function images402(){
        return $this->images()->where('year','=','1402');
    }

}
