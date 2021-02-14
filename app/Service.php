<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['title_en', 'title_ar', 'type', 'category_id'];

    public function category() {
        return $this->belongsTo('App\Category', 'category_id');
    }

    public function doctorLawyers() {
        return $this->belongsToMany('App\DoctorsLawyers', 'doctor_lawyer_services', 'service_id', 'doctor_lawyer_id');
    }
}
