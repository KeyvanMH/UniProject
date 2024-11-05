<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Dissertation extends Model
{
    use HasFactory;
//    protected function casts() {
//    }

    protected $guarded = [];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
