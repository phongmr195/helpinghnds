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
use App\Models\Traits\Scope\UserScope;
use App\Models\Traits\Filter\UserFilter;
use App\Models\Traits\CommonFilter;
use App\Traits\Filterable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory; 
    use Notifiable;
    use HasApiTokens;
    use HasRoles;
    use SoftDeletes;
    use Filterable;
    use UserAttribute;
    use UserScope;
    use UserFilter;
    use CommonFilter;
    public const IS_USER = 'client';
    public const IS_WORKER = 'worker';
    public const IS_ADMIN = 'admin';
    public const OTP_DEFAULT = 111111;
    public const IS_INACTIVE = 0;
    public const IS_ACTIVE = 1;
    public const IS_PENDING = 2;
    public const IS_REJECTED = 3;
    public const WORKING_ON = 1;
    public const WORKING_OFF = 0;
    public const WORKER_OFF = 0;
    public const WORKER_ON = 1;
    public const NONE_USER_MESSAGE = 'Unauthenticated';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'number_id',
        'type_number_id',
        'id_card_before',
        'id_card_after',
        'user_type',
        'role_id',
        'address',
        'longtitude',
        'latitude',
        'country_id',
        'password',
        'status',
        'is_working',
        'worker_status',
        'device_token',
        'device_platform',
        'app_version',
        'balance',
        'nation_code',
        'final_rating'
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
        'role_id',
        'is_working',
        'worker_status',
    ];

    public function findForPassport($username)
    {
        return $this->where('phone', $username)->first();
    }
    
    /**
     * User has profile
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id')->select('id', 'user_id', 'avatar');
    }

    /**
     * User has orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'worker_id', 'id');
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

    /**
     * User role
     */
    public function userRole()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * User token payment
     */
    public function tokenPayments()
    {
        return $this->hasMany(UserTokenPayment::class);
    }

    /**
     * User country
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'nation_code', 'alt');
    }
}
