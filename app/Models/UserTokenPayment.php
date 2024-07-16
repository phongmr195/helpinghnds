<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Attribute\UserPaymentTokenAttribute;

class UserTokenPayment extends Model
{
    use HasFactory, SoftDeletes, UserPaymentTokenAttribute;

    protected $fillable = [
        'user_id',
        'pay_token',
        'bank_name',
        'bank_type',
        'card_no',
        'last_used_date',
        'payment_3rd',
        'customer_id',
        'payment_method_id',
        'card_brand',
    ];
}
