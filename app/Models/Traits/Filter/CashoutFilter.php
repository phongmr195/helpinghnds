<?php

namespace App\Models\Traits\Filter;

use Carbon\Carbon;

trait CashoutFilter
{
    /**
     * Filter with from date
     */
    public function filterFromDate($query, $value)
    {
        $fromDate = Carbon::createFromFormat('m-d-Y', $value)->format('Y-m-d H:i:s');

        return $query->where('created_at', '>=', $fromDate);
    }

    /**
     * Filter with to date
     */
    public function filterToDate($query, $value)
    {
        $toDate = Carbon::createFromFormat('m-d-Y', $value)->format('Y-m-d H:i:s');

        return $query->where('created_at', '<=', $toDate);
    }

    /**
     * Filter by nation code
     */
    public function filterNationCode($query, $value)
    {
        return $query->whereHas('worker', function($sq) use ($value) {
            if($value == 'vn'){
                $sq->where('nation_code', $value)
                    ->orWhereNull('nation_code');
            } else {
                $sq->where('nation_code', $value);
            }
        });
    }

    /**
     * Filter by phone
     */
    public function filterPhone($query, $value)
    {
        return $query->whereHas('worker', function($sq) use ($value) {
            $sq->where('phone', 'like' , '%' . $value . '%');
        });
    }
}