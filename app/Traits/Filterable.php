<?php

namespace App\Traits;
use Illuminate\Support\Str;

trait Filterable
{
    /**
     * Filter multiple field
     *
     * @param array $params
     */
    public function scopeFilterWithParams($query, $params)
    {
        foreach ($params as $field => $value) {
            $method = 'filter' . Str::studly($field);
            $fieldQuery = !empty($this->table) ? $this->table . '.' . $field : $field;
    
            if (empty($value) || is_null($value)) {
                continue;
            }

            if (method_exists($this, $method)) {
                $this->{$method}($query, $value);
                continue;
            }
    
            if (empty($this->filterable) || !is_array($this->filterable)) {
                continue;
            }
    
            if (in_array($field, $this->filterable)) {
                $query->where($fieldQuery, $value);
                continue;
            }
        }

        return $query;
    }
}