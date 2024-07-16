<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddCardLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'access_token',
        'order_id',
        'user_id',
        'status',
        'response',
        'card_no',
        'type',
        'request_header',
        'request_params',
        'amount',
    ];
}
