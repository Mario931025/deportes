<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'id' => 1, 
            'name' => 'Super',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456'),
            'last_name' => 'Admin',
            'phone' => '+595 21 123 456',
            'city_id' => 1, 
            'document_number' => '1234567',
            'birthday' => Carbon::parse('1969-01-01'),
            'academy_id' => 1, 
            'role_id' => 5, 
            'grade_id' => 1, 
            'active' => true
        ]);
    }
}
