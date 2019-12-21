<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{

    protected $fillable = [
                'user_id',
    			'name', 
    			'address', 
    			'city',
    			'state_province',
    			'country',
    			'zip_code',
    			'phone_number',
    			'email',
				'image',
				'status',
				'check_in',
				'check-out',
				'website',
				'verify_token',
				'hotel_rooms_no'
    			];


    protected $with = ['baseCurrency', 'globalBaseCurrency'];//rermove this if unnecessary                  

    public function globalBaseCurrency() {
        return $this->belongsTo(Option::class, 'user_id', 'meta_value')->where('meta_key', 'global_base_currency');
    }

    public function baseCurrency(){
        return $this->belongsTo(Option::class, 'id', 'meta_value')->where('meta_key', 'base_currency');
    }
}
