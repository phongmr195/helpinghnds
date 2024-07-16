<?php

namespace App\Models\Traits\Filter;

use Carbon\Carbon;

trait PaymentFilter
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
     * Filter by Transaction ID or Order ID
     */
    public function filterTransactionIdOrderId($query, $value)
    {
        return $query->where('transaction_id', 'like', '%' . $value . '%')
            ->orWhere('order_id', 'like', '%' . $value . '%');
    }
}