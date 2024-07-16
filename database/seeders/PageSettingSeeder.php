<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PageSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PageSeeder::class);
        $this->call(PageDetailSeeder::class);
        $this->call(BlockSeeder::class);
        $this->call(ComponentTypeSeeder::class);
        $this->call(ComponentSeeder::class);
    }
}

class PageSeeder extends Seeder
{
    public function run(){
        DB::table('pages')->insert([
            [
                'name' => 'Customer Demo',
                'slug' => 'custom-demo',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
        ]);
    }
}

class PageDetailSeeder extends Seeder
{
    public function run(){
        DB::table('page_details')->insert([
            [
                'page_id' => 1,
                'block_id' => 1,
                'type' => 'component',
                'controller' => 'admin/page/PageController',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),

            ],
        ]);
    }
}

class BlockSeeder extends Seeder
{
    public function run(){
        DB::table('blocks')->insert([
            [
                'name' => 'Filter Customer',
                'type' => 'form',
                'component_ids' => json_encode([1, 2, 3, 4]),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
        ]);
    }
}

class ComponentTypeSeeder extends Seeder
{
    public function run(){
        DB::table('component_types')->insert([
            [
                'name' => 'select',
                'description' => 'Select option value',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),

            ],
            [
                'name' => 'input',
                'description' => 'Enter value to input',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),

            ],
        ]);
    }
}

class ComponentSeeder extends Seeder
{
    public function run(){
        DB::table('components')->insert([
            [
                'component_type_id' => 2,
                'name' => 'ID Number',
                'value' => 'id-number',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'component_type_id' => 1,
                'name' => 'Gender',
                'value' => 'gender',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'component_type_id' => 1,
                'name' => 'Rating',
                'value' => 'rating',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'component_type_id' => 1,
                'name' => 'Status',
                'value' => 'status',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'component_type_id' => 2,
                'name' => 'Created At',
                'value' => 'dates',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'component_type_id' => 2,
                'name' => 'submit',
                'value' => 'submit',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
        ]);
    }
}
