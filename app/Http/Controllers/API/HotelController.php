<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Hotel;
use App\UserMeta;
use App\Helpers\Helpers;

class HotelController extends Controller
{
    public function __construct() {
        $this->middleware(['auth:api', 'verified', 'two_factor_auth']);
    }

    public function index($id=null,$recep=null,$capa=null) {
        if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
            return die('not allowed');

        if($id==null && $recep==null) {
            if(\Gate::allows('superAdmin'))
    		    return Hotel::orderBy('created_at', 'desc')->get();
            if(\Gate::allows('hotelOwner'))
                return Hotel::where('user_id',  Helpers::ownerId())->orderBy('created_at', 'desc')->get(); 
        }else{
            if($id=='0')
                $id = Helpers::ownerId();
            $exist_recep = UserMeta::select('value')->where('meta_key', 'assign_to_hotel')->where('user_id', $recep)->get();
            $exist_recep = json_decode(json_encode($exist_recep),true); 
            $toarr = array();
            if($exist_recep)
                $toarr = explode(',', substr($exist_recep[0]['value'], 1, -1));
            if($capa=='1') 
                return Hotel::where('status', 'verified')->whereIn('id', $toarr)->where('user_id', $id)->orderBy('created_at', 'desc')->get();
            else
                return Hotel::where('status', 'verified')->whereNotIn('id', $toarr)->where('user_id', $id)->orderBy('created_at', 'desc')->get();
        } 	
    }

    public function create(Request $request) {
        if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
            return die('not allowed');

    	$hotel = null;
    	$data = [
                'name'           => 'required|string|max:191||unique:hotels',
                'address'        => 'required|string',
                'city'           => 'required|string|max:191',
                'country'        => 'required|string|max:191',
                'phone_number'   => 'required|string|max:191',
                'email'          => 'required|string|email|max:191|unique:hotels',
                'image'          => 'required|max:307200|image64:jpeg,jpg,png',
                'base_currency'  => 'required|string|max:191',
                'proofFile'      => 'required|max:5242880|image64:x-zip-compressed,zip',
                'check_in'       => 'required',
                'check_out'      => 'required',
                'rooms_no'       => 'required'
                ];
        
        $customMessages = [
                    'proofFile.image64' => 'The :attribute field is invalid, zip file only.'
                    ];          
        
        $dataCreate = [
                    'name'           => $request['name'],
                    'address'        => $request['address'],
                    'city'           => $request['city'],
                    'state_province' => $request['state_province'],
                    'country'        => $request['country'],
                    'zip_code'       => $request['zip_code'],
                    'phone_number'   => $request['phone_number'],
                    'email'          => $request['email'],
                    'check_in'       => $request['check_in'],
                    'check_out'      => $request['check_out'],
                    'website'        => $request['website'],
                    'hotel_rooms_no' => json_encode($request['rooms_no'])
                    ];            

        if(\Gate::allows('superAdmin')) {
            $data['owner'] = 'required|numeric|min:1';
            $dataCreate['user_id'] = (int)$request['owner'];
        }

        if(\Gate::allows('hotelOwner'))
            $dataCreate['user_id'] =  (int)Helpers::ownerId();

        $this->validate($request, $data, $customMessages);

        $hotel = Hotel::create($dataCreate);

        Helpers::baseCurrencyExist($hotel->id, $request['base_currency']);
        
		if($request->image) {
			$image =  $hotel->name.'-'.$hotel->id.'.'.explode('/', 
					 explode(':', substr($request->image, 0, 
					 strpos($request->image, ';')))[1])[1];
			\Image::make($request->image)
			->save(public_path('storage/images/upload/hotelImages/'.$image));

			 Hotel::where('id', $hotel->id)->update(['image'=>$image]);
        }
        
        if($request->proofFile) 
            $this->uploadFile($request->proofFile, "/ImportantFiles", $hotel->id);

		return ( ($hotel!=null)? $hotel : die('Something went wrong!') );
    }

    public function show($id) {
        if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
            return die('not allowed');
            
    	if(\Gate::allows('superAdmin'))
            return Hotel::with('globalBaseCurrency', 'baseCurrency')->where('id', $id)->first();
        if(\Gate::allows('hotelOwner'))
            return Hotel::with('globalBaseCurrency', 'baseCurrency')->where('id', $id)->where('user_id', Helpers::ownerId())->first();
    }

    public function update(Request $request, $id) {
        if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
            return die('not allowed');

    	$data = [
                'name'           => 'required|string|max:191|unique:hotels,name,'.$id,
                'address'        => 'required|string',
                'city'           => 'required|string|max:191',
                'country'        => 'required|string|max:191',
                'phone_number'   => 'required|string|max:191',
                'email'          => 'required|string|email|max:191|unique:hotels,email,'.$id,
                'base_currency'  => 'required|string|max:191',
                'check_in'       => 'required',
                'check_out'      => 'required',
                'rooms_no'       => 'required'
                ];

        $dataUpdate = [
                      'name'           => $request['name'],
                      'address'        => $request['address'],
                      'city'           => $request['city'],
                      'state_province' => $request['state_province'],
                      'country'        => $request['country'],
                      'zip_code'       => $request['zip_code'],
                      'phone_number'   => $request['phone_number'],
                      'email'          => $request['email'],
                      'check_in'       => $request['check_in'],
                      'check_out'      => $request['check_out'],
                      'website'        => $request['website'],
                      'hotel_rooms_no' => $request['rooms_no']
                      ]; 

        if($request['changeCover']) 
            $data['image'] = 'required|image64:jpeg,jpg,png';

        if(\Gate::allows('superAdmin')) {
            $data['owner'] = 'required|numeric|min:1';
            $dataUpdate['user_id'] = $request['owner'];
        }

        $this->validate($request,$data);

        if($request->image && $request['changeCover']) {
            $image = $request['name'].'-'.$id.'.'.explode('/', 
					 explode(':', substr($request->image, 0, 
					 strpos($request->image, ';')))[1])[1];
			\Image::make($request->image)
			->save(public_path('storage/images/upload/hotelImages/').$image);
            $dataUpdate['image'] = $image;
        }

        Helpers::baseCurrencyExist($id, $request['base_currency']);

        if(\Gate::allows('superAdmin'))
            return Hotel::where('id', $id)->update($dataUpdate);
        if(\Gate::allows('hotelOwner'))
            return Hotel::where('id', $id)->where('user_id', Helpers::ownerId())->update($dataUpdate);
    }

    public function approveHotel(Request $request) {
        return (\Gate::allows('superAdmin'))? Hotel::where('id', $request['hotelId'])->update(['status'=>'email_verifying']) : 'error';
    }

    public function destroy($id) {
    	if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
            return die('not allowed');
        
        $hotel = Hotel::where('id', $id)->first();
        if(\Gate::allows('hotelOwner'))
            $hotel = Hotel::where('id', $id)->where('user_id', Helpers::ownerId())->first();

        if($hotel) {
            unlink(storage_path('app/public/images/upload/hotelImages/'.$hotel->image));
            return Hotel::where('id', $id)->delete();
        }
        return die('Something went wrong!');
    }

}
