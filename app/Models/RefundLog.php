<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'amount',
        'response',
        'request',
    ];
}
