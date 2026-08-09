<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Package;
use App\Models\Question;
use Illuminate\Support\Facades\Hash;

class InitSeeder extends Seeder
{
    public function run(): void
    {
       

        // PACKAGE TOEFL ITP
        $package = Package::create([
            'name' => 'TOEFL ITP Simulation',
            'description' => 'Listening, Structure, Reading',
            'status' => 'published',
        ]);

        
    }
}
