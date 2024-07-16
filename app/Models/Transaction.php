<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'transactions';
    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'card_id',
        'card_type',
        'session_id'
    ];

    public function order()
    {
        return $this->hasOne(Order::class, 'transaction_id', 'id');
    }
}
