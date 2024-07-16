<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    /**
     * Parent page
     */
    public function parent()
    {
        return $this->belongsTo(Service::class, 'parent_id', 'id');
    }

    /**
     * Children page
     */
    public function children()
    {
        return $this->hasMany(Service::class, 'parent_id', 'id');
    }
}
