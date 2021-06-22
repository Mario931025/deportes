<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grade;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->grades() as $data) {
            Grade::create($data);
        }
    }
    
    protected function grades()
    {
        return [
            ['id' => 1, 'name' => 'P0'],
            ['id' => 2, 'name' => 'P1'],
            ['id' => 3, 'name' => 'P2'],
            ['id' => 4, 'name' => 'P3'],
            ['id' => 5, 'name' => 'P4'],
            ['id' => 6, 'name' => 'P5'],
            ['id' => 7, 'name' => 'G1'],
            ['id' => 8, 'name' => 'G2'],
            ['id' => 9, 'name' => 'G3'],
            ['id' => 10, 'name' => 'G4'],
            ['id' => 11, 'name' => 'G5'],
            ['id' => 12, 'name' => 'E1'],
            ['id' => 13, 'name' => 'E2'],
            ['id' => 14, 'name' => 'E3'],
            ['id' => 15, 'name' => 'E4'],            
            ['id' => 16, 'name' => 'E5'],
        ];
    }     
}
