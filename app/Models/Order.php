<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use App\Models\OrderDetail;
use App\Models\Traits\Scope\OrderScope;
use App\Models\Traits\Filter\OrderFilter;
use App\Models\Traits\CommonFilter;
use App\Traits\Filterable;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Filterable;
    use OrderScope;
    use OrderFilter;
    use CommonFilter;
    
    public const BEGIN_AT = 1;
    public const BEGIN_END = 2;
    public const BEGIN_PAUSE = 3;
    public const CANCEL = 4;
    public const IS_WORKING = 1;

    protected $fillable = [
        'user_id',
        'worker_id',
        'transaction_id',
        'address',
        'address_title',
        'order_status',
        'payment_status',
        'session_payment_id',
        'work_time',
        'token_payment',
        'nation_code',
        'tip_status',
    ];

    // protected $casts = ['order_status' => 'boolean'];

    /**
     * Params for filter
     */
    protected $filterable = [
        'id',
        'order_status',
        'nation_code'
    ];

    /**
     * Handle when has event created, updated
     */
    protected static function boot()
    {
        parent::boot();

        Order::created(function($model){
            Cache::flush();
        });

        Order::updated(function($model){
            Cache::flush();
        });
    }

    /**
     * Order detail
     */
    public function detail()
    {
        return $this->hasOne(OrderDetail::class, 'order_id', 'id');
    }

    /**
     * User customer
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->where('user_type', User::IS_USER);
    }

    /**
     * User worker
     */
    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id', 'id')->where('user_type', User::IS_WORKER);
    }

    /**
     * Country
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'nation_code', 'alt');
    }

    /**
     * Payment info
     */
    public function paymentInfo()
    {
        return $this->belongsTo(UserTokenPayment::class, 'token_payment', 'pay_token');
    }

}
