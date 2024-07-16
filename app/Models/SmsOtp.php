<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsOtp extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'phone',
        'otp',
        'type',
        'expired_date',
        'status',
    ];
}
