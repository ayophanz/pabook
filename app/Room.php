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
    			'total_room',
                'status'
    			];

    protected $with = ['roomType', 'roomFeature', 'roomFeatureOptional', 'roomGallery', 'roomNumbering'];           

    public function roomType() {
        return $this->belongsTo(RoomType::class, 'type_id', 'id'); 
    }

    public function roomFeature() {
        return $this->belongsTo(RoomMeta::class, 'id', 'room_id')->where('meta_key', 'room_feature'); 
    }

    public function roomFeatureOptional() {
        return $this->belongsTo(RoomMeta::class, 'id', 'room_id')->where('meta_key', 'room_feature_optional'); 
    }
    
    public function roomNumbering() {
        return $this->belongsTo(RoomMeta::class, 'id', 'room_id')->where('meta_key', 'room_numbering'); 
    }

    public function roomGallery() {
        return $this->belongsTo(RoomMeta::class, 'id', 'room_id')->where('meta_key', 'room_gallery'); 
    }			
}
