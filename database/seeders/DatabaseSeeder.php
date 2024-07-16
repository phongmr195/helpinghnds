<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ServiceDatabaseSeeder::class);
        $this->call(OrderStatusDatabaseSeeder::class);
        $this->call(OrderDatabaseSeeder::class);
        $this->call(OrderDetailDatabaseSeeder::class);
    }
}

// Fake data for table services
Class ServiceDatabaseSeeder extends Seeder
{
    public function run(){
        DB::table('services')->insert([
            [
                'name' => 'Lite',
                'price' => 15,
                'image' => null,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'Medium',
                'price' => 20,
                'image' => null,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'Heavy',
                'price' => 25,
                'image' => null,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
        ]);
    }
}
// Fake data for table order_statuses
Class OrderStatusDatabaseSeeder extends Seeder
{
    public function run(){
        DB::table('order_statuses')->insert([
            [
                'status_name' => 'Begin At',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'status_name' => 'Begin End',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'status_name' => 'Begin Pause',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'status_name' => 'Cancel',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]
        ]);
    }
}
// Fake data for table order_statuses
Class OrderDatabaseSeeder extends Seeder
{
    public function run(){
        for($i = 0; $i < 10; $i++)
        {
            DB::table('orders')->insert([
                [
                    'user_id' => rand(0, 10),
                    'worker_id' => rand(0, 10),
                    'order_status' => 0,
                    'payment_status' => 0,
                    'session_payment_id' => null,
                    'address' => '150 Hai Ba Trung, Q.1',
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    
                ]
            ]);
        }
    }
}
// Fake data for table order_statuses
Class OrderDetailDatabaseSeeder extends Seeder
{
    public function run(){
        for($i = 0; $i < 10; $i++)
        {
            DB::table('order_details')->insert([
                [
                    'order_id' => rand(0, 10),
                    'status_id' => null,
                    'begin_at' => Carbon::now()->toDateTimeString(),
                    'begin_end' => Carbon::now()->toDateTimeString(),
                    'begin_pause' => Carbon::now()->toDateTimeString(),
                    'cancel_at' => Carbon::now()->toDateTimeString(),
                    'price' => 20,
                    'note_description' => 'Ghi chu danh cho worker',
                    'phone' => '+84934416235',
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ]);
        }
    }
}
