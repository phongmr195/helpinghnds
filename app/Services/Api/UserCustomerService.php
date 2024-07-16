<?php

namespace App\Services\Api;

use App\Models\Order;
use App\Models\Customer;
use App\Services\BaseService;
use App\Exceptions\GeneralException;
use App\Models\UserOtp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

/**
 * Class UserService.
 */
class UserCustomerService extends BaseService
{
    /**
     * UserCustomerService constructor.
     *
     * @param  Customer  $customer
     */
    public function __construct(Customer $customer)
    {
        $this->model = $customer;
    }
}
