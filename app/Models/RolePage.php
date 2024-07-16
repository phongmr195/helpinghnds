<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class RolePage extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'page_ids'
    ];

    protected $casts = [
        // 'page_ids' => 'array',
    ];

    /**
     * Handle when has event created, updated
     */
    protected static function boot()
    {
        parent::boot();

        RolePage::created(function($model){
            Cache::flush();
        });

        RolePage::updated(function($model){
            Cache::flush();
        });
    }
}
