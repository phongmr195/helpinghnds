<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Services\Admin\UserService;
use App\Services\Api\CountryService;
use App\Services\Admin\OrderService;
use App\Services\Admin\PaymentService;
use App\Traits\ImageUpload;
use App\Traits\ApiResponser;
use App\Http\Requests\Admin\User\CreateAccountFormRequest;
use App\Http\Requests\Admin\User\UpdateAccountFormRequest;
use App\Services\Api\FcmService;

class UserController extends Controller {

    use ImageUpload,
        ApiResponser;

    protected $userService;
    protected $countryService;
    protected $orderService;
    protected $paymentService;

    public function __construct(UserService $userService, CountryService $countryService, OrderService $orderService, PaymentService $paymentService) {
        $this->userService = $userService;
        $this->countryService = $countryService;
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
    }

    /**
     * List users
     */
    public function index() {
        $users = $this->userService->listUser();
        return view('admin.users.index', compact('users'));
    }

    /**
     * User detail
     * @param User
     * @return $user
     */
    public function detail(User $user) {
        $userDetail = $this->userService->detail($user);
        $totalEarned = $this->orderService->getTotalEarned($userDetail->orders->pluck('id')->toArray());
        $workerInfo = $this->userService->getWorkerInfo($user);
        $view = 'admin.users.customer-detail';
        if ($userDetail->user_type == 'worker') {
            $view = 'admin.users.worker-detail';
        }

        return view($view, compact('userDetail', 'workerInfo', 'totalEarned'));
    }

    /**
     * Get list worker activity
     */
    public function getListWorkerActivity(Request $request)
    {
        $data = $this->userService->getListWorkerActivity($request->only('user_id', 'from_date', 'to_date'));

        return $this->createJsonDatatableWorkerActivity($data->orders);
    }

     /**
     * Filter worker activity history
     */
    public function filterTotalEarned(Request $request)
    {   
        $data = $this->userService->getListWorkerActivity($request->only('user_id', 'from_date', 'to_date'));
        $totalEarned = $this->orderService->getTotalEarned($data->orders->pluck('id')->toArray());
        $totalCashData = $this->paymentService->getTotalCashInCashOut(['worker_id' => $request->user_id, 'from_date' => $request->from_date, 'to_date' => $request->to_date]);
        $from = !empty($request->from_date) ? $request->from_date : 'time immemorial';
        $to = !empty($request->to_date) ? $request->to_date : 'currently';
        $text = "Activity from $from to $to";

        $dataOutputs = [
            'text' => $text,
            'total_earned' => $totalEarned,
            'total_cash_out' => formatCurrency($totalCashData->total_cash_out ?? 0, 'vn')
        ];

        return $this->success($dataOutputs);
    }

    /**
     * Update user
     * @return $user
     */
    public function updateUser(Request $request, User $user) {
        $data = $request->only(['name', 'first_name', 'last_name', 'address', 'email', 'phone', 'gender', 'latitude', 'longtitude', 'number_id', 'type_number_id', 'avatar', 'id_card_before', 'id_card_after', 'user_type', 'password']);
        if (isset($request->avatar)) {
            $avatar = $this->saveImage($request->avatar, 'images/', public_path('/uploads/images/' . $user->avatar));
            $data['avatar'] = $avatar;
        }

        $user = $this->userService->updateUser($user, $data);

        if ($user) {
            return $this->success(['message' => 'Update profile success!']);
        }
    }

    /**
     * List customer
     */
    public function listCustomer() {
        // $customers = $this->userService->listCustomer();
        // return view('admin.users.customer', compact('customers'));

        return view('admin.users.customer', ['route_refresh' => 'admin.users.list-customer']);
    }

    /**
     * Json list customer
     */
    public function getListCustomer(Request $request) {
        $customers = $this->userService->getListCustomerWithFilter($request->only(['gender', 'status', 'dates', 'name', 'phone']));

        return $this->createJsonDatatable($customers);
    }

    /**
     * Filter customer
     */
    public function filterCustomer(Request $request) {
        // $customers = $this->userService->filterCustomer($request->all());
        // return view('admin.users.customer', compact('customers'));
        return view('admin.users.customer', ['route_refresh' => 'admin.users.list-customer']);
    }

    /**
     * List worker
     */
    public function listWorker() {
        // $workers = $this->userService->listWorker();
        // return view('admin.users.worker', compact('workers'));
        return view('admin.users.worker', ['route_refresh' => 'admin.users.list-worker']);
    }

    /**
     * Json list worker
     */
    public function getListWorker(Request $request) {
        $workers = $this->userService->getListWorkerWithFilter($request->only(['number_id', 'rating', 'gender', 'status', 'dates', 'name', 'phone', 'worker_status', 'is_working']));

        return $this->createJsonDatatable($workers);
    }

    /**
     * Filter worker
     */
    public function filterWorker(Request $request) {
        // $workers = $this->userService->filterWorker($request->all());
        // return view('admin.users.worker', compact('workers'));
        return view('admin.users.worker', ['route_refresh' => 'admin.users.list-worker']);
    }

    /**
     * Create json datatable user
     *
     * @param $data
     */
    public function createJsonDatatable($data) {
        return datatables()
                ->eloquent($data)
                ->editColumn('fullname', function($user) {
                    return
                    '<div class="info">
                        <div class="user-avatar">' . getAvatarHtml($user) . '</div>
                        <div class="name-and-phone">
                            <div class="name">
                                <span>
                                    <b>' . $user->name . '</b>
                                </span>
                            </div>
                            <div class="phone">
                                <span>
                                    ' . $user->phone . '
                                </span>
                            </div>
                        </div>
                    </div>';
                })
                ->editColumn('worker_status', function($user){
                    return $user->worker_status != 0 ? '<i class="fas fa-circle text-success icon-small"></i>' : '<i class="fas fa-circle text-gray icon-small"></i>';
                })
                ->editColumn('is_working', function($user){
                    return $user->is_working != 0 ? '<i class="fas fa-circle text-danger icon-small"></i>' : '';
                })
                ->editColumn('id_number', function($user) {
                    return '<b>' . $user->number_id . '</b> <br/> <span>' . $user->type_number_id . '</span>';
                })
                ->editColumn('rating', function($user) {
                    return '<div class="user-rating">' . showRatingStar($user->ratings_avg_rating) . '</div>';
                })
                ->editColumn('status', function($user) {
                    return
                    '<div class="user-status">
                        <i class="' . getClassIconUserStatus($user->status) . '"></i>
                        <div class="text-status">
                            <span class="' . getTextStatus($user->status) . '"><b>' . config('constant.user_status.' . $user->status) . '</b></span>
                        </div>
                    </div>';
                })
                ->editColumn('gender', function($user) {
                    return '<b>' . $user->gender . '</b>';
                })
                ->editColumn('created_at', function($user) {
                    return '<b>' . formatDateTime($user->created_at, 'Y-m-d') . '</b> <br> ' . formatDateTime($user->created_at, 'H:i:s');
                })
                ->editColumn('balance', function($user){
                    return formatCurrency($user->balance ?? 0, $user->nation_code ?? 'vn');
                })
                ->editColumn('currency', function($user){
                    return !is_null($user->country) ? $user->country->currency : 'vnd';
                })
                ->editColumn('action', function($user) {
                    return getActionHtml($user);
                })
                ->rawColumns(['fullname', 'balance', 'currency', 'worker_status', 'is_working', 'id_number', 'rating', 'status', 'gender', 'created_at', 'action'])
                ->skipTotalRecords()
                ->toJson();
    }

    /**
     * Create json datatable for worker activity history
     *
     * @param $data
     *
     * @return mixed
     */
    public function createJsonDatatableWorkerActivity($data)
    {
        return datatables($data)
            ->editColumn('order_id', function($order){
                return $order->id;
            })
            ->editColumn('service', function($order){
                return $order->detail->service_name;
            })
            ->editColumn('date', function($order){
                return formatDateTime($order->created_at, 'm-d-Y');
            })
            ->editColumn('duration', function($order){
                return round($order->detail->working_total_minute / 60);
            })
            ->editColumn('fee', function($order){
                return formatCurrency($order->detail->price, $order->nation_code);
            })
            ->editColumn('amount', function($order){
                return formatCurrency($order->detail->amount, $order->nation_code);
            })
            ->editColumn('amount_tip', function($order){
                return formatCurrency($order->detail->amount_tip, $order->nation_code);
            })
            ->editColumn('fee_app', function($order){
                return formatCurrency($order->detail->fee_app ?? 0, $order->nation_code);
            })
            ->editColumn('total_earned', function($order){
                return formatCurrency((int)$order->detail->amount - (int)$order->detail->fee_app, $order->nation_code);
            })
            ->editColumn('action', function($order){
                return 
                '<a href="'.route('admin.orders.detail', [$order->id]).'" class="dropdown-item btn btn-danger btn-sm" data-id="'.$order->id.'">
                    <i class="fas fa-eye"></i>
                </a>';
            })
            ->rawColumns(['order_id', 'service', 'date', 'duration', 'fee', 'amount', 'amount_tip', 'fee_app', 'total_earned', 'action'])
            ->toJson();
    }


    /**
     * Update user status
     */
    public function updateUserStatus(Request $request, User $user) {
        $userUpdate = $this->userService->updateUserStatus($user, $request->status);
        return $this->success(['message' => 'Update status success!']);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request, User $user) {
        $userUpdate = $this->userService->resetPassword($user, $request->new_password);
        return $this->success(['message' => 'Update password success!']);
    }

    /**
     * Remove user
     */
    public function removeUser(Request $request, User $user) {
        $userDel = $this->userService->removeUser($user);
        if ($userDel) {
            return $this->success(['message' => 'Delete user success!']);
        }
    }

    /**
     * List account
     */
    public function listAccount() {
        $arrRoleNames = ['root', 'admin'];
        $roles = Role::select('id', 'name')->with('page')->whereNotIn('name', $arrRoleNames)->get();
        $firstRole = $roles->first();
        $pages = Page::select('id', 'name')->orderBy('order', 'asc')->get();
        $rolePages = [];
        if ($firstRole && $firstRole->page) {
            $rolePages = json_decode($firstRole->page->page_ids) ?? [];
        }

        $route_refresh = 'admin.users.list-account';

        return view('admin.users.account', compact('roles', 'pages', 'rolePages', 'route_refresh'));
    }

    /**
     * Get list user account
     */
    public function getListAccount(Request $request) {
        $users = $this->userService->getListAccountWithFilter($request->only('name', 'phone', 'role_id', 'status', 'dates'));

        return $this->createJsonDatatableUserAccount($users);
    }

    /**
     * Create user account
     */
    public function createAccount(CreateAccountFormRequest $request) {
        $user = $this->userService->createUserAccount($request->only('name', 'password', 'email', 'phone', 'role_data', 'status', 'gender'));
        if ($user) {
            return $this->success($user);
        }
    }

    /**
     * Update user account
     */
    public function updateUserAccount(UpdateAccountFormRequest $request, User $user) {
        $update = $this->userService->updateUserAccount($user, $request->only('name', 'role_data', 'gender', 'status'));
        if ($update) {
            return $this->success($update);
        }
    }

    /**
     * Get user account detail
     */
    public function getDataUserAccount(Request $request) {
        $userDetail = $this->userService->getUserDetailByID($request->user_id);
        $arrRoleNames = ['root', 'admin'];
        $roles = Role::select('id', 'name')->with('page')->whereNotIn('name', $arrRoleNames)->get();
        $htmlFormUpdate = view('admin.partials.form-update-account', ['userDetail' => $userDetail, 'roles' => $roles])->render();

        if ($userDetail) {
            return $this->success(['html_form' => $htmlFormUpdate]);
        }

        return $this->error();
    }

    /**
     * Create json datatable user account
     */
    public function createJsonDatatableUserAccount($data) {
        return datatables($data)
            ->editColumn('user_role', function($user) {
                if (!is_null($user->userRole)) {
                    return $user->userRole->name;
                }
                return '';
            })
            ->editColumn('status', function($user) {
                return
                '<div class="user-status">
                    <i class="' . getClassIconUserStatus($user->status) . '"></i>
                    <div class="text-status">
                        <span class="' . getTextStatus($user->status) . '"><b>' . config('constant.user_status.' . $user->status) . '</b></span>
                    </div>
                </div>';
            })
            ->editColumn('created_at', function($user) {
                return formatDateTime($user->created_at, 'Y-m-d');
            })
            ->editColumn('action', function($user) {
                return getActionUserAccountHtml($user);
            })
            ->rawColumns(['user_role', 'status', 'gender', 'created_at', 'action'])
            ->toJson();
    }

    /**
     * Addmin push call video worker
     */
    public function pushToCallUser(Request $request) {
        $userDetail = $this->userService->getUserDetailByID($request->userID);
        if ($userDetail && $userDetail->device_token && isset($request->zoomID) && isset($request->zoomPasscode)) {
            //push data
            $notiData = [
                'title' => 'HelpingHnds is calling...',
                'body' => 'Admin HelpingHnds is calling...',
                'image' => asset('assets/images/icon-app-for-client.jpg'),
                'meetingInfo' => array(
                    'username' => $userDetail->first_name . '-' . $userDetail->phone,
                    'meetingNumber' => $request->zoomID,
                    'password' => $request->zoomPasscode
                ),
                'fcm' => [
                    'type' => 'FCM-ADMIN-CALL-ZOOM'
                ]
            ];

            $fcm = new FcmService();
            $fcm->sendNotification($userDetail->device_token, $notiData, 'android');
            die('1');
        }
        die('0');
    }
}
