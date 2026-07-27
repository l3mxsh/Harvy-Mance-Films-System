<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;

class StaffTableSeeder extends Seeder
{
    public function run(): void
    {
        $staff = [
            ['name' => 'Juan Dela Cruz',    'email' => 'juan@harvymance.com',    'contact_number' => '09171234567', 'password' => 'password'],
            ['name' => 'Maria Santos',      'email' => 'maria@harvymance.com',   'contact_number' => '09181234567', 'password' => 'password'],
            ['name' => 'Jose Rizal',        'email' => 'jose@harvymance.com',    'contact_number' => '09191234567', 'password' => 'password'],
            ['name' => 'Andres Bonifacio',  'email' => 'andres@harvymance.com',  'contact_number' => '09201234567', 'password' => 'password'],
            ['name' => 'Gabriela Silang',   'email' => 'gabriela@harvymance.com','contact_number' => '09211234567', 'password' => 'password'],
            ['name' => 'Diego Silang',      'email' => 'diego@harvymance.com',   'contact_number' => '09221234567', 'password' => 'password'],
        ];

        foreach ($staff as $s) {
            Staff::create($s);
        }
    }
}
