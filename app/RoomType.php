<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $fillable = [
                'hotel_id',
                'name'
    			];
     
    protected $with = ['roomTypeHotel', 'roomTypeRooms'];//rermove this if unnecessary    

    public function roomTypeHotel() {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'id'); 
    }

    public function roomTypeRooms() {
        return $this->hasMany(Room::class, 'id');
    }
}
