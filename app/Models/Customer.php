<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use App\Models\UserProfile;
use App\Models\Order;
use App\Models\Traits\Attribute\UserAttribute;
use App\Traits\Filterable;
use App\Models\Traits\Scope\UserScope;
use Spatie\Permission\Traits\HasRoles;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles, SoftDeletes, Filterable, UserAttribute, UserScope;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'first_name',
        'email',
        'phone',
        'gender',
        'number_id',
        'type_number_id',
        'address',
        'longtitude',
        'latitude',
        'password',
        'status',
        'device_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // protected $with = ['orders'];

    /**
     * Params for filter
     */
    protected $filterable = [
        'status',
        'gender',
        'number_id'
    ];

    /**
     * User has profile
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    /**
     * User has orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * User has ratings
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * User has location
     */
    public function location()
    {
        return $this->hasOne(UserLocation::class);
    }

}
