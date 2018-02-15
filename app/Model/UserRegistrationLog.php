<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserRegistrationLog extends Model
{
    // связь с базой данных
    protected $table = 'user_registration_logs';

    //отключаем метки времени
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'registration_time'
    ];
}
