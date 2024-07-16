<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUserAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserAdminSeeder::class);
    }
}

class UserAdminSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Administrators',
                'email' => 'super_admin@gmail.com',
                'password' => Hash::make('superadmin123'),
                'user_type' => 'super_admin'
            ],
            [
                'name' => 'Admin',
                'email' => 'admin1@gmail.com',
                'password' => Hash::make('admin123'),
                'user_type' => 'admin'
            ],
            [
                'name' => 'Account',
                'email' => 'account@gmail.com',
                'password' => Hash::make('account123'),
                'user_type' => 'account'
            ],
        ]);
    }
}
