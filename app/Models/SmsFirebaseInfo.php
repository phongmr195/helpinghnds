<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsFirebaseInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'code',
        'status',
        'session_info',
        'expired_date',
        'type'
    ];
}
