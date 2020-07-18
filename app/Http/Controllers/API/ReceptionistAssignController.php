<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Gate\OwnerAndAdminRequest;
use App\Http\Requests\User\RecepCapRequest;
use App\Http\Controllers\Controller;
use App\Models\ReceptionistAssign;
use Illuminate\Http\Request;
use App\Models\Hotel;
use Helpers;

class ReceptionistAssignController extends Controller
{
    public function __construct() {
        $this->middleware(['auth:api', 'verified', 'two_factor_auth']);
    }

    public function index(OwnerAndAdminRequest $ownerAndAdminRequest, $recep_id) 
    {
        $owner = Helpers::ownerId();
        return Hotel::where('status', 'verified')->where('user_id', $owner)->with(['receptionistAssign' => function($query) use ($recep_id, $owner){
            $query->where('receptionist_id', $recep_id)->where('owner_id', $owner);
        }])->orderBy('created_at', 'desc')->get();
    }

    public function recepCap(OwnerAndAdminRequest $ownerAndAdminRequest, RecepCapRequest $request) 
    {
        $data = [];
        $assign = ReceptionistAssign::where('receptionist_id', $request->recep)->where('owner_id', Helpers::ownerId())->where('hotel_id', $request->hotelId);
        
        if ($request->type === 'add_room') $data = ['can_add_room' => (int) $request->action];
        if ($request->type === 'edit_room') $data = ['can_edit_room' => (int) $request->action];
        if ($request->type === 'delete_room') $data = ['can_delete_room' => (int) $request->action];

        if ($request->type === 'add_room_type') $data = ['can_add_room_type' => (int) $request->action];
        if ($request->type === 'edit_room_type') $data = ['can_edit_room_type' => (int) $request->action];
        if ($request->type === 'delete_room_type') $data = ['can_delete_room_type' => (int) $request->action];

        if ($assign->count() > 0) $assign->update($data); 
        else {
            $data['receptionist_id'] = (int) $request->recep;
            $data['owner_id'] = (int) Helpers::ownerId();
            $data['hotel_id'] = (int) $request->hotelId;
            $assign->create($data);
        }
    }
}
