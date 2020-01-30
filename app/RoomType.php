<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $fillable = [
                'hotel_id',
                'name'
    			];
     
    protected $with = ['roomTypeHotel'];    

    public function roomTypeHotel() {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'id'); 
    }

    public function roomTypeRooms() {
        return $this->hasMany(Room::class, 'room_type_id');
    }
}
