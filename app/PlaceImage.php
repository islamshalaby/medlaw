<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlaceImage extends Model
{
    protected $fillable = ['image', 'doctor_lawyer_id'];
}
