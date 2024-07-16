<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $table = 'order_details';

    protected $fillable = [
        'order_id',
        'status_id',
        'begin_at',
        'begin_end',
        'begin_pause',
        'cancel_at',
        'cancel_reason',
        'service_id',
        'service_name',
        'service_child_name',
        'price',
        'currency',
        'note_description',
        'phone',
        'latitude',
        'longtitude',
        'amount',
        'amount_tip',
        'tip',
        'tip_type',
        'fee_app'
    ];

    //ket them table services
    function services()
    {
        return $this->hasOne(Service::class, 'service_id', 'id');
    }
}
