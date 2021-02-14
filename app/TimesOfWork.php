<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimesOfWork extends Model
{
    protected $fillable = ['day', 'holiday', 'from', 'to', 'doctor_lawyer_id', 'count'];
}
