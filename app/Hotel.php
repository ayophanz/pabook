<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
                'owner_id',
    			'name', 
    			'address', 
    			'city',
    			'state_province',
    			'country',
    			'zip_code',
    			'phone_number',
    			'email',
    			'image'
    			];


    // protected $with = [];           

    // public function userRefer() {
    //     return $this->belongsTo(User::class, 'user_id', 'id'); 
    // }
}
