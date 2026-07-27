<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class TeamTableSeeder extends Seeder
{
    public function run(): void
    {
        $alpha = Team::create(['name' => 'Team Alpha',   'description' => 'Primary photography and videography team.', 'status' => 'active']);
        $beta  = Team::create(['name' => 'Team Beta',    'description' => 'Secondary team for events and coverage.',      'status' => 'active']);
        $gamma = Team::create(['name' => 'Team Gamma',   'description' => 'Special events and drone coverage team.',      'status' => 'active']);

        $alpha->members()->attach([Staff::where('email', 'juan@harvymance.com')->first()->id, Staff::where('email', 'maria@harvymance.com')->first()->id, Staff::where('email', 'jose@harvymance.com')->first()->id]);
        $beta->members()->attach([Staff::where('email', 'andres@harvymance.com')->first()->id, Staff::where('email', 'gabriela@harvymance.com')->first()->id]);
        $gamma->members()->attach([Staff::where('email', 'diego@harvymance.com')->first()->id, Staff::where('email', 'juan@harvymance.com')->first()->id]);
    }
}
