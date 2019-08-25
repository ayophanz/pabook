<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Option;

class OptionController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
   }

   public function index() {

    //if(\Gate::allows('superAdmin')) 
      //return Booking::with('room')->get();
   }

   public function create() {

   		$data = [
   			    'base_currency'  => 'required|string|max:191'
   		];

   		$dataCreate = [
   			'meta_key' => 'owner_id',
   			'meta_value' => 'base_currency',
   			'value'   => $request['base_currency']
   		];

   		$this->validate($request, $data);

   		// if(\Gate::allows('superAdmin')) {
   		// 	$exist = Option::where('owner_id', )
   		// } 

   }

}
