<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Hotel;
use App\UserMeta;

class HotelController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index($id=null,$recep=null,$capa=null) {
        if($id==null && $recep==null) {
            if(\Gate::allows('superAdmin'))
    		    return Hotel::orderBy('created_at', 'desc')->get();
            if(\Gate::allows('hotelOwner'))
                return Hotel::where('owner_id',  $this->ownerId())->orderBy('created_at', 'desc')->get(); 
        }else{
            if(\Gate::allows('superAdmin')) {
                $exist_recep = UserMeta::select('value')->where('meta_key', 'assign_to_hotel')->where('user_id', $recep)->get();
                $exist_recep = json_decode(json_encode($exist_recep),true);
                $toarr = array();
                if($exist_recep)
                    $toarr = explode(',', substr($exist_recep[0]['value'], 1, -1));
                if($capa=='1') 
                    return Hotel::whereIn('id', $toarr)->where('owner_id', $id)->orderBy('created_at', 'desc')->get();
                else
                    return Hotel::whereNotIn('id', $toarr)->where('owner_id', $id)->orderBy('created_at', 'desc')->get();
            }
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
                'image'          => 'required|image64:jpeg,jpg,png'
                ];
        
        $dataCreate = [
                    'name'           => $request['name'],
                    'address'        => $request['address'],
                    'city'           => $request['city'],
                    'state_province' => $request['state_province'],
                    'country'        => $request['country'],
                    'zip_code'       => $request['zip_code'],
                    'phone_number'   => $request['phone_number'],
                    'email'          => $request['email']
                    ];

        if(\Gate::allows('superAdmin')) {
            $data['owner'] = 'required|numeric|min:1';
            $dataCreate['owner_id'] = $request['owner'];
        }

        if(\Gate::allows('hotelOwner'))
            $dataCreate['owner_id'] =  $this->ownerId();

        $this->validate($request, $data);

        $hotel = Hotel::create($dataCreate);
        
		if($request->image) {
			$image =  $hotel->name.'-'.$hotel->id.'.'.explode('/', 
					 explode(':', substr($request->image, 0, 
					 strpos($request->image, ';')))[1])[1];
			\Image::make($request->image)
			->save(public_path('storage/images/upload/hotelImages/').$image);

			 Hotel::where('id', $hotel->id)->update(['image'=>$image]);
		}

		return ( ($hotel!=null)? $hotel : die('Something went wrong!') );
    }

    public function show($id) {
    	if(\Gate::allows('superAdmin'))
            return Hotel::where('id', $id)->first();
        if(\Gate::allows('hotelOwner'))
            return Hotel::where('id', $id)->where('owner_id', $this->ownerId())->first();
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
                'email'          => 'required|string|email|max:191|unique:hotels,email,'.$id
                ];
        $dataUpdate = [
                      'name'           => $request['name'],
                      'address'        => $request['address'],
                      'city'           => $request['city'],
                      'state_province' => $request['state_province'],
                      'country'        => $request['country'],
                      'zip_code'       => $request['zip_code'],
                      'phone_number'   => $request['phone_number'],
                      'email'          => $request['email']
                      ]; 

        if($request['changeCover']) 
            $data['image'] = 'required|image64:jpeg,jpg,png';

        if(\Gate::allows('superAdmin')) {
            $data['owner'] = 'required|numeric|min:1';
            $dataUpdate['owner_id'] = $request['owner'];
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

        if(\Gate::allows('superAdmin'))
            return Hotel::where('id', $id)->update($dataUpdate);
        if(\Gate::allows('hotelOwner'))
            return Hotel::where('id', $id)->where('owner_id', $this->ownerId())->update($dataUpdate);
    }

    public function destroy($id) {
    	if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner')) {
            $hotel = Hotel::where('id', $id)->first();
            if(\Gate::allows('hotelOwner'))
                $hotel = Hotel::where('id', $id)->where('owner_id', $this->ownerId())->first();

            if($hotel) {
                unlink(storage_path('app/public/images/upload/hotelImages/'.$hotel->image));
                return Hotel::where('id', $id)->delete();
            }
            return die('Something went wrong!');
        }
    }


    /**
    *  Extra function
    */


    /**
    *  Owner Id security verification
    */
    public function ownerId() {
        return auth('api')->user()->id;
    }

}
