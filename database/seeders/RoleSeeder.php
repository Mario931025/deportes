<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->roles() as $data) {
            Role::create($data);
        }
    }
    
    protected function roles()
    {
        return [
            ['id' => 1, 'name' => 'student', 'description' => 'Solo Alumno'],
            ['id' => 2, 'name' => 'instructor', 'description' => 'Instructor'],
            ['id' => 3, 'name' => 'country-manager', 'description' => 'Country Manager'],
            ['id' => 4, 'name' => 'latam-manager', 'description' => 'LATAM Manager'],
            ['id' => 5, 'name' => 'admin', 'description' => 'Super Administrador'],
        ];
    }        
}
