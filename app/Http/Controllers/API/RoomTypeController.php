<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\Room;
use Helpers;

class RoomTypeController extends Controller
{

   public function __construct() {
        $this->middleware(['auth:api', 'verified', 'two_factor_auth']);
   }

   public function index() {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');

      if(\Gate::allows('hotelOwner'))
          return RoomType::whereIn('hotel_id', Helpers::hotel_ids())->with('roomTypeHotel')->orderBy('created_at', 'desc')->get(); 
      
      if(\Gate::allows('superAdmin'))
          return RoomType::with('roomTypeHotel')->orderBy('created_at', 'desc')->get();  
     
   }

   public function create(Request $request) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');
      $data = [
              'name' 	=> 'required|string|max:191|unique_name:room_types,name,hotel_id,'.$request['hotel'].',0',
              'hotel' => 'required|numeric|min:1'
              ];

      $customMessages = [
                        'unique_name' => 'The :attribute field is already exist in the same hotel name.'
                        ];        
                        
      $dataCreate = [
                  'name'      => $request['name'],
                  'hotel_id'  => $request['hotel']
                  ];

      $this->validate($request, $data, $customMessages);
      
      return RoomType::create($dataCreate);              
   }

   public function show($id) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');

      if(\Gate::allows('hotelOwner'))
           return RoomType::whereIn('hotel_id', Helpers::hotel_ids())->where('id', $id)->first();

       if(\Gate::allows('superAdmin'))
           return RoomType::where('id', $id)->first();
    }

   public function update(Request $request, $id) {
        if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');

        $data = [
                'name'  => 'required|string|max:191|unique_name:room_types,name,hotel_id,'.$request['hotel'].','.$id
                ];

        $customMessages = [
                          'unique_name' => 'The :attribute field is already exist.'
                          ];           

        $dataUpdate = [
                      'name' => $request['name']
                      ]; 

        $this->validate($request,$data,$customMessages);

        if(\Gate::allows('hotelOwner'))
            return RoomType::whereIn('hotel_id', Helpers::hotel_ids())->where('id', $id)->update($dataUpdate);

        if(\Gate::allows('superAdmin'))
            return RoomType::where('id', $id)->update($dataUpdate);
   }

   public function destroy($id) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');

      if(\Gate::allows('hotelOwner'))
          return RoomType::whereIn('hotel_id', Helpers::hotel_ids())->where('id', $id)->delete();

      if(\Gate::allows('superAdmin')) 
         return RoomType::where('id', $id)->delete();
   }


   /**
    *  Custom query
    */

   public function bookingRoomTypes($hotelId) {
    if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
      return die('not allowed');

    return RoomType::where('hotel_id', $hotelId)->with('roomTypeRooms')->orderBy('created_at', 'desc')->get();  
   }

   public function roomRoomTypes($id, $roomId) {
    if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
      return die('not allowed');

    return RoomType::where('hotel_id', $id)->with('roomTypeHotel')->orderBy('created_at', 'desc')->get();
   }
   
}
