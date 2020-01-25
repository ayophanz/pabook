<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Notifications\notifiable;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Controller;
use App\Booking;
use App\User;
use App\UserMeta;
use App\Notifications\RoomReservation;
use App\Room;
use App\RoomType;
use App\Hotel;
use DateTime;


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
      return Booking::whereIn('room_id', $this->filterBookings('owner'))->with('room')->get();

    if(\Gate::allows('hotelReceptionist')) 
      return Booking::whereIn('room_id', $this->filterBookings('recep'))->with('room')->get();  
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

      Booking::create($data);
       
      return $data;

      // $book = Booking::create($dataCreate);
      // if($book) {
      //   Notification::send($this->userToNotify($book->room_id), new RoomReservation($book));
      //   return $book;
      // }
      
   }

   public function autoCancel() {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner') && !\Gate::allows('hotelReceptionist'))
        return die('not allowed');
      
      if(\Gate::allows('hotelOwner')) 
        return Booking::whereIn('room_id', $this->filterBookings('owner'))->whereDate('dateEnd', '<=', date('Y-m-d'))->where('status', 'book')->update(['status'=>'cancel']);
      
      if(\Gate::allows('hotelReceptionist')) 
        return Booking::whereIn('room_id', $this->filterBookings('recep'))->whereDate('dateEnd', '<=', date('Y-m-d'))->where('status', 'book')->update(['status'=>'cancel']);

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


   /**
    *  Extra function
    */


   /**
    *  Users to notify
    */
    private function userToNotify($room_id) {
      $id = $room_id;
      $room = Room::select('room_type_id')->where('id', $id)->first();
      $roomType = RoomType::select('hotel_id')->where('id', $room->room_type_id)->first();
      $hotel = Hotel::select('user_id')->where('id', $roomType->hotel_id)->first();
      $owner = User::select('id')->where('id', $hotel->user_id)->first();
      $recep = UserMeta::select('value')->where('meta_key', 'receptionist_id')->where('user_id', $owner->id)->get()->toArray();
      $permission = UserMeta::select('value', 'user_id')->where('meta_key', 'assign_to_hotel')->whereIn('user_id', $recep)->get();

      $allowed = array();
      array_push($allowed, $owner->id);
      foreach ($permission as $key => $item) {
          $ids = explode(',', substr($item->value, 1, -1));
          foreach ($ids as $key2 => $item2) {
            if((int)$roomType->hotel_id==(int)$item2) {
               array_push($allowed, (int)$item->user_id);
               continue;
            } 
          }
      }
      return User::whereIn('id', $allowed)->orwhere('role', 'super_admin')->get();
    }

   /**
    *  filter bookings
    */
    private function filterBookings($userType) {
      $owner = 0;
      if($userType=='recep') 
        $owner = UserMeta::select('user_id')->where('meta_key', 'receptionist_id')->where('value', auth('api')->user()->id)->first()->user_id;
      
      if($userType=='owner')
        $owner = auth('api')->user()->id;
      
      $hotel = Hotel::select('id')->where('user_id', $owner)->get()->toArray();
      $types = RoomType::select('id')->whereIn('hotel_id', $hotel)->get()->toArray();
      $room = Room::select('id')->whereIn('id', $types)->get()->toArray();
      return $room;
    }

    /**
    *  validate action by user type
    */
    private function validateAction($userType, $id) {
      $book = Booking::select('room_id')->where('id', $id)->first()->room_id;
      foreach ($this->filterBookings($userType) as  $item) {
        if($item['id']==$book)
          return true;
      }
      return false;
    
    }
}
