<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['date', 'doctor_lawyer_id'];

    public function drLawyer() {
        return $this->belongsTo('App\DoctorsLawyers', 'doctor_lawyer_id');
    }
}
