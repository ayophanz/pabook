<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $fillable = [
                'hotel_id',
    			'name'
    			];


    protected $with = ['roomTypeRefer'];           

    public function roomTypeRefer() {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'id'); 
    }
}
