<?php

namespace App\Filament\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginResponse implements \Filament\Http\Responses\Auth\Contracts\LoginResponse {

    /**
     * @inheritDoc
     */
    public function toResponse($request) {
        $user = Auth::user();
        if(Hash::check($user->national_code, $user->password) and $user->role == 'user'){
            $url = 'pazhoohane/my-profile';
        }else{
            $url = 'pazhoohane/';
        }
        return redirect()->intended($url);
    }
}
