<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserOtp extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'user_otps';
    protected $fillable = [
        'user_id',
        'phone',
        'otp',
        'token'
    ];
}
