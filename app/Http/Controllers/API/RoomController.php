<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Room\CreateRequest;
use App\Http\Requests\Room\UpdateRequest;
use App\Http\Requests\Gate\AllRequest;
use App\Http\Requests\Gate\OwnerAndAdminRequest;

use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomMeta;
use Helpers;
use Exception;

class RoomController extends Controller
{
    public function __construct() {
        $this->middleware(['auth:api', 'verified', 'two_factor_auth']);
   }

   public function index(OwnerAndAdminRequest $ownerAndAdminRequest) {
      if(\Gate::allows('hotelOwner'))
        return Room::whereIn('room_type_id', Helpers::hotelOwner())->with('roomType')->orderBy('created_at', 'desc')->get();

      if(\Gate::allows('superAdmin'))
		      return Room::with('roomType')->orderBy('created_at', 'desc')->get();
   }

   public function create(OwnerAndAdminRequest $ownerAndAdminRequest, CreateRequest $request) {   	  
      $room = null;            
      $dataCreate = [
                    'status'       => $request->status,
                    'name'         => $request->name,
                    'room_type_id' => $request->type,
                    'description'  => $request->description,
                    'price'        => $request->price,
                    'total_room'   => $request->no_of_room,
                    'max_adult'    => (int)$request->max_adult,
                    'max_child'    => (int)$request->max_child
                    ];                     

      $validated = $request->validated();

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

      return $room!=null ? $room : die('Something went wrong!');
   }

   public function show(OwnerAndAdminRequest $ownerAndAdminRequest, $id) {
      return Room::where('id', $id)->with('roomFeature', 'roomFeatureOptional', 'roomType')->first();
   }

   public function availableRooms(AllRequest $allRequest, $start=null, $end=null) {
      if(\Gate::allows('hotelOwner') || \Gate::allows('hotelReceptionist')) 
        return Room::with('roomType', 'roomGallery')->whereIn('room_type_id', $this->owner())->where('status', 'active')->get();

      if(\Gate::allows('superAdmin')) 
        return Room::with('roomType', 'roomGallery')->where('status', 'active')->get();
   }

   public function update(OwnerAndAdminRequest $ownerAndAdminRequest, UpdateRequest $request, $id) {
      $room=null;                        
      $dataUpdate = [
                    'status'       => $request->status,
                    'name'         => $request->name,
                    'room_type_id' => $request->type,
                    'description'  => $request->description,
                    'price'        => $request->price,
                    'total_room'   => $request->no_of_room,
                    'max_adult'    => (int)$request->max_adult,
                    'max_child'    => (int)$request->max_child
                    ];

      $validated = $request->validated();
      $room = Room::where('id', $id)->update($dataUpdate);

      if($request->image && $request->changeFeature) {
        $image = Helpers::featureImage($request->image, $id, $request->name, 'update');  
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

   public function destroy(OwnerAndAdminRequest $ownerAndAdminRequest, $id) {
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
    public function specificRooms(OwnerAndAdminRequest $ownerAndAdminRequest, $ids) {
      $ids = explode(',', $ids);
      return Room::whereIn('id', $ids)->with('roomType')->orderBy('created_at', 'desc')->get();
    }

}
