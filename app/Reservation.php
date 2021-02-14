<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['date',
    'time',
    'cost',
    'status',
    'payment_method',
    'user_name',
    'phone',
    'user_confirm',
    'work_time_id',
    'user_id',
    'doctor_lawyer_id',
    'latitude',
    'longitude',
    'address_en',
    'address_ar',
    'city_ar',
    'city_en',
    'reservation_for',
    'user_cancell_reason',
    'type'
    ];

    public function doctorLawyer() {
        return $this->belongsTo('App\DoctorsLawyers', 'doctor_lawyer_id');
    }

    public function workTime() {
        return $this->belongsTo('App\TimesOfWork', 'work_time_id');
    }
}
