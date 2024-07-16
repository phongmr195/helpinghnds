<?php

namespace App\Models\Traits\Attribute;

trait UserAttribute
{
    /**
     * Get gender
     * @return string value
     */
    public function getGenderAttribute($value) {
        if (!empty($value)) {
            return in_array($value, array('m', 'Male', 'male')) ? 'Male' : 'Female';
        }

        return $value;
    }

    /**
     * Get type_number_id
     * @return string value
     */
    public function getTypeNumberIdAttribute($value) {
        return !empty($value) ? config('constant.type_number_id.' . $value) : $value;
    }

    /**
     * Get fullname
     */
    public function getNameAttribute($value) {
        if (!empty($this->attributes['first_name']) && !empty($this->attributes['last_name'])) {
            return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
        }
        return $value;
    }
}
