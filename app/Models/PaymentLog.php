<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Filterable;
use App\Models\Traits\Attribute\PaymentAttribute;
use App\Models\Traits\Filter\PaymentFilter;

class PaymentLog extends Model
{
    use HasFactory, SoftDeletes, Filterable, PaymentAttribute, PaymentFilter;

    public const PAYMENT_DONE = 1;
    public const PAYMENT_FAILED = 2;
    public const PAYMENT_REFUND = 3;
    public const PAYMENT_PROCESSING = 0;

    protected $fillable = [
        'order_id',
        'user_id',
        'worker_id',
        'transaction_id',
        'type',
        'status',
        'amount',
        'nation_code',
        'card_type',
        'request_payment',
        'response_payment',
    ];

    protected $filterable = [
        'type',
        'status',
        'card_type',
        'order_id',
        'worker_id',
        'nation_code'
    ];

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id', 'id')->where('user_type', User::IS_WORKER);
    }
}
