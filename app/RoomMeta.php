<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomMeta extends Model
{
    protected $fillable = [
                'room_id',
    			'meta_key', 
    			'value'
    			];
}
