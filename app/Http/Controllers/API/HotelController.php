<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Hotel\CreateRequest;
use App\Http\Requests\Hotel\UpdateRequest;
use App\Http\Requests\Gate\AllRequest;
use App\Http\Requests\Gate\OwnerAndAdminRequest;
use App\Http\Requests\Gate\ApproveRequest;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Helpers;

class HotelController extends Controller
{
    public function __construct() {
        $this->middleware(['auth:api', 'verified', 'two_factor_auth']);
    }

    public function index(OwnerAndAdminRequest $ownerAndAdminRequest) {
        if (\Gate::allows('superAdmin')) return Hotel::orderBy('created_at', 'desc')->get();
        if (\Gate::allows('hotelOwner')) return Hotel::where('user_id',  Helpers::ownerId())->orderBy('created_at', 'desc')->get(); 	
    }

    public function create(OwnerAndAdminRequest $ownerAndAdminRequest, CreateRequest $request) {
        
    	$hotel = null;   
        $dataCreate = [
                    'name'           => $request->name,
                    'address'        => $request->address,
                    'city'           => $request->city,
                    'state_province' => $request->state_province,
                    'country'        => $request->country,
                    'zip_code'       => $request->zip_code,
                    'phone_number'   => $request->phone_number,
                    'email'          => $request->email,
                    'check_in'       => $request->check_in,
                    'check_out'      => $request->check_out,
                    'website'        => $request->website,
                    'hotel_rooms_no' => json_encode($request->rooms_no)
                    ];            

        if (\Gate::allows('superAdmin')) $dataCreate['user_id'] = (int)$request->owner;

        if (\Gate::allows('hotelOwner')) $dataCreate['user_id'] =  (int)Helpers::ownerId();

        $validated = $request->validated();

        $hotel = Hotel::create($dataCreate);

        Helpers::baseCurrencyExist($hotel->id, $request->base_currency);
        
		if ($request->image) {
			$image =  $hotel->name.'-'.$hotel->id.'.'.explode('/', 
					 explode(':', substr($request->image, 0, 
					 strpos($request->image, ';')))[1])[1];
			\Image::make($request->image)
			->save(public_path('storage/images/upload/hotelImages/'.$image));

			 Hotel::where('id', $hotel->id)->update(['image'=>$image]);
        }
        
        if( $request->proofFile) 
            Helpers::uploadFile($request->proofFile, "/ImportantFiles", $hotel->id);

		return ( ($hotel!=null)? $hotel : die('Something went wrong!') );
    }

    public function show(OwnerAndAdminRequest $ownerAndAdminRequest, $id) {
            
    	if (\Gate::allows('superAdmin')) return Hotel::with('globalBaseCurrency', 'baseCurrency')->where('id', $id)->first();
        if (\Gate::allows('hotelOwner')) return Hotel::with('globalBaseCurrency', 'baseCurrency')->where('id', $id)->where('user_id', Helpers::ownerId())->first();
    }

    public function update(OwnerAndAdminRequest $ownerAndAdminRequest, UpdateRequest $request, $id) {

        $dataUpdate = [
                      'name'           => $request->name,
                      'address'        => $request->address,
                      'city'           => $request->city,
                      'state_province' => $request->state_province,
                      'country'        => $request->country,
                      'zip_code'       => $request->zip_code,
                      'phone_number'   => $request->phone_number,
                      'email'          => $request->email,
                      'check_in'       => $request->check_in,
                      'check_out'      => $request->check_out,
                      'website'        => $request->website,
                      'hotel_rooms_no' => $request->rooms_no
                      ]; 

        if (\Gate::allows('superAdmin'))  $dataUpdate['user_id'] = $request->owner;

        $validated = $request->validated();

        if ($request->image && $request->changeCover) {
            $image = $request['name'].'-'.$id.'.'.explode('/', 
					 explode(':', substr($request->image, 0, 
					 strpos($request->image, ';')))[1])[1];
			\Image::make($request->image)
			->save(public_path('storage/images/upload/hotelImages/').$image);
            $dataUpdate['image'] = $image;
        }

        Helpers::baseCurrencyExist($id, $request->base_currency);

        if (\Gate::allows('superAdmin'))  return Hotel::where('id', $id)->update($dataUpdate);
        if (\Gate::allows('hotelOwner'))  return Hotel::where('id', $id)->where('user_id', Helpers::ownerId())->update($dataUpdate);
    }

    public function approveHotel(ApproveRequest $request) {
        return (\Gate::allows('superAdmin'))? Hotel::where('id', $request->hotelId)->update(['status'=>'email_verifying']) : 'error';
    }

    public function destroy(OwnerAndAdminRequest $ownerAndAdminRequest, $id) {
        
        $hotel = Hotel::where('id', $id)->first();
        if (\Gate::allows('hotelOwner'))  $hotel = Hotel::where('id', $id)->where('user_id', Helpers::ownerId())->first();

        if ($hotel) {
            unlink(storage_path('app/public/images/upload/hotelImages/'.$hotel->image));
            return Hotel::where('id', $id)->delete();
        }
        return die('Something went wrong!');
    }
}
