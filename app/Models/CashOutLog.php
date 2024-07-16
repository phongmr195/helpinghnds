<?php

namespace App\Models;

use App\Models\Traits\Filter\CashoutFilter;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashOutLog extends Model
{
    use HasFactory, SoftDeletes, Filterable, CashoutFilter;

    public const WAITING = 0;
    public const APPROVE = 1;
    public const CANCEL = 2;
    public const FAIL = 3;

    protected $table = 'cash_out_logs';
    protected $fillable = [
        'worker_id',
        'card_id',
        'cash_out_amount',
        'status',
        'current_balance',
        'reason',
        'transaction_id',
    ];

    protected $filterable = [
        'status',
        'worker_id',
    ];

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id', 'id');
    }

    public function cardInfo()
    {
        return $this->belongsTo(Card::class, 'card_id', 'id');
    }
}
