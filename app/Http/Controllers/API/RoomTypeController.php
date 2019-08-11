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
        $this->middleware('auth:api');
   }

   public function index($id=null) {
      if($id) {
        $hotelId = explode(',', $id)[0];
        $roomId  = explode(',', $id)[1];
        if(\Gate::allows('hotelOwner') || \Gate::allows('superAdmin'))
           return RoomType::where('hotel_id', $hotelId)->whereNotIn('id', $this->roomTypeIds($hotelId, $roomId))->with('roomTypeRefer')->orderBy('created_at', 'desc')->get();
     
      }else{
        if(\Gate::allows('hotelOwner'))
           return RoomType::whereIn('hotel_id', $this->hotel_ids())->with('roomTypeRefer')->orderBy('created_at', 'desc')->get(); 
        
        if(\Gate::allows('superAdmin'))
           return RoomType::with('roomTypeRefer')->orderBy('created_at', 'desc')->get();  
     }
   }

   public function create(Request $request) {
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
      
      if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner'))
        return RoomType::create($dataCreate);              
   }

   public function show($id) {
        if(\Gate::allows('hotelOwner'))
            return RoomType::whereIn('hotel_id', $this->hotel_ids())->where('id', $id)->first();

        if(\Gate::allows('superAdmin'))
            return RoomType::where('id', $id)->first();
    }

   public function update(Request $request, $id) {
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
   public function hotel_ids() {
      return Hotel::select('id')->where('owner_id', auth('api')->user()->id)->get()->toArray();
   }

   /**
    *  Get unassigned room types
    */
   public function roomTypeIds($hotelId, $roomId) {
      $roomType = RoomType::select('id')->where('hotel_id', $hotelId)->get()->toArray();
      foreach ($roomType as $key => $value) {
        unset($roomType[$key]['room_type_refer']);
      }
      
      $rooms = Room::select('type_id')->whereIn('type_id', $roomType)->get()->toArray();
      if($roomId!=null) 
        $rooms = Room::select('type_id')->where('id', '!=', $roomId)->whereIn('type_id', $roomType)->get()->toArray();

      foreach ($rooms as $key => $value) {
        unset($rooms[$key]['room_type']);
        unset($rooms[$key]['room_feature']);
        unset($rooms[$key]['room_gallery']);
      }

      return $rooms;
   }

}
