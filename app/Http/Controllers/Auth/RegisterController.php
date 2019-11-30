<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Hotel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Notifications\EmailVerificationForNewRegistered;
use App\Notifications\HotelEmailVerification;
use Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except'=> ['ResendVerification','HotelEmailVerification','VerifyHotelEmail']]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
       $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => 'hotel_owner',
            'status' => 'active',
            'password' => Hash::make($data['password']),
        ]);

       $user->notify(new EmailVerificationForNewRegistered());
       return Auth::guard()->login($user);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    public function ResendVerification() {
        Auth::user()->notify(new EmailVerificationForNewRegistered());
        return back();
    }

    public function VerifyHotelEmail($id, $token) {
        hotel::where('id', $id)
        ->where('verify_token', $token)
        ->where('status', 'email_verifying')
        ->update(['status'=>'verified']);
        return back();
    }

    public function HotelEmailVerification($id) {
        $token = $this->str_random(6);
        Auth::user()->notify(new HotelEmailVerification($token));
        hotel::where('id', $id)
        ->where('status', 'email_verifying')
        ->update(['verify_token'=>$token]);
        return back();
    }

    /**
    *  generate random token
    */
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
