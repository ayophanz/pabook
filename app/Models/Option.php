<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = [
                'id',
    			'meta_key', 
    			'meta_value', 
    			'value'
    			];
}
