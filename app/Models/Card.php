<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_name',
        'fullname',
        'bank_no',
        'code',
        'img_url',
        'bin_no',
    ];
}
