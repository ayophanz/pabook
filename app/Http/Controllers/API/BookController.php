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
                'name'         => 'incomplete',
                'phoneNo'      => 'incomplete',
                'roomId'       => (int)$request->roomWithRoomType,
                'hotelId'      => (int)$request->hotel,
                'dateStart'    => date($request->checkInD),
                'dateEnd'      => date($request->checkOutD),
                'amount'       => (float)$request->totalAmount,
                'currency'     => $request->currency_use,
                'userId'       => auth('api')->user()->id,
                'manyRoom'     => (int)$request->manyRoom,
                'manyAdult'    => (int)$request->manyAdult,
                'manyChild'    => (int)$request->manyChild,
                'roomsNo'      =>  json_encode($request->rooms_no),
                'status'       => 'incomplete',
                'optionalAmen' =>  json_encode($request->addOnOptionalAmen)
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
        'phoneNo' => $request->phoneNo,
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
