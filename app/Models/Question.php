<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
      'number_code','grant','coefficient','self_proclaimed'
    ];
    public function answer(){
        return $this->hasMany(Answer::class);
    }
}
