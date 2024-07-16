<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'page_id',
        'block_id',
        'type',
        'controller'
    ];
}
