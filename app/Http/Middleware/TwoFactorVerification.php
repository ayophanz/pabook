<?php

namespace App\Http\Middleware;

use Closure;
use App\Notifications\TwoFactorAuthentication;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class TwoFactorVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $user = auth()->user();
        if ($user->two_factor_expiry > \Carbon\Carbon::now()) {
            return $next($request);
        }

        if ($request->user() || (!$request->user() instanceof MustVerifyEmail && $request->user()->hasVerifiedEmail())) { 
            $request->user()->two_factor_token = $this->str_random(10);
            $request->user()->save();
            $request->user()->notify(new TwoFactorAuthentication($request->user()->two_factor_token)); 
        }
        return redirect('/2fa');
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
