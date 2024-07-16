<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug'
    ];

    /**
     * Detail page
     */
    public function detail()
    {
        return $this->hasOne(PageDetail::class);
    }

    /**
     * Parent page
     */
    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id', 'id');
    }

    /**
     * Children page
     */
    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id', 'id');
    }
}
