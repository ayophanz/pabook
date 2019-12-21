<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\RoomType;
use App\hotel;
use App\Room;

class RoomTypeController extends Controller
{

   public function __construct() {
        $this->middleware(['auth:api', 'verified', 'two_factor_auth']);
   }

   public function index($id=null) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');

      if($id) {
        $hotelId = explode(',', $id)[0];
        $roomId  = explode(',', $id)[1];
        if($roomId=='0') 
          return RoomType::where('hotel_id', $hotelId)->with('roomTypeRooms')->orderBy('created_at', 'desc')->get();
        else
          return RoomType::where('hotel_id', $hotelId)->whereNotIn('id', $this->roomTypeIds($hotelId, $roomId))->with('roomTypeHotel')->orderBy('created_at', 'desc')->get();
        

      }else{
        if(\Gate::allows('hotelOwner'))
           return RoomType::whereIn('hotel_id', $this->hotel_ids())->with('roomTypeHotel')->orderBy('created_at', 'desc')->get(); 
        
        if(\Gate::allows('superAdmin'))
           return RoomType::with('roomTypeHotel')->orderBy('created_at', 'desc')->get();  
     }
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
           return RoomType::whereIn('hotel_id', $this->hotel_ids())->where('id', $id)->first();

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
            return RoomType::whereIn('hotel_id', $this->hotel_ids())->where('id', $id)->update($dataUpdate);

        if(\Gate::allows('superAdmin'))
            return RoomType::where('id', $id)->update($dataUpdate);
   }

   public function destroy($id) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');

      if(\Gate::allows('hotelOwner'))
          return RoomType::whereIn('hotel_id', $this->hotel_ids())->where('id', $id)->delete();

      if(\Gate::allows('superAdmin')) 
         return RoomType::where('id', $id)->delete();
   }


   /**
    *  Extra function
    */

   /**
    *  Get owner hotels ID
    */
   private function hotel_ids() {
      return Hotel::where('user_id', auth('api')->user()->id)->pluck('id')->toArray();
   }

   /**
    *  Get unassigned room types
    */
   private function roomTypeIds($hotelId, $roomId) {
      $roomType = RoomType::select('id')->where('hotel_id', $hotelId)->get()->toArray();
      foreach ($roomType as $key => $value) {
        unset($roomType[$key]['room_type_hotel']);
      }
      
      $rooms = Room::select('room_type_id')->whereIn('room_type_id', $roomType)->get()->toArray();
      if($roomId!=null) 
        $rooms = Room::select('room_type_id')->where('id', '!=', $roomId)->whereIn('room_type_id', $roomType)->get()->toArray();

      foreach ($rooms as $key => $value) {
        unset($rooms[$key]['room_type']);
        unset($rooms[$key]['room_feature']);
        unset($rooms[$key]['room_gallery']);
      }

      return $rooms;
   }

}
