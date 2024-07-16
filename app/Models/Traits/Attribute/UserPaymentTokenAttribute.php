<?php

namespace App\Models\Traits\Attribute;

trait UserPaymentTokenAttribute
{
    /**
     * Get bank type attribute
     *
     * @return string value
     */
    public function getBankTypeAttribute($value) {
        return array_key_exists($value, config('constant.card_type')) ? config('constant.card_type.' . $value) : 'ATM';
    }
}
