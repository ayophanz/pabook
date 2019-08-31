<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
                'room_id',
    			'name', 
    			'email', 
    			'phone_no',
    			'dateStart',
    			'dateEnd',
                'amount',
                'currency',
                'user_id',
                'status'
    			];

    protected $with = ['room'];           

    public function room() {
        return $this->belongsTo(Room::class, 'room_id', 'id'); 
    }

    //public static function boot() {

        //parent::boot();

        // static::created(function($model){
            
        //     $adminAndOwner = User::where('role', 'super_admin')->first();

        //     Notification::send($adminAndOwner, new RoomReservation($model));
        // });
    //}            
}
