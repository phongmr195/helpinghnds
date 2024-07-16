<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreatePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PageSeeder::class);
    }
}

class PageSeeder extends Seeder
{
    public function run(){
        DB::table('pages')->insert([
            [
                'parent_id' => 0,
                'name' => 'Overview',
                'slug' => 'overview',
                'order' => 1,
                'route_name' => 'admin.dashboard',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'parent_id' => 0,
                'name' => 'Users',
                'slug' => 'users',
                'order' => 2,
                'route_name' => 'admin.users.list',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'parent_id' => 2,
                'name' => 'Customer',
                'slug' => 'customer',
                'order' => 3,
                'route_name' => 'admin.users.list-customer',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'parent_id' => 2,
                'name' => 'Worker',
                'slug' => 'worker',
                'order' => 4,
                'route_name' => 'admin.users.list-worker',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'parent_id' => 0,
                'name' => 'Orders',
                'slug' => 'orders',
                'order' => 5,
                'route_name' => 'admin.orders.list',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'parent_id' => 0,
                'name' => 'Payment',
                'slug' => 'payment',
                'order' => 6,
                'route_name' => 'admin.payment',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'parent_id' => 0,
                'name' => 'Report',
                'slug' => 'report',
                'order' => 7,
                'route_name' => 'admin.report',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'parent_id' => 0,
                'name' => 'Settings',
                'slug' => 'settings',
                'order' => 8,
                'route_name' => 'admin.settings',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
        ]);
    }
}
