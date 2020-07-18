<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Gate\OwnerAndAdminRequest;

use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\Gate\AllRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Notifications\EmailVerificationForNewRegistered;
use App\Models\User;
use App\Models\UserMeta;
use Helpers;

class UserController extends Controller
{
    private $isRecep = false;
    public function __construct() {
        $this->middleware(['auth:api', 'verified', 'two_factor_auth'])->except('checkTwoFactExpired');
    }

    public function index(OwnerAndAdminRequest $ownerAndAdminRequest) 
    {
        if(\Gate::allows('hotelOwner'))  return User::whereIn('id', Helpers::recep())->where('role', 'hotel_receptionist')->orderBy('created_at', 'desc')->get();   
        if(\Gate::allows('superAdmin')) return User::where('role', '!=', 'super_admin')->orderBy('created_at', 'desc')->get();  	
    }

    public function create(OwnerAndAdminRequest $ownerAndAdminRequest, CreateRequest $request) 
    {
        $user = null;
        $this->isRecep = (($request['role']=='hotel_receptionist')? true : false );        
        $dataCreate = [
                    'name'     => $request->fullname,
                    'email'    => $request->email,
                    'role'     => $request->role,
                    'status'   => $request->status,
                    'password' => Hash::make('pabook@123')
                    ];
        
        $userMeta = ['meta_key' => 'receptionist_id'];
        
        if(\Gate::allows('superAdmin')) {
            if($this->isRecep) {
                $userMeta['user_id'] = $request->assignTo;   
            }   
        }
    	$validated = $request->validated();

        if(\Gate::allows('hotelOwner')) {
            $userMeta['user_id'] = auth('api')->user()->id;
            $user = User::where('role', 'hotel_receptionist')->create($dataCreate);
        }

        if(\Gate::allows('superAdmin'))
            $user = User::create($dataCreate);

        if($this->isRecep) {
            $userMeta['value'] = $user->id;
            UserMeta::create($userMeta);
        }
        return ( ($user!=null)? $user : die('something went wrong!') );
    }

    public function show(OwnerAndAdminRequest $ownerAndAdminRequest, $id) 
    {
        if(\Gate::allows('hotelOwner')) return User::whereIn('id', Helpers::recep())->where('id', $id)->where('role', 'hotel_receptionist')->first();   
        if(\Gate::allows('superAdmin')) return User::where('id', $id)->where('role', '!=', 'super_admin')->first();
    }

    public function update(OwnerAndAdminRequest $ownerAndAdminRequest, UpdateRequest $request, $id) 
    {
        $validated = $request->validated();
        return $this->validateUpdate($request, $id);
    }

    public function destroy(OwnerAndAdminRequest $ownerAndAdminRequest, $id) 
    {
        if(\Gate::allows('hotelOwner')) return User::whereIn('id', Helpers::recep())->where('id', $id)->delete();
        if(\Gate::allows('superAdmin')) return User::where('id', $id)->delete();
    }

    public function profile(AllRequest $allRequest) 
    {
        return auth('api')->user();
    }

    public function updateProfile(AllRequest $allRequest, UpdateProfileRequest $request) 
    {
        $validated = $request->validated();
        return $this->validateUpdate($request, auth('api')->user()->id);
    }

    public function hotelOwner(OwnerAndAdminRequest $ownerAndAdminRequest) 
    {
        if(\Gate::allows('superAdmin')) return User::select('id', 'email')->where('role', 'hotel_owner')->get();
        if(\Gate::allows('hotelOwner')) return User::select('id', 'email')->where('id', auth('api')->user()->id)->where('role', 'hotel_owner')->first();
    }

    public function hotelReceptionist(OwnerAndAdminRequest $ownerAndAdminRequest, $id=null) 
    {
        if(\Gate::allows('superAdmin')) {
            $recep = UserMeta::select('value')->where('meta_key', 'receptionist_id')->where('user_id', $id)->get()->toArray();
            return User::where('role', 'hotel_receptionist')->where('status', 'active')->whereIn('id', $recep)->get();
        }
        if(\Gate::allows('hotelOwner')) 
            return User::where('role', 'hotel_receptionist')->where('status', 'active')->whereIn('id', Helpers::recep())->get();
    }

    /**
     * Custom query
     */
    public function checkTwoFactExpired() 
    {
        $user = auth('api')->user();
        if ($user->two_factor_expiry < \Carbon\Carbon::now()) {
            return 'reload';
        }
        return 'ok';
    }

    /**
    *  Validate update for single user page and user profile
    */
    private function validateUpdate($request, $id) 
    {
        $dataUpdate = [
                      'name'     => $request->fullname,
                      'email'    => $request->email,
                      'role'     => $request->role,
                      'status'   => $request->status
                      ]; 

        if($request->changePass) {
            $dataUpdate['password'] = Hash::make($request->password);
        }

        $emailChange = false;
        if($id==auth('api')->user()->id) {
            if($request->email!=auth('api')->user()->email) {
                $dataUpdate['email_verified_at'] = null;
                $emailChange = true;
            }
        }else{
            if($request->email!=User::where('id', $id)->first()->email) {
                $dataUpdate['email_verified_at'] = null;
                $emailChange = true;
            }
        }

        $user = User::where('id', $id)->update($dataUpdate); 
        if($emailChange) {
            if($id==auth('api')->user()->id) {
                auth('api')->user()->refresh();
                auth('api')->user()->notify(new EmailVerificationForNewRegistered());
            }else{
                User::where('id', $id)->first()->notify(new EmailVerificationForNewRegistered());
            }
            return 'refresh';
        }
        return $user;
    }

}
