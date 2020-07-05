<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    protected $fillable = [
    			'user_id', 
    			'meta_key', 
    			'value'
    			];


    // protected $with = ['userRefer'];//rermove this if unnecessary                  

    // public function userRefer() {
    //     return $this->belongsTo(User::class, 'user_id', 'id'); 
    // }
}
