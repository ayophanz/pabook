<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Gate\OwnerAndAdminRequest;
use App\Http\Requests\Option\CreateRequest;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Helpers;

class OptionController extends Controller
{
    public function __construct() {
		$this->middleware(['auth:api', 'verified', 'two_factor_auth']);
   }

   public function index(OwnerAndAdminRequest $ownerAndAdminRequest, $id=null) 
   {
		if (\Gate::allows('superAdmin') && $id!=null) return Option::where('meta_key', 'global_base_currency')->where('meta_value', $id)->first();
	  	if (\Gate::allows('hotelOwner')) return Option::where('meta_key', 'global_base_currency')->where('meta_value', Helpers::ownerId())->first();
   
	}

   public function create(OwnerAndAdminRequest $ownerAndAdminRequest, CreateRequest $request) 
   {
   	  	$option = null;
   		$dataCreate = [
   			'meta_key' => 'global_base_currency',
   			'meta_value' => Helpers::ownerId(),
   			'value'   => $request->base_currency
   		];

   		if (\Gate::allows('superAdmin')) $dataCreate['meta_value'] = $request->user_id;

   		$validated = $request->validated();

   		if (\Gate::allows('superAdmin')) $option = Helpers::createUpdateOption($request->user_id, $dataCreate);
   		elseif (\Gate::allows('hotelOwner')) $option = Helpers::createUpdateOption(Helpers::ownerId(), $dataCreate);
   		return $option;  
   }
}
