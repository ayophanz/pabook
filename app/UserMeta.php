<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    protected $fillable = [
    			'user_id', 
    			'meta_key', 
    			'value'
    			];


    // protected $with = ['userRefer'];           

    // public function userRefer() {
    //     return $this->belongsTo(User::class, 'user_id', 'id'); 
    // }
}
