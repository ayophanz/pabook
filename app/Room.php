<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
	protected $fillable = [
                'type_id',
    			'name', 
    			'description', 
    			'price',
    			'image',
    			'total_room'
    			];

    protected $with = ['roomRefer', 'roomFeature', 'roomGallery'];           

    public function roomRefer() {
        return $this->belongsTo(RoomType::class, 'type_id', 'id'); 
    }

    public function roomFeature() {
        return $this->belongsTo(RoomMeta::class, 'id', 'room_id')->where('meta_key', 'room_feature'); 
    }

    public function roomGallery() {
        return $this->belongsTo(RoomMeta::class, 'id', 'room_id')->where('meta_key', 'room_gallery'); 
    }			
}
