<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Room;
use App\RoomMeta;

class RoomController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
   }

   public function index() {
        if(\Gate::allows('superAdmin'))
		    return Room::with('roomRefer')->orderBy('created_at', 'desc')->get();
   }

   public function create(Request $request) {
   	  $room = null;
   	  $data = [
              'name' 	    => 'required|string|max:191|unique_name:rooms,name,type_id,'.$request['type'].',0',
              'type'        => 'required|numeric|min:1',
              'price'       => 'required|min:1|regex:/^\d+(\.\d{1,2})?$/',
              'no_of_room'  => 'required|numeric|min:1',
              'hotel'       => 'required|numeric|min:1',
              'image'       => 'required|image64:jpeg,jpg,png'
              ];

      $customMessages = [
                        'unique_name' => 'The :attribute field is already exist in the same room type.'
                        ];        
                        
      $dataCreate = [
                    'name'        => $request['name'],
                    'type_id'     => $request['type'],
                    'description' => $request['description'],
                    'price'       => $request['price'],
                    'total_room'  => $request['no_of_room']
                    ];                     

      $this->validate($request, $data, $customMessages);
      
      if(\Gate::allows('superAdmin')) 
        $room = Room::create($dataCreate);
         

      if($request->image) {
        $folder = public_path().'/storage/images/upload/roomImages/gallery-'.$room->id.'/';
        if (!File::exists($folder)) 
            File::makeDirectory($folder, 0775, true);

  			$image =  $room->name.'-'.$room->id.'.'.explode('/', 
  					 explode(':', substr($request->image, 0, 
  					 strpos($request->image, ';')))[1])[1];
  			\Image::make($request->image)
  			->save(public_path('storage/images/upload/roomImages/gallery-'.$room->id.'/').$image);

  			 Room::where('id', $room->id)->update(['image'=>$image]);
  	  }	

      $featureDataTemp = array_filter($request->featureData, function($v) { return !is_null($v['value']); });
      if($featureDataTemp) {
        $dataMetaCreate = [
                          'room_id'  => $room->id,
                          'meta_key' => 'room_feature',
                          'value'    => json_encode($featureDataTemp)
                          ];
        RoomMeta::create($dataMetaCreate);
      }

      if($request->gallery) {
        $file = [];                     
        foreach ($request->gallery as $key => $subArr) {
            $image = $subArr['1']['filename'];
            \Image::make($subArr['2']['image'])
            ->save(public_path('storage/images/upload/roomImages/gallery-'.$room->id.'/').$image); 
            unset($subArr['2']);
            $file[$key] = $subArr;  
        }

        $dataMetaCreate = [
                          'room_id'  => $room->id,
                          'meta_key' => 'room_gallery',
                          'value'    => json_encode($file)
                          ];
        RoomMeta::create($dataMetaCreate);                  
      }

	  return ( ($room!=null)? $room : die('Something went wrong!') );
   }

   public function show($id) {
      if(\Gate::allows('superAdmin'))
          return Room::where('id', $id)->with('roomFeature', 'roomRefer')->first();
   }

   public function update(Request $request, $id) {
      $data = [
              'name'        => 'required|string|max:191|unique_name:rooms,name,type_id,'.$request['type'].','.$id,
              'type'        => 'required|numeric|min:1',
              'price'       => 'required|min:1|regex:/^\d+(\.\d{1,2})?$/',
              'no_of_room'  => 'required|numeric|min:1',
              'hotel'       => 'required|numeric|min:1'
              ];

      $customMessages = [
                        'unique_name' => 'The :attribute field is already exist in the same room type.'
                        ];        
                        
      $dataCreate = [
                    'name'        => $request['name'],
                    'type_id'     => $request['type'],
                    'description' => $request['description'],
                    'price'       => $request['price'],
                    'total_room'  => $request['no_of_room']
                    ]; 

      if($request['changeFeature']) 
          $data['image'] = 'required|image64:jpeg,jpg,png';                                  

      $this->validate($request, $data, $customMessages);

      return '';
   }

   public function destroy($id) {
    	if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner')) {
            $room = Room::where('id', $id)->first();

            if($room) {
                File::deleteDirectory(storage_path('app/public/images/upload/roomImages/gallery-'.$id));
                RoomMeta::where('room_id', $id)->delete();
                return Room::where('id', $id)->delete();
            }
            return die('Something went wrong!');
        }
    }



    /**
    *  Extra function
    */



}
