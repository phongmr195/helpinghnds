<?php

namespace App\Models\Traits\Filter;

use App\Models\User;
use Carbon\Carbon;

trait OrderFilter
{
    /**
     * Filter by phone
     */
    public function filterPhone($query, $value)
    {
        if(!empty($value)){
            $dataValue = explode('_', $value);
            $userType = $dataValue[0];
            $phone = $dataValue[1];

            if($userType == User::IS_USER){
                return $query->with(['detail', 'customer'])
                    ->orWhereHas('customer', function($q) use ($phone, $userType) {
                        $q->where('phone', 'like', '%'.$phone.'%')
                        ->where('user_type', $userType);
                    });
            }

            return $query->with(['detail', 'worker'])
                    ->orWhereHas('worker', function($q) use ($phone, $userType) {
                        $q->where('phone', 'like', '%'.$phone.'%')
                        ->where('user_type', $userType);
                    });

        }
    }

    /**
     * Filter by order ID
     */
    public function filterOrderId($query, $value)
    {
        return $query->where('id', 'like', '%'.$value.'%');
    }

    /**
     * Filter by customer name
     */
    public function filterCustomerName($query, $value)
    {
        return $query->whereHas('customer', function($q) use ($value) {
            $q->where('name', 'like', '%'.$value.'%');
        });
    }

    /**
     * Filter by worker name
     */
    public function filterWorkerName($query, $value)
    {
        return $query->whereHas('worker', function($q) use ($value) {
            $q->where('name', 'like', '%'.$value.'%');
        });
    }

    /**
     * Filter with from date
     */
    public function filterFromDate($query, $value)
    {
        $fromDate = Carbon::createFromFormat('m-d-Y', $value)->format('Y-m-d');

        return $query->where('created_at', '>=', $fromDate);
    }

    /**
     * Filter with to date
     */
    public function filterToDate($query, $value)
    {
        $toDate = Carbon::createFromFormat('m-d-Y', $value)->format('Y-m-d');

        return $query->where('created_at', '<=', $toDate);
    }
}