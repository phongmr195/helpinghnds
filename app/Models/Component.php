<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Component extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'component_type_id',
        'name',
        'value'
    ];

    protected $casts = [
        'component_ids' => 'array',
    ];
}
