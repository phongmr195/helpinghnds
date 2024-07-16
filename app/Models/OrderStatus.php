<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;

    public const PENDING = 0;
    public const WAITING_WORKER_ACCEPT = 1;
    public const WORKER_ACCEPTED = 2;
    public const WORKER_GOING = 3;
    public const WORKER_ARRIVE = 4;
    public const WORKING = 5;
    public const DONE = 6;
    public const FAILED = 7;
    public const PAUSE = 8;
    public const CANCEL = 12; //CANCEL ORDER
    public const WAITING_PAYMENT_OTP = 13; // Status wating for payment with ATM
    public const PAYMENT_SUCCESS_CODE = '00_000';

    protected $table = 'order_statuses';
}
