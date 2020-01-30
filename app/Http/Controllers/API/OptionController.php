<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Option;
use App\Helpers\Helpers;

class OptionController extends Controller
{
    public function __construct() {
        $this->middleware(['auth:api', 'verified', 'two_factor_auth']);
   }

   public function index($id=null) {
      if(!\Gate::allows('superAdmin') && !\Gate::allows('hotelOwner'))
          return die('not allowed');

    	if(\Gate::allows('superAdmin') && $id!=null) 
        	return Option::where('meta_key', 'global_base_currency')->where('meta_value', $id)->first();
      
      if(\Gate::allows('hotelOwner')) 
        	return Option::where('meta_key', 'global_base_currency')->where('meta_value', Helpers::ownerId())->first();
       
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
   			'meta_value' => Helpers::ownerId(),
   			'value'   => $request['base_currency']
   		];

   		if(\Gate::allows('superAdmin')) {
   			$data['user_id'] = 'required|numeric|min:1';
   			$dataCreate['meta_value'] = $request['user_id'];
   		}

   		$this->validate($request, $data);

   		if(\Gate::allows('superAdmin')) {
   			$option = Helpers::createUpdateOption($request['user_id'], $dataCreate);
   		}elseif(\Gate::allows('hotelOwner')) {
   			$option = Helpers::createUpdateOption(Helpers::ownerId(), $dataCreate);
   		}
   		return $option;  
   }

}
