<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Notifications\EmailVerificationForNewRegistered;
use App\Models\User;
use App\Models\UserMeta;
use App\Helpers\Helpers;

class UserController extends Controller
{
    private $isRecep = false;
    public function __construct() {
        $this->middleware(['auth:api', 'verified', 'two_factor_auth'])->except('checkTwoFactExpired');
    }

    public function index() {
        if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');

        if(\Gate::allows('hotelOwner')) 
            return User::whereIn('id', Helpers::recep())->where('role', 'hotel_receptionist')->orderBy('created_at', 'desc')->get();
        
        if(\Gate::allows('superAdmin'))
		    return User::where('role', '!=', 'super_admin')->orderBy('created_at', 'desc')->get();  	
    }

    public function create(Request $request) {
        if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');

        $user = null;
        $this->isRecep = (($request['role']=='hotel_receptionist')? true : false );
        $data = [
                'fullname' => 'required|string|max:191',
                'email'    => 'required|string|email|max:191|unique:users',
                'role'     => 'required|string|max:191',
                'status'   => 'required|string|max:191'
                ];
        
        $dataCreate = [
                    'name'     => $request['fullname'],
                    'email'    => $request['email'],
                    'role'     => $request['role'],
                    'status'   => $request['status'],
                    'password' => Hash::make('pabook@123')
                    ];
        
        $userMeta = ['meta_key' => 'receptionist_id'];
        
        if(\Gate::allows('superAdmin')) {
            if($this->isRecep) {
                $data['assignTo'] = 'required|numeric|min:1';
                $userMeta['user_id'] = $request['assignTo'];   
            }   
        }

    	$this->validate($request, $data);

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

    public function show($id) {
        if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
            return die('not allowed');

        if(\Gate::allows('hotelOwner')) 
            return User::whereIn('id', Helpers::recep())->where('id', $id)->where('role', 'hotel_receptionist')->first();
        
        if(\Gate::allows('superAdmin'))
            return User::where('id', $id)->where('role', '!=', 'super_admin')->first();
    }

    public function update(Request $request, $id) {
        if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
            return die('not allowed');

        return $this->validateUpdate($request, $id);
    }

    public function destroy($id) {
        if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
            return die('not allowed');

        if(\Gate::allows('hotelOwner'))
            return User::whereIn('id', Helpers::recep())->where('id', $id)->delete();

        if(\Gate::allows('superAdmin')) 
    	   return User::where('id', $id)->delete();
    }

    public function profile() {
        if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner') && !\Gate::allows('hotelReceptionist'))
            return die('not allowed');

        return auth('api')->user();
    }

    public function updateProfile(Request $request) {
        if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner') && !\Gate::allows('hotelReceptionist')) 
            return die('not allowed');

        return $this->validateUpdate($request, auth('api')->user()->id);
    }

    public function hotelOwner() {
        if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
            return die('not allowed');

        if(\Gate::allows('superAdmin'))
            return User::select('id', 'email')->where('role', 'hotel_owner')->get();

        if(\Gate::allows('hotelOwner'))
            return User::select('id', 'email')->where('id', auth('api')->user()->id)->where('role', 'hotel_owner')->first();
    }

    public function hotelReceptionist($id=null) {
        if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
            return die('not allowed');

        if(\Gate::allows('superAdmin')) {
            $recep = UserMeta::select('value')->where('meta_key', 'receptionist_id')->where('user_id', $id)->get()->toArray();
            return User::where('role', 'hotel_receptionist')->where('status', 'active')->whereIn('id', $recep)->get();
        }
        if(\Gate::allows('hotelOwner')) 
            return User::where('role', 'hotel_receptionist')->where('status', 'active')->whereIn('id', Helpers::recep())->get();
    }

    public function recapCap(Request $request, $action) {
        if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');

        $data = [
                'recep' => 'required|numeric|min:1'
                ];

        $hotel_ids = array();
        foreach ($request['assignHotel'] as $key => $value) {
            array_push($hotel_ids, $value['id']);
        }        

        $userMeta = [
                'user_id'  => $request['recep'],
                'meta_key' => 'assign_to_hotel',
                'value'    => json_encode($hotel_ids)
                ];        
        
        $this->validate($request, $data);
                
        if($action=='add') {
            $isMetakeyExist = UserMeta::where('user_id', $request['recep'])->where('meta_key', 'assign_to_hotel')->first();
            if($isMetakeyExist) {
                return UserMeta::where('user_id', $request['recep'])->where('meta_key', 'assign_to_hotel')->update($userMeta);
           }else{
                return UserMeta::where('user_id', $request['recep'])->where('meta_key', 'assign_to_hotel')->create($userMeta);
           }
        }else{
            return UserMeta::where('user_id', $request['recep'])->where('meta_key', 'assign_to_hotel')->update($userMeta);
        }
    }

    /**
     * Custom query
     */
    public function checkTwoFactExpired() {
        $user = auth('api')->user();
        if ($user->two_factor_expiry < \Carbon\Carbon::now()) {
            return 'reload';
        }
        return 'ok';
    }

    /**
    *  Validate update for single user page and user profile
    */
    private function validateUpdate(Request $request, $id) {
        $data = [
                'fullname' => 'required|string|max:191',
                'email'    => 'required|string|email|max:191|unique:users,email,'.$id,
                'role'     => 'required|string|max:191',
                'status'   => 'required|string|max:191'
                ];
        $dataUpdate = [
                      'name'     => $request['fullname'],
                      'email'    => $request['email'],
                      'role'     => $request['role'],
                      'status'   => $request['status']
                      ]; 

        $customMessages = [
                        'match_old_password' => 'The :attribute field doesn\'t match the current password.'
                        ];              

        if($request['changePass']) {
            $data['password'] = 'required|string|min:6';
            $data['old_password'] = 'required|string|min:6|match_old_password:'.$request['old_password'].','.auth('api')->user()->password; 
            $dataUpdate['password'] = Hash::make($request['password']);
        }

        $emailChange = false;
        if($id==auth('api')->user()->id) {
            if($request['email']!=auth('api')->user()->email) {
                $dataUpdate['email_verified_at'] = null;
                $emailChange = true;
            }
        }else{
            if($request['email']!=User::where('id', $id)->first()->email) {
                $dataUpdate['email_verified_at'] = null;
                $emailChange = true;
            }
        }

        $this->validate($request, $data, $customMessages);

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
