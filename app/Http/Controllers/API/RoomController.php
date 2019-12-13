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
use Exception;

class RoomController extends Controller
{
    public function __construct() {
        $this->middleware(['auth:api', 'verified', 'two_factor_auth']);
   }

   public function index() {
        if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
            return die('not allowed');

        if(\Gate::allows('hotelOwner'))
          return Room::whereIn('type_id', $this->owner())->with('roomType')->orderBy('created_at', 'desc')->get();

        if(\Gate::allows('superAdmin'))
		      return Room::with('roomType')->orderBy('created_at', 'desc')->get();
   }

   public function create(Request $request) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');
   	  
      $room = null;
   	  $data = [
              'name' 	      => 'required|string|max:191|unique_name:rooms,name,type_id,'.$request['type'].',0',
              'type'        => 'required|numeric|min:1',
              'price'       => 'required|min:1|regex:/^\d+(\.\d{1,2})?$/',
              //'no_of_room'  => 'required|numeric|min:1',
              'hotel'       => 'required|numeric|min:1',
              'image'       => 'required|image64:jpeg,jpg,png'//,
              //'rooms_no'    => 'required|rooms_no_equal_room_total:'.count($request['rooms_no']).','.$request['no_of_room']
              ];                                
            
      $customMessages = [
                        'min' => 'The :attribute is required'//,
                        //'rooms_no_equal_room_total' => 'The Rooms no. items must equal to "No. of unit".'
                        ];          

      $dataCreate = [
                    'status'      => $request['status'],
                    'name'        => $request['name'],
                    'type_id'     => $request['type'],
                    'description' => $request['description'],
                    'price'       => $request['price'],
                    //'total_room'  => $request['no_of_room']
                    ];                     

      $this->validate($request, $data, $customMessages);

      if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner')) 
        $room = Room::create($dataCreate);
         

      if($request->image) {

        $image = $this->featureImage($request->image, $room->id, $room->name, 'create');
        
  			Room::where('id', $room->id)->update(['image'=>$image]);
      }	
      
      try{$this->amenities('room_feature', $request->featureData, $room->id);}catch(Exception $e){}
      try{$this->amenities('room_feature_optional', $request->featureOptionalData, $room->id);}catch(Exception $e){}
      try{$this->roomsNo($request->rooms_no, $room->id, $request->hotel);}catch(Exception $e){}

      if($request->gallery) {

        $file = $this->imageGallery($request->gallery, $room->id, 'create');
        
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
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');

      return Room::where('id', $id)->with('roomFeature', 'roomFeatureOptional', 'roomType')->first();
   }

   public function availableRooms($start=null, $end=null) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner') && !\Gate::allows('hotelReceptionist'))
        return die('not allowed');

      if(\Gate::allows('hotelOwner') || \Gate::allows('hotelReceptionist')) 
        return Room::with('roomType', 'roomGallery')->whereIn('type_id', $this->owner())->where('status', 'active')->get();

      if(\Gate::allows('superAdmin')) 
        return Room::with('roomType', 'roomGallery')->where('status', 'active')->get();
   }

   public function update(Request $request, $id) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');

      $room=null;
      $data = [
              'name'        => 'required|string|max:191|unique_name:rooms,name,type_id,'.$request['type'].','.$id,
              'type'        => 'required|numeric|min:1',
              'price'       => 'required|min:1|regex:/^\d+(\.\d{1,2})?$/',
              //'no_of_room'  => 'required|numeric|min:1',
              'hotel'       => 'required|numeric|min:1'//,
              //'rooms_no'    => 'required|rooms_no_equal_room_total:'.count($request['rooms_no']).','.$request['no_of_room']
              ];

      $customMessages = [
                        'unique_name' => 'The :attribute field is already exist in the same room type.'//,
                        //'rooms_no_equal_room_total' => 'The Rooms no. items must equal to "No. of unit".'
                        ];
                        
      $dataUpdate = [
                    'status'      => $request['status'],
                    'name'        => $request['name'],
                    'type_id'     => $request['type'],
                    'description' => $request['description'],
                    'price'       => $request['price'],
                    //'total_room'  => $request['no_of_room']
                    ];

      if($request['changeFeature']) 
        $data['image'] = 'required|image64:jpeg,jpg,png';

      $this->validate($request, $data, $customMessages);


        $room = Room::where('id', $id)->update($dataUpdate);

      if($request->image && $request['changeFeature']) {

        $image = $this->featureImage($request->image, $room->id, $room->name, 'update');  

        Room::where('id', $id)->update(['image'=>$image]);
      }

      try{$this->updateAmenities('room_feature', $request->featureData, $id);}catch(Exception $e){}
      try{$this->updateAmenities('room_feature_optional', $request->featureOptionalData, $id);}catch(Exception $e){}
      try{$this->roomsNo($request->rooms_no, $id, $request->hotel);}catch(Exception $e){}

      if($request->gallery) {

        $file = $this->imageGallery($request->gallery, $id, 'update');

        $dataMetaUpdate = ['value' => json_encode($file)];
        RoomMeta::where('room_id', $id)->where('meta_key', 'room_gallery')->update($dataMetaUpdate);                  
      }

      return ( ($room!=null)? $room : die('Something went wrong!') );
   }

   public function destroy($id) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');

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



    /**
    *  Extra function
    */


    /**
    * Gallery upload
    */
    private function imageGallery($images, $id, $action) {
      $file = [];                     
      foreach ($images as $key => $subArr) {
          $image = $subArr['1']['filename'];
          $fileExist = public_path().'/storage/images/upload/roomImages/gallery-'.$id.'/'.$image;
          if($action=='update' && File::exists($fileExist))
            unlink(storage_path('app/public/images/upload/roomImages/gallery-'.$id.'/'.$image));
          \Image::make($subArr['2']['image'])->save(public_path('storage/images/upload/roomImages/gallery-'.$id.'/').$image); 
          unset($subArr['2']);
          $file[$key] = $subArr;  
      }
      return $file;
    }

    /**
    * Feature image upload
    */
    private function featureImage($img, $id, $name, $action) {
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
    private function owner($roomType=0) {
      $user = auth('api')->user();
      if($roomType==0) {
        $hotel = Hotel::select('id')->where('status', 'verified')->where('owner_id', $user->id)->pluck('id')->toArray();
        if(empty($hotel)) {
          $userMeta = UserMeta::select('user_id')->where('value', $user->id)->first();
          if(!empty($userMeta))
            $hotel = Hotel::select('id')->where('status', 'verified')->where('owner_id', $userMeta->user_id)->pluck('id')->toArray();
        }

        $roomType = RoomType::select('id')->whereIn('hotel_id', $hotel)->pluck('id')->toArray();
        foreach ($roomType as $key => $value) {
          unset($roomType[$key]['room_type_refer']);
        }
        return $roomType;
      }else{
        //
      }
    }

    /**
    *  create rooms no.
    */
    private function roomsNo($roomsNoData, $room_id, $hotel_id) {
      $hotel = Hotel::where('id', $hotel_id)->first();
      $newData = json_decode($hotel->hotel_rooms_no, true);
      foreach($newData as &$val) {
        if($val['assign_id']==$room_id) $val['assign_id'] = 'no';
        foreach($roomsNoData as $val2) {
          if($val['code']==$val2['code']) {
            $val['status'] = $val2['status'];
            $val['assign_id'] = $room_id;
            continue;
          }
        }
      }
      $data = ['hotel_rooms_no' => json_encode($newData)];
      if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner'))
        Hotel::where('id', $hotel_id)->update($data);
    }


    /**
    *  create amenities
    */
    private function amenities($type, $featureData, $room_id) {
        $featureDataTemp = array_filter($featureData, function($v) { return !is_null($v['value']); });
        if(json_encode($featureDataTemp)!='[[]]') {
          $dataMetaCreate = [
                            'room_id'  => $room_id,
                            'meta_key' => $type,
                            'value'    => json_encode($featureDataTemp)
                            ];
          if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner'))                  
            RoomMeta::create($dataMetaCreate);
        }
    }


    /**
    *  update amenities
    */
    private function updateAmenities($type, $featureData, $room_id) {
      $featureDataTemp = array_filter($featureData, function($v) { return !is_null($v['value']); });
      if(json_encode($featureDataTemp)!='[[]]') {
          $dataMetaUpdate = ['value' => json_encode($featureDataTemp)];
          if(RoomMeta::where('room_id', $room_id)->where('meta_key', $type)->get()->count()) {
            if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner'))
              RoomMeta::where('room_id', $room_id)->where('meta_key', $type)->update($dataMetaUpdate);
          }else{
            $dataMetaCreate = [
              'room_id'  => $room_id,
              'meta_key' => $type,
              'value' => json_encode($featureDataTemp)
            ];
            if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner'))
              RoomMeta::create($dataMetaCreate);
          }
        }
    }

    /**
    *  is room available
    */
    // private function isRoomAvailable() {
    //   $room = Room::select('id')->whereIn('type_id', $this->owner())->where('status', 'active')->get()->toArray();
    //   $book = Booking::whereIn('room_id', $room)->get();
    //   return false;
    // }

}
