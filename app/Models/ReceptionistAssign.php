<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceptionistAssign extends Model
{
    protected $fillable = [
        'receptionist_id',
        'owner_id', 
        'hotel_id',
        'can_add_room',
        'can_edit_room',
        'can_delete_room',
        'can_add_room_type',
        'can_edit_room_type',
        'can_delete_room_type'
    ];

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id', 'id')->where('role', 'hotel_owner');
    }

    public function receptionist() {
        return $this->hasOne(User::class, 'receptionist_id', 'id')->where('role', 'hotel_receptionist'); 
    }

    public function hotel() {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'id'); 
    }
}
