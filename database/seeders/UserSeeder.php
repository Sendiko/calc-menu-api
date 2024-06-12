<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('restaurants')->insert([
            'name' => 'AdminResto',
            'address' => 'Purwokerto Barat, Banyumas',
            'phone_contact' => '082241626760',
            'email' => 'admin@adminresto.web.id',
            'password' => Hash::make('admin12345'),
        ]);

        function generateEmployeeId() {
            $today = date('Ymd');
            $id = $today . Str::random(6);
            return $id;
        }

        DB::table('employees')->insert([
            'employee_id' => generateEmployeeId(),
            'name' => 'AdminEmployeeResto',
            'email' => 'employeeAdmin@adminresto.web.id',
            'password' => Hash::make('employeeadmin123'),
            'restaurant_id' => '1',
        ]);
    }
}
