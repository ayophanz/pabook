<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Book\CreateRequest;
use App\Http\Requests\Book\ContinueBookRequest;
use App\Http\Requests\Gate\AllRequest;

use Illuminate\Notifications\notifiable;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Notifications\RoomReservation;
use App\Events\incompleteBooking;
use Helpers;

class BookController extends Controller
{
    public function __construct() {
        $this->middleware(['auth:api', 'verified', 'two_factor_auth']);
    }

    public function index(AllRequest $request) 
    { 
      if (\Gate::allows('superAdmin')) return Booking::with('room')->get(); 
      if (\Gate::allows('hotelOwner')) return Booking::whereIn('roomId', Helpers::getRoomsIdBaseUser('owner'))->with('room')->get();
      if (\Gate::allows('hotelReceptionist')) return Booking::whereIn('roomId', Helpers::getRoomsIdBaseUser('recep'))->with('room')->get();  
    }

    public function create(AllRequest $gateRequest, CreateRequest $request) 
    {
      $validated = $request->validated();
      $data = [
                'name'          => 'incomplete',
                'phone_no'      => 'incomplete',
                'room_id'       => (int)$request->roomWithRoomType,
                'hotel_id'      => (int)$request->hotel,
                'date_start'    => date($request->checkInD),
                'date_end'      => date($request->checkOutD),
                'amount'        => (float)$request->totalAmount,
                'currency'      => $request->currency_use,
                'user_id'       => auth('api')->user()->id,
                'many_room'     => (int)$request->manyRoom,
                'many_adult'    => (int)$request->manyAdult,
                'many_child'    => (int)$request->manyChild,
                'rooms_no'      =>  json_encode($request->rooms_no),
                'status'        => 'incomplete',
                'optional_amen' =>  json_encode($request->addOnOptionalAmen)
              ];      

      $book = Booking::create($data);
      // event(new incompleteBooking($book->id));       
      return Booking::where('id', $book->id)->with('room', 'hotel')->get();
    }
   
    public function continueBooking(AllRequest $request, $id) 
    {    
      return Booking::where('id', (int)$id)->with('room', 'hotel')->get();
    }

    public function saveContinueBooking(AllRequest $gateRequest, ContinueBookRequest $request, $id) 
    {
      if ($request->email=='') $request->email = 'mail@mail.com';

      $validated = $request->validated();  

      $data = [
        'name'    => $request->name,
        'phone_no' => $request->phoneNo,
        'email'   => ($request->email=='mail@mail.com') ? null : $request->email,
        'status'  => 'active'
      ]; 

      Booking::where('id', $id)->update($data);
      $book = Booking::where('id', $id)->with('room', 'hotel')->first();
      Notification::send(Helpers::userToNotify($book->roomId), new RoomReservation($book));

      return $book;
    }

    public function autoCancel(AllRequest $gateRequest) 
    {
      if (\Gate::allows('hotelOwner')) return Booking::whereIn('roomId', Helpers::getRoomsIdBaseUser('owner'))->whereDate('dateEnd', '<=', date('Y-m-d'))->where('status', 'book')->update(['status'=>'cancel']);
      if (\Gate::allows('hotelReceptionist')) return Booking::whereIn('roomId', Helpers::getRoomsIdBaseUser('recep'))->whereDate('dateEnd', '<=', date('Y-m-d'))->where('status', 'book')->update(['status'=>'cancel']);
    }

    public function bookCancel(AllRequest $gateRequest, $id)
    {      
      if ($this->validateAction('owner', $id) || $this->validateAction('recep', $id))
        return Booking::where('id', $id)->where('status', 'book')->update(['status'=>'cancel']);
      else
        return die('this room is not yours');
    }

    public function checkOut(AllRequest $gateRequest, $id) 
    {      
      if ($this->validateAction('owner', $id) || $this->validateAction('recep', $id))
        return Booking::where('id', $id)->update(['status'=>'checkout']);
      else
        return die('this room is not yours');
    }

    public function checkIn(AllRequest $gateRequest, $id) 
    {
      if ($this->validateAction('owner', $id) || $this->validateAction('recep', $id))
        return Booking::where('id', $id)->update(['status'=>'checkin']);
      else
        return die('this room is not yours');  
    }

    public function markAsRead(AllRequest $gateRequest, $id=null) 
    {
      if ($id!=null) auth()->user()->unreadNotifications->where('id', $id)->markAsRead();
      return auth()->user()->load('notifications');
    }

    public function incompleteBooking(AllRequest $gateRequest) 
    {
      return Booking::where('status', 'incomplete')->get();
    }

    public function cancelBooking(AllRequest $gateRequest, $id) 
    {
      return Booking::where('id', $id)->delete();
    }

    public function singleDetails(AllRequest $gateRequest, $id) 
    {
      return Booking::where('id', $id)->with('hotel', 'room')->first();
    }

}
