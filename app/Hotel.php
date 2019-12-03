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
				'image',
				'status',
				'check_in',
				'check-out'
    			];


    protected $with = ['baseCurrency', 'globalBaseCurrency'];           

    public function globalBaseCurrency() {
        return $this->belongsTo(Option::class, 'owner_id', 'meta_value')->where('meta_key', 'global_base_currency');
    }

    public function baseCurrency(){
        return $this->belongsTo(Option::class, 'id', 'meta_value')->where('meta_key', 'base_currency');
    }
}
