<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
                'roomId',
    			'name', 
                'email', 
                'address',
    			'phoneNo',
    			'dateStart',
    			'dateEnd',
                'amount',
                'currency',
                'manyRoom',
                'optionalAmen',
                'manyAdult',
                'manyChild',
                'roomsNo',
                'userId',
                'hotelId',
                'status'
    			];

    protected $with = ['room', 'hotel'];//rermove this if unnecessary                  

    public function room() {
        return $this->belongsTo(Room::class, 'roomId', 'id'); 
    }

    public function hotel() {
        return $this->belongsTo(Hotel::class, 'hotelId', 'id'); 
    }

    //public static function boot() {

        //parent::boot();

        // static::created(function($model){
            
        //     $adminAndOwner = User::where('role', 'super_admin')->first();

        //     Notification::send($adminAndOwner, new RoomReservation($model));
        // });
    //}            
}
