<?php

namespace App\Models\Traits;

use Carbon\Carbon;

trait CommonFilter
{
    /**
     * Filter by date
     */
    public function filterDates($query, $value)
    {
        $dates = explode(' - ', $value);
        $from = Carbon::createFromFormat('m-d-Y', $dates[0])->format('Y-m-d');
        $to = Carbon::createFromFormat('m-d-Y', $dates[1])->format('Y-m-d');

        return $query->where(function($q) use ($from, $to){
            $q->whereBetween('created_at', [$from, $to]);
        });    
    }
}