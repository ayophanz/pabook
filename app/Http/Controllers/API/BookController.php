<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Booking;
use DateTime;

class BookController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
   }

   public function index() {

    if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner') || \Gate::allows('hotel_receptionist')) 
      return Booking::with('room')->get();

   }

   public function create(Request $request) {
   		$data = [
   			    'fullname'  => 'required|string|max:191',
   			    'email'     => 'required|string|email|max:191',
            'phone_no'  => 'required',
            'consent'   => 'accepted',
            'total'     => 'required',
            'dateStart' => 'required',
            'dateEnd'   => 'required',
            'room_id'   => 'required|numeric|min:1',
            'total'     => 'required|numeric',
  			    'amount'    => 'required|numeric|greater_than_field:total'
   		];

   		$customMessages = [
                        'greater_than_field' => 'The amount is not enough.'
                        ];

      $sdate = new \DateTime($request['dateStart']);
      $edate = new \DateTime($request['dateEnd']);   

   		$dataCreate = [
   			'room_id'   => $request['room_id'],
   			'name'      => $request['fullname'],
   			'email'     => $request['email'],
   			'phone_no'  => $request['phone_no'],
   			'dateStart' => $sdate,
   			'dateEnd'   => $edate,
   			'amount'    => $request['amount'],
        'status'    => $request['btnSubmit']
   		];

   		$this->validate($request, $data, $customMessages);

   		if(\Gate::allows('superAdmin') || \Gate::allows('hotelOwner') || \Gate::allows('hotel_receptionist')) 
        	return Booking::create($dataCreate);
   }
}
