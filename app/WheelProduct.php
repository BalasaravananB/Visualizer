<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WheelProduct extends Model
{
    
	public function DifferentOffsets() {
	    return $this->hasMany('App\WheelProduct','wheeldiameter','wheeldiameter');
	}

	
}