<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Option;

class OptionController extends Controller
{
    public function __construct() {
        $this->middleware(['auth:api', 'verified']);
   }

   public function index($id=null) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');

    	if(\Gate::allows('superAdmin') && $id!=null) 
        	return Option::where('meta_key', 'global_base_currency')->where('meta_value', $id)->first();
      
      if(\Gate::allows('hotelOwner')) 
        	return Option::where('meta_key', 'global_base_currency')->where('meta_value', $this->ownerId())->first();
       
   }

   public function create(Request $request) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');

   	  $option = null;
   		$data = [
   			    'base_currency'  => 'required|string|max:191'
   		];

   		$dataCreate = [
   			'meta_key' => 'global_base_currency',
   			'meta_value' => $this->ownerId(),
   			'value'   => $request['base_currency']
   		];

   		if(\Gate::allows('superAdmin')) {
   			$data['owner_id'] = 'required|numeric|min:1';
   			$dataCreate['meta_value'] = $request['owner_id'];
   		}

   		$this->validate($request, $data);

   		if(\Gate::allows('superAdmin')) {
   			$option = $this->createUpdate($request['owner_id'], $dataCreate);
   		}elseif(\Gate::allows('hotelOwner')) {
   			$option = $this->createUpdate($this->ownerId(), $dataCreate);
   		}
   		return $option;  

   }



   /**
    *  Extra function
    */


    /**
    *  Owner Id security verification
    */
    private function ownerId() {
        return auth('api')->user()->id;
    }

    /**
    *  Create and update
    */
    private function createUpdate($id, $dataCreate) {
    	$option = null;
    	$exist = Option::where('meta_key', 'base_currency')->where('meta_value', $id)->first();
		if(!$exist) {
			$option = Option::create($dataCreate);
		}else{
			$option = Option::where('meta_key', 'base_currency')->where('meta_value', $id)->update($dataCreate);
		}

		return $option;
    }

}
