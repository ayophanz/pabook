<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
                'room_id',
    			'name', 
                'email', 
                'address',
    			'phone_no',
    			'date_start',
    			'date_end',
                'amount',
                'currency',
                'many_room',
                'optional_amen',
                'many_adult',
                'many_child',
                'rooms_no',
                'user_id',
                'hotel_id',
                'status'
    			];

    protected $with = ['room', 'hotel'];//rermove this if unnecessary                  

    public function room() {
        return $this->belongsTo(Room::class, 'room_id', 'id'); 
    }

    public function hotel() {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'id'); 
    }

    //public static function boot() {

        //parent::boot();

        // static::created(function($model){
            
        //     $adminAndOwner = User::where('role', 'super_admin')->first();

        //     Notification::send($adminAndOwner, new RoomReservation($model));
        // });
    //}            
}
