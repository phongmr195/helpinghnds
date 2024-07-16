<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Admin\UserService;
use App\Services\Admin\OrderService;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $userService;
    protected $orderService;
    public function __construct(UserService $userService, OrderService $orderService)
    {
        $this->middleware('auth');
        $this->userService = $userService;
        $this->orderService = $orderService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $orders = $this->orderService->countListOrder();
        $users = $this->userService->countListUser();
        $listWorkerPending = $this->userService->listWorkerPending();
        $latestWork = $this->orderService->listOrderLatest();
        return view('dashboard', compact('users', 'orders', 'listWorkerPending', 'latestWork'));
    }
}
