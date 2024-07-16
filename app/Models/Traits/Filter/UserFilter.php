<?php

namespace App\Models\Traits\Filter;

trait UserFilter 
{
    /**
     * Filter by name
     */
    public function filterName($query, $value)
    {
        return $query->where('name', 'like', '%' . $value . '%');    
    }

    /**
     * Filter by rating avg
     */
    public function filterRating($query, $value)
    {   
        return $query->having('ratings_avg_rating', '>=', $value)
            ->orderByDesc('ratings_avg_rating');
    }

    /**
     * Filter by phone
     */
    public function filterPhone($query, $value)
    {
        return $query->where('phone', 'like', '%'.$value.'%');
    }

    /**
     * Filter by number id
     */
    public function filterNumberId($query, $value)
    {   
        return $query->where('number_id', 'like', '%' . $value . '%');
    }

    /**
     * Filter by gender
     */
    public function filterGender($query, $value)
    {
        $arrValues = $value == 'f' ? ['f', 'Female'] : ['m', 'Male'];

        return $query->whereIn('gender', $arrValues);
    }
}