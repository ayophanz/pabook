<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Notifications\notifiable;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Controller;
use App\Booking;
use App\Notifications\RoomReservation;
use App\Events\incompleteBooking;
use App\Helpers\Helpers;

class BookController extends Controller
{
    public function __construct() {
        $this->middleware(['auth:api', 'verified', 'two_factor_auth']);
   }

   public function index() {
    if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner') && !\Gate::allows('hotelReceptionist'))
      return die('not allowed');
    
    if(\Gate::allows('superAdmin'))  
      return Booking::with('room')->get();
    
    if(\Gate::allows('hotelOwner')) 
      return Booking::whereIn('roomId', Helpers::getRoomsIdBaseUser('owner'))->with('room')->get();

    if(\Gate::allows('hotelReceptionist')) 
      return Booking::whereIn('roomId', Helpers::getRoomsIdBaseUser('recep'))->with('room')->get();  
   }

   public function create(Request $request) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner') && !\Gate::allows('hotelReceptionist'))
        return die('not allowed');

   		$data = [
            'hotel'  => 'required',
            'checkInD'  => 'required',
            'checkOutD' => 'required',
            'manyAdult' => 'required',
            'manyChild' => 'required',
            'manyRoom'  => 'required',
            'roomWithRoomType'  => 'required',
            'rooms_no'  => 'required|rooms_no_equal_room_name:'.$request['manyRoom'].','.count($request['rooms_no'])
   		]; 

   		$customMessages = [
                        'hotel.required' => 'The hotel is required.',
                        'checkInD.required' => 'The check-in or check-out date is required.',
                        'checkOutD.required' => 'The check-in or check-out date is required.',
                        'manyAdult.required' => 'The number of adult is required.',
                        'manyChild.required' => 'The number of child is required.',
                        'manyRoom.required' => 'The number of how many room is required.',
                        'roomWithRoomType.required' => 'The room is required.',
                        'rooms_no_equal_room_name' => 'The item on this field must match on "No. of room"'
                        ];

      $this->validate($request, $data, $customMessages);

      $data = [
                'name'         => 'incomplete',
                'phoneNo'      => 'incomplete',
                'email'        => 'incomplete',
                'address'      => 'incomplete',
                'roomId'       => (int)$request['roomWithRoomType'],
                'hotelId'      => (int)$request['hotel'],
                'dateStart'    => date($request['checkInD']),
                'dateEnd'      => date($request['checkOutD']),
                'amount'       => (float)$request['totalAmount'],
                'currency'     => $request['currency_use'],
                'userId'       => auth('api')->user()->id,
                'manyRoom'     => (int)$request['manyRoom'],
                'manyAdult'    => (int)$request['manyAdult'],
                'manyChild'    => (int)$request['manyChild'],
                'roomsNo'      =>  json_encode($request['rooms_no']),
                'status'       => 'incomplete',
                'optionalAmen' =>  json_encode($request['addOnOptionalAmen'])
              ];      

      $book = Booking::create($data);
      event(new incompleteBooking($book->id));       
      return Booking::where('id', $book->id)->with('room', 'hotel')->get();
   }
   
   public function continueBooking($id) {
    if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner') && !\Gate::allows('hotelReceptionist'))
      return die('not allowed');
    
    return Booking::where('id', (int)$id)->with('room', 'hotel')->get();
   }

   public function saveContinueBooking(Request $request, $id) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner') && !\Gate::allows('hotelReceptionist'))
        return die('not allowed');

      $data = [
            'name'    => 'required',
            'phoneNo' => 'required',
            'email'   => 'string|email|max:191'
      ]; 

      $this->validate($request, $data);  

      $data = [
        'name'    => $request['name'],
        'phoneNo' => $request['phoneNo'],
        'email'   => $request['email'],
        'address' => $request['address'],
        'status'  => 'active'
      ]; 

      Booking::where('id', $id)->update($data);
      $book = Booking::where('id', $id)->with('room', 'hotel')->first();
      Notification::send(Helpers::userToNotify($book->roomId), new RoomReservation($book));

      return $book;
   }

   public function autoCancel() {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner') && !\Gate::allows('hotelReceptionist'))
        return die('not allowed');
      
      if(\Gate::allows('hotelOwner')) 
        return Booking::whereIn('roomId', Helpers::getRoomsIdBaseUser('owner'))->whereDate('dateEnd', '<=', date('Y-m-d'))->where('status', 'book')->update(['status'=>'cancel']);
      
      if(\Gate::allows('hotelReceptionist')) 
        return Booking::whereIn('roomId', Helpers::getRoomsIdBaseUser('recep'))->whereDate('dateEnd', '<=', date('Y-m-d'))->where('status', 'book')->update(['status'=>'cancel']);

   }

   public function bookCancel($id) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner') && !\Gate::allows('hotelReceptionist'))
        return die('not allowed');
      
      if( (\Gate::allows('superAdmin')) || (\Gate::allows('hotelOwner') && $this->validateAction('owner', $id)) || (\Gate::allows('hotelReceptionist') && $this->validateAction('recep', $id)) )
        return Booking::where('id', $id)->where('status', 'book')->update(['status'=>'cancel']);
      else
        return die('this room is not yours');
   }

   public function checkOut($id) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner') && !\Gate::allows('hotelReceptionist'))
        return die('not allowed');
      
      if( (\Gate::allows('superAdmin')) || (\Gate::allows('hotelOwner') && $this->validateAction('owner', $id)) || (\Gate::allows('hotelReceptionist') && $this->validateAction('recep', $id)) )
        return Booking::where('id', $id)->update(['status'=>'checkout']);
      else
        return die('this room is not yours');
   }

   public function checkIn($id) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner') && !\Gate::allows('hotelReceptionist'))
        return die('not allowed');

      if( (\Gate::allows('superAdmin')) || (\Gate::allows('hotelOwner') && $this->validateAction('owner', $id)) || (\Gate::allows('hotelReceptionist') && $this->validateAction('recep', $id)) )
        return Booking::where('id', $id)->update(['status'=>'checkin']);
      else
        return die('this room is not yours');  
   }

   public function markAsRead($id=null) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner') && !\Gate::allows('hotelReceptionist'))
          return die('not allowed');

      if($id!=null)
        auth()->user()->unreadNotifications->where('id', $id)->markAsRead();
      return auth()->user()->load('notifications');

   }

   public function incompleteBooking() {
    if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner') && !\Gate::allows('hotelReceptionist'))
      return die('not allowed');

     return Booking::where('status', 'incomplete')->get();
   }


   public function cancelBooking($id) {
    if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner') && !\Gate::allows('hotelReceptionist'))
      return die('not allowed');

     return Booking::where('id', $id)->delete();
   }

}
