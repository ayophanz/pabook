<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Hotel;
use App\UserMeta;
use App\Option;
use Storage;


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
                return Hotel::where('owner_id',  $this->ownerId())->orderBy('created_at', 'desc')->get(); 
        }else{
            if($id=='0')
                $id = $this->ownerId();
            $exist_recep = UserMeta::select('value')->where('meta_key', 'assign_to_hotel')->where('user_id', $recep)->get();
            $exist_recep = json_decode(json_encode($exist_recep),true);
            $toarr = array();
            if($exist_recep)
                $toarr = explode(',', substr($exist_recep[0]['value'], 1, -1));
            if($capa=='1') 
                return Hotel::where('status', 'verified')->whereIn('id', $toarr)->where('owner_id', $id)->orderBy('created_at', 'desc')->get();
            else
                return Hotel::where('status', 'verified')->whereNotIn('id', $toarr)->where('owner_id', $id)->orderBy('created_at', 'desc')->get();
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
                'check_out'      => 'required'
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
                    'website'        => $request['website']
                    ];

        if(\Gate::allows('superAdmin')) {
            $data['owner'] = 'required|numeric|min:1';
            $dataCreate['owner_id'] = $request['owner'];
        }

        if(\Gate::allows('hotelOwner'))
            $dataCreate['owner_id'] =  $this->ownerId();

        $this->validate($request, $data, $customMessages);

        $hotel = Hotel::create($dataCreate);

        $this->baseCurrencyExist($hotel->id, $request['base_currency']);
        
		if($request->image) {
			$image =  $hotel->name.'-'.$hotel->id.'.'.explode('/', 
					 explode(':', substr($request->image, 0, 
					 strpos($request->image, ';')))[1])[1];
			\Image::make($request->image)
			->save(public_path('storage/images/upload/hotelImages/').$image);

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
            return Hotel::with('globalBaseCurrency', 'baseCurrency')->where('id', $id)->where('owner_id', $this->ownerId())->first();
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
                'check_out'      => 'required'
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
                      'website'      => $request['website']
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

        $this->baseCurrencyExist($id, $request['base_currency']);

        if(\Gate::allows('superAdmin'))
            return Hotel::where('id', $id)->update($dataUpdate);
        if(\Gate::allows('hotelOwner'))
            return Hotel::where('id', $id)->where('owner_id', $this->ownerId())->update($dataUpdate);
    }

    public function destroy($id) {
    	if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
            return die('not allowed');
        
        $hotel = Hotel::where('id', $id)->first();
        if(\Gate::allows('hotelOwner'))
            $hotel = Hotel::where('id', $id)->where('owner_id', $this->ownerId())->first();

        if($hotel) {
            unlink(storage_path('app/public/images/upload/hotelImages/'.$hotel->image));
            return Hotel::where('id', $id)->delete();
        }
        return die('Something went wrong!');
    }

    /**
    *  Extra function
    */


    /**
    *  Owner Id security verification
    */
    private function ownerId() {
        return auth('api')->user()->id;
    }


    /**
    *  If does have base currency
    */
    private function baseCurrencyExist($id, $base_currency) {
        $option = [
                    'meta_key' => 'base_currency',
                    'meta_value' => $id,
                    'value' => $base_currency
                  ];
        $exist = Option::where('meta_key', 'base_currency')->where('meta_value', $id)->first();
        if($exist) {
            if($base_currency=='use_global')
                Option::where('meta_key', 'base_currency')->where('meta_value', $id)->delete();
            else
                Option::where('meta_key', 'base_currency')->where('meta_value', $id)->update($option);
        }else{
             if($base_currency!='use_global')
                Option::create($option);
        }
    }


    /**
    *  File upload
    */
    private function uploadFile($base64_file, $path, $name) {
        $file = $base64_file;
        $check_base64 = strrpos($file, "base64");
        if($check_base64 > 0) {
            $explode = explode(",", $file);
            $decode_file = base64_decode($explode[1]);
            $file_extension = $this->uf_get_base64_file_extension($explode[0]);
            $filename = $name.'.'.$file_extension ;
            Storage::disk('public')->put($path."/".$filename, $decode_file, "public");
            $url = Storage::url($path."/".$filename);
            return $url;
        }else{
            return $file;
        }
    }

    private function uf_get_base64_file_extension($base64_raw_file) {
        $mime = str_replace(";base64", '', $base64_raw_file);
        $mime = str_replace("data:", '', $mime);
        $extension_arr = [
            "application/zip" => "zip",
            "application/x-zip-compressed" => "zip"
        ];
        return $extension_arr[$mime];
    }

}
