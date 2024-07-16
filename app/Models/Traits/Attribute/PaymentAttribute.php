<?php

namespace App\Models\Traits\Attribute;

trait PaymentAttribute
{
    /**
     * Show cash type
     */
    public function getTypeAttribute($value)
    {
        return $value == 'cash_in' ? 'CASHIN' : 'CASHOUT';
    }

    /**
     * Show card type
     */
    public function getCardTypeAttribute($value)
    {
        return $value == 'IC' ? 'International card' : 'Local card';
    }

    // /**
    //  * Show status
    //  */
    // public function getStatusAttribute($value)
    // {
    //     return config('constant.vnpt.status.' . $value);
    // }
}
