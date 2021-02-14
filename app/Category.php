<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['image', 'vector', 'title_en', 'title_ar', 'type', 'deleted'];

    public function drsLawyers() {
        return $this->hasMany('App\DoctorsLawyers', 'category_id');
    }

    public function services() {
        return $this->hasMany('App\Service', 'category_id');
    }
}
