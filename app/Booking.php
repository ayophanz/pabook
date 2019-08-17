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
                'user_id',
                'status'
    			];
}
