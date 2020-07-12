<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\RoomType\CreateRequest;
use App\Http\Requests\RoomType\UpdateRequest;
use App\Http\Requests\Gate\OwnerAndAdminRequest;
use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\Room;
use Helpers;

class RoomTypeController extends Controller
{

   public function __construct() 
   {
        $this->middleware(['auth:api', 'verified', 'two_factor_auth']);
   }

   public function index(OwnerAndAdminRequest $ownerAndAdminRequest) 
   {
      if(\Gate::allows('hotelOwner'))
          return RoomType::whereIn('hotel_id', Helpers::hotel_ids())->with('roomTypeHotel')->orderBy('created_at', 'desc')->get(); 
      
      if(\Gate::allows('superAdmin'))
          return RoomType::with('roomTypeHotel')->orderBy('created_at', 'desc')->get();  
     
   }

   public function create(OwnerAndAdminRequest $ownerAndAdminRequest, CreateRequest $request) 
   {
        $dataCreate = [
                  'name'      => $request->name,
                  'hotel_id'  => $request->hotel,
                  ];

        $validated = $request->validated();
    return RoomType::create($dataCreate);              
   }

   public function show(OwnerAndAdminRequest $ownerAndAdminRequest, $id) 
   {
        if(\Gate::allows('hotelOwner')) return RoomType::whereIn('hotel_id', Helpers::hotel_ids())->where('id', $id)->first();
        if(\Gate::allows('superAdmin')) return RoomType::where('id', $id)->first();
    }

   public function update(OwnerAndAdminRequest $ownerAndAdminRequest, UpdateRequest $request, $id) 
   {
        $dataUpdate = [
                      'name' => $request->name
                      ]; 

        $validated = $request->validated();
        if(\Gate::allows('hotelOwner')) return RoomType::whereIn('hotel_id', Helpers::hotel_ids())->where('id', $id)->update($dataUpdate);
        if(\Gate::allows('superAdmin')) return RoomType::where('id', $id)->update($dataUpdate);
   }

   public function destroy(OwnerAndAdminRequest $ownerAndAdminRequest, $id) 
   {
      if(\Gate::allows('hotelOwner'))
          return RoomType::whereIn('hotel_id', Helpers::hotel_ids())->where('id', $id)->delete();

      if(\Gate::allows('superAdmin')) 
         return RoomType::where('id', $id)->delete();
   }


   /**
    *  Custom query
    */

   public function bookingRoomTypes(OwnerAndAdminRequest $ownerAndAdminRequest, $hotelId) 
   {
    return RoomType::where('hotel_id', $hotelId)->with('roomTypeRooms')->orderBy('created_at', 'desc')->get();  
   }

   public function roomRoomTypes(OwnerAndAdminRequest $ownerAndAdminRequest, $id, $roomId) 
   {
    return RoomType::where('hotel_id', $id)->with('roomTypeHotel')->orderBy('created_at', 'desc')->get();
   }
   
}
