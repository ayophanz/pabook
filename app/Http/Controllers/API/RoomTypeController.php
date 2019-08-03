<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\RoomType;
use App\hotel;

class RoomTypeController extends Controller
{

   public function __construct() {
        $this->middleware('auth:api');
   }

   public function index($id=null) {
      if($id) {
        if(\Gate::allows('superAdmin'))
          return RoomType::where('hotel_id', $id)->with('roomTypeRefer')->orderBy('created_at', 'desc')->get();
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
}
