<?php

namespace App\Services\Api;

use App\Models\Order;
use App\Models\Worker;
use App\Models\UserOtp;
use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

/**
 * Class UserService.
 */
class UserWorkerService extends BaseService
{
    /**
     * UserWorkerService constructor.
     *
     * @param  Worker  $worker
     */
    public function __construct(Worker $worker)
    {
        $this->model = $worker;
    }
}
