<?php
// Home

use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

Breadcrumbs::for('home', function ($trail) {
    $trail->push('Home', route('admin.home'));
});

// Home > overview
Breadcrumbs::for('overview', function ($trail) {
    $trail->parent('home');
    $trail->push('Overview', route('admin.dashboard'));
});

// Home > users
Breadcrumbs::for('users', function ($trail) {
    $trail->parent('home');
    $trail->push('Users', '#');
});

// Home > users > account
Breadcrumbs::for('account', function ($trail) {
    $trail->parent('home');
    $trail->push('Account', '#');
});

// Home > users > customer
Breadcrumbs::for('customer', function ($trail) {
    $trail->parent('users');
    $trail->push('Customers', route('admin.users.list-customer'));
});

// Home > users > customer
Breadcrumbs::for('worker', function ($trail) {
    $trail->parent('users');
    $trail->push('Workers', route('admin.users.list-worker'));
});

// Home > users customer/worker > user
Breadcrumbs::for('user_detail', function ($trail, $user) {
    $parent = 'users';
    
    if($user->user_type == 'client'){
        $parent = 'customer';
    }

    if($user->user_type == 'worker'){
        $parent = 'worker';
    }

    $trail->parent($parent);
    $trail->push($user->name, route(getRouteNameUserDetail($user->user_type), ['user' => $user->id]));
});

// Home > orders

Breadcrumbs::for('orders', function($trail){
    $trail->parent('home');
    $trail->push('Orders', route('admin.orders.list'));
});

// Home > orders > detail
Breadcrumbs::for('order_detail', function($trail, $order){
    $trail->parent('orders');
    $trail->push($order->detail->service_name, route('admin.orders.detail', ['order' => $order->id]));
});

// Home > payment

Breadcrumbs::for('payment', function($trail){
    $trail->parent('home');
    $trail->push('Payments', route('admin.payment'));
});

// Home > report

Breadcrumbs::for('report', function($trail){
    $trail->parent('home');
    $trail->push('Reports', route('admin.report'));
});

// Home > settings

Breadcrumbs::for('settings', function($trail){
    $trail->parent('home');
    $trail->push('Settings', route('admin.settings'));
});

// Home > cashout

Breadcrumbs::for('cashout', function($trail){
    $trail->parent('home');
    $trail->push('Cashout', route('admin.pages.cashout'));
});

// Home > logs

Breadcrumbs::for('logs', function($trail){
    $trail->parent('home');
    $trail->push('Logs', route('admin.logs_views'));
});