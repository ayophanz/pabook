<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\TwoFactorAuthentication;

class TwoFactorController extends Controller
{
    // show the two factor auth form
    public function show2faForm() {
        return view('auth.2fa');
    }
    // post token to the backend for check
    public function verifyToken(Request $request) {
        $this->validate($request, ['token' => 'required']);
        $user = auth()->user();
        if ($request->token == $user->two_factor_token) {
            $user->two_factor_expiry = \Carbon\Carbon::now()->addMinutes(config('session.lifetime'));
            $user->save();
            return redirect()->intended('/');
        }
        return redirect('/2fa')->with('message', 'Incorrect token.');
    }

    public function ResendVerifyToken() {
       $user = auth()->user();
       $user->two_factor_token = $this->str_random(6);
       $user->save();
       $user->notify(new TwoFactorAuthentication($user->two_factor_token));
       return back();
    }

    function str_random($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
