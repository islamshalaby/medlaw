<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Support\Facades\Auth;

class DoctorsLawyers extends Authenticatable
{
    protected $fillable = [
    'first_name',
    'last_name',
    'app_name_en',
    'app_name_ar',
    'email',
    'phone',
    'password',
    'professional_title_en',
    'professional_title_ar',
    'image_professional_title',
    'image_profession_license',
    'personal_image',
    'about_en',
    'about_ar',
    'city_en',
    'city_ar',
    'address_en',
    'address_ar',
    'reservation_cost',
    'recieving_reservation_phone',
    'gender',
    'latitude',
    'longitude',
    'category_id',
    'type',
    'active',
    'profile_completed',
    'reservation_type'
    ];

    protected $appends = ['custom'];
    public function getCustomAttribute()
    {
        $data['setting'] = Setting::where('id' ,1)->select('app_name_en' , 'app_name_ar' , 'logo')->first();
        return $data;
    }

    public function category() {
        return $this->belongsTo('App\Category', 'category_id');
    }

    public function times() {
        return $this->hasMany('App\TimesOfWork', 'doctor_lawyer_id');
    }

    public function drLawServices() {
        return $this->hasMany('App\DoctorLawyerService', 'doctor_lawyer_id');
    }

    public function placeImages() {
        return $this->hasMany('App\PlaceImage', 'doctor_lawyer_id');
    }
}
