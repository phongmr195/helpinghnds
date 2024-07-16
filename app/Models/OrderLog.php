<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'worker_id',
        'order_id',
        'type',
        'by_user'
    ];
}
