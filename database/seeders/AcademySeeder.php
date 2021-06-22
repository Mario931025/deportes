<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academy;

class AcademySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Academy::create([
            'id' => 1, 
            'name' => 'SIT', 
            'country_id' => 1, 
            'active' => true
        ]);
    }
}
