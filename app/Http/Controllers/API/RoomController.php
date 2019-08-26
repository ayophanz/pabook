<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Room;
use App\RoomMeta;
use App\RoomType;
use App\Hotel;
use App\UserMeta;
use App\Option;

class RoomController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
   }

   public function index() {
        if(\Gate::allows('hotelOwner'))
          return Room::whereIn('type_id', $this->owner())->with('roomType')->orderBy('created_at', 'desc')->get();

        if(\Gate::allows('superAdmin'))
		      return Room::with('roomType')->orderBy('created_at', 'desc')->get();
   }

   public function create(Request $request) {
   	  $room = null;
   	  $data = [
              'name' 	      => 'required|string|max:191|unique_name:rooms,name,type_id,'.$request['type'].',0',
              'type'        => 'required|numeric|min:1',
              'price'       => 'required|min:1|regex:/^\d+(\.\d{1,2})?$/',
              'no_of_room'  => 'required|numeric|min:1',
              'hotel'       => 'required|numeric|min:1',
              'image'       => 'required|image64:jpeg,jpg,png'
              ];                                
      
      $customMessages = [
                        'min' => 'The :attribute is required'
                        ];          

      $dataCreate = [
                    'status'      => $request['status'],
                    'name'        => $request['name'],
                    'type_id'     => $request['type'],
                    'description' => $request['description'],
                    'price'       => $request['price'],
                    'total_room'  => $request['no_of_room']
                    ];                     

      $this->validate($request, $data, $customMessages);

      if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner')) 
        $room = Room::create($dataCreate);
         

      if($request->image) {

        $image = $this->featureImage($request->image, $room->id, $room->name, 'create');
        
        if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner'))
  			 Room::where('id', $room->id)->update(['image'=>$image]);
  	  }	

      $featureDataTemp = array_filter($request->featureData, function($v) { return !is_null($v['value']); });
      if($featureDataTemp) {
        $dataMetaCreate = [
                          'room_id'  => $room->id,
                          'meta_key' => 'room_feature',
                          'value'    => json_encode($featureDataTemp)
                          ];
        if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner'))                  
          RoomMeta::create($dataMetaCreate);
      }

      if($request->gallery) {

        $file = $this->imageGallery($request->gallery, $room->id, 'create');
        
        $dataMetaCreate = [
                          'room_id'  => $room->id,
                          'meta_key' => 'room_gallery',
                          'value'    => json_encode($file)
                          ];
        if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner'))                  
          RoomMeta::create($dataMetaCreate);                  
      }

	  return ( ($room!=null)? $room : die('Something went wrong!') );
   }

   public function show($id) {
      if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner')) 
          return Room::where('id', $id)->with('roomFeature', 'roomType')->first();
   }

   public function availableRooms($start=null, $end=null) {
      if(\Gate::allows('hotelOwner') || \Gate::allows('hotelReceptionist')) 
        return Room::with('roomType', 'roomGallery')->whereIn('type_id', $this->owner())->where('status', 'active')->get();

      if(\Gate::allows('superAdmin')) 
        return Room::with('roomType', 'roomGallery')->where('status', 'active')->get();
   }

   public function update(Request $request, $id) {
      $room=null;
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
                        
      $dataUpdate = [
                    'status'      => $request['status'],
                    'name'        => $request['name'],
                    'type_id'     => $request['type'],
                    'description' => $request['description'],
                    'price'       => $request['price'],
                    'total_room'  => $request['no_of_room']
                    ];

      if($request['changeFeature']) 
        $data['image'] = 'required|image64:jpeg,jpg,png';

      $this->validate($request, $data, $customMessages);

      if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner')) 
        $room = Room::where('id', $id)->update($dataUpdate);

      if($request->image && $request['changeFeature']) {

        $image = $this->featureImage($request->image, $room->id, $room->name, 'update');  

        if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner'))
          Room::where('id', $id)->update(['image'=>$image]);
      }

      $featureDataTemp = array_filter($request->featureData, function($v) { return !is_null($v['value']); });
      if($featureDataTemp) {
        $dataMetaUpdate = ['value' => json_encode($featureDataTemp)];
        if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner'))
          RoomMeta::where('room_id', $id)->where('meta_key', 'room_feature')->update($dataMetaUpdate);
      }

      if($request->gallery) {

        $file = $this->imageGallery($request->gallery, $id, 'update');

        $dataMetaUpdate = ['value' => json_encode($file)];
        RoomMeta::where('room_id', $id)->where('meta_key', 'room_gallery')->update($dataMetaUpdate);                  
      }

      return ( ($room!=null)? $room : die('Something went wrong!') );
   }

   public function destroy($id) {
    	if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner')) {
          $room = Room::where('id', $id)->first();

          if($room) {
              File::deleteDirectory(storage_path('app/public/images/upload/roomImages/gallery-'.$id));
              if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner')) {
                RoomMeta::where('room_id', $id)->delete();
                return Room::where('id', $id)->delete();
              }
          }
          return die('Something went wrong!');
      }
   }



    /**
    *  Extra function
    */


    /**
    * Gallery upload
    */
    public function imageGallery($images, $id, $action) {
      $file = [];                     
      foreach ($images as $key => $subArr) {
          $image = $subArr['1']['filename'];
          $fileExist = public_path().'/storage/images/upload/roomImages/gallery-'.$id.'/'.$image;
          if($action=='update' && File::exists($fileExist))
            unlink(storage_path('app/public/images/upload/roomImages/gallery-'.$id.'/'.$image));
          \Image::make($subArr['2']['image'])
          ->save(public_path('storage/images/upload/roomImages/gallery-'.$id.'/').$image); 
          unset($subArr['2']);
          $file[$key] = $subArr;  
      }
      return $file;
    }

    /**
    * Feature image upload
    */
    public function featureImage($img, $id, $name, $action) {
      $folder = public_path().'/storage/images/upload/roomImages/gallery-'.$id.'/';
      if (!File::exists($folder)) 
          File::makeDirectory($folder, 0775, true);

      $image =  $name.'-'.$id.'.'.explode('/', 
           explode(':', substr($img, 0, 
           strpos($img, ';')))[1])[1];
      if($action=='update')
          unlink(storage_path('app/public/images/upload/roomImages/gallery-'.$id.'/'.$image));
      \Image::make($img)
      ->save(public_path('storage/images/upload/roomImages/gallery-'.$id.'/').$image);
      return $image;
    }

    /**
    *  Hotel owner
    */
    public function owner($roomType=0) {
      $user = auth('api')->user();
      if($roomType==0) {
        $hotel = Hotel::select('id')->where('owner_id', $user->id)->get()->toArray();
        if(empty($hotel)) {
          $userMeta = UserMeta::select('user_id')->where('value', $user->id)->first();
          $hotel = Hotel::select('id')->where('owner_id', $userMeta->user_id)->get()->toArray();
        }

        $roomType = RoomType::select('id')->whereIn('hotel_id', $hotel)->get()->toArray();
        foreach ($roomType as $key => $value) {
          unset($roomType[$key]['room_type_refer']);
        }
        return $roomType;
      }else{
        //
      }
    }

}
