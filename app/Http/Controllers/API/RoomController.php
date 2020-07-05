<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomMeta;
use App\Helpers\Helpers;
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
        return Room::whereIn('room_type_id', Helpers::hotelOwner())->with('roomType')->orderBy('created_at', 'desc')->get();

      if(\Gate::allows('superAdmin'))
		      return Room::with('roomType')->orderBy('created_at', 'desc')->get();
   }

   public function create(Request $request) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');
   	  
      $room = null;
   	  $data = [
              'name' 	      => 'required|string|max:191|unique_name:rooms,name,room_type_id,'.$request['type'].',0',
              'type'        => 'required|numeric|min:1',
              'price'       => 'required|min:1|regex:/^\d+(\.\d{1,2})?$/',
              'no_of_room'  => 'required|numeric|min:0',
              'hotel'       => 'required|numeric|min:1',
              'image'       => 'required|image64:jpeg,jpg,png',
              'max_adult'   => 'required|numeric|min:1',
              'max_child'   => 'required|numeric|min:0'
              ];                                
            
      $customMessages = [
                        'min' => 'The :attribute is required.',
                        'unique_name' => 'The :attribute field is already exist in the same room type.'
                        ];          

      $dataCreate = [
                    'status'       => $request['status'],
                    'name'         => $request['name'],
                    'room_type_id' => $request['type'],
                    'description'  => $request['description'],
                    'price'        => $request['price'],
                    'total_room'   => $request['no_of_room'],
                    'max_adult'    => (int)$request['max_adult'],
                    'max_child'    => (int)$request['max_child']
                    ];                     

      $this->validate($request, $data, $customMessages);

      if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner')) 
        $room = Room::create($dataCreate);
         

      if($request->image) {

        $image = Helpers::featureImage($request->image, $room->id, $room->name, 'create');
        
  			Room::where('id', $room->id)->update(['image'=>$image]);
      }	
      
      try{Helpers::amenities('room_feature', $request->featureData, $room->id);}catch(Exception $e){}
      try{Helpers::amenities('room_feature_optional', $request->featureOptionalData, $room->id);}catch(Exception $e){}
      try{Helpers::roomsNo($request->rooms_no, $room->id, $request->hotel);}catch(Exception $e){}

      if($request->gallery) {

        $file = Helpers::imageGallery($request->gallery, $room->id, 'create');
        
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
        return Room::with('roomType', 'roomGallery')->whereIn('room_type_id', $this->owner())->where('status', 'active')->get();

      if(\Gate::allows('superAdmin')) 
        return Room::with('roomType', 'roomGallery')->where('status', 'active')->get();
   }

   public function update(Request $request, $id) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');

      $room=null;
      $data = [
              'name'        => 'required|string|max:191|unique_name:rooms,name,room_type_id,'.$request['type'].','.$id,
              'type'        => 'required|numeric|min:1',
              'price'       => 'required|min:1|regex:/^\d+(\.\d{1,2})?$/',
              'no_of_room'  => 'required|numeric|min:0',
              'hotel'       => 'required|numeric|min:1',
              'max_adult'   => 'required|numeric|min:1',
              'max_child'   => 'required|numeric|min:0'
              ];

      $customMessages = [
                        'min' => 'The :attribute is required.',
                        'unique_name' => 'The :attribute field is already exist in the same room type.',
                        ];
                        
      $dataUpdate = [
                    'status'       => $request['status'],
                    'name'         => $request['name'],
                    'room_type_id' => $request['type'],
                    'description'  => $request['description'],
                    'price'        => $request['price'],
                    'total_room'   => $request['no_of_room'],
                    'max_adult'    => (int)$request['max_adult'],
                    'max_child'    => (int)$request['max_child']
                    ];

      if($request['changeFeature']) 
        $data['image'] = 'required|image64:jpeg,jpg,png';

      $this->validate($request, $data, $customMessages);


        $room = Room::where('id', $id)->update($dataUpdate);

      if($request->image && $request['changeFeature']) {

        $image = Helpers::featureImage($request->image, $id, $request['name'], 'update');  

        Room::where('id', $id)->update(['image'=>$image]);
      }

      try{Helpers::updateAmenities('room_feature', $request->featureData, $id);}catch(Exception $e){}
      try{Helpers::updateAmenities('room_feature_optional', $request->featureOptionalData, $id);}catch(Exception $e){}
      try{Helpers::roomsNo($request->rooms_no, $id, $request->hotel);}catch(Exception $e){}

      if($request->gallery) {

        $file = Helpers::imageGallery($request->gallery, $id, 'update');

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
    *  Custom query
    */
    public function specificRooms($ids) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
        return die('not allowed');
      $ids = explode(',', $ids);
      return Room::whereIn('id', $ids)->with('roomType')->orderBy('created_at', 'desc')->get();
    }

}
