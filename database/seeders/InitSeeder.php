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
        // USERS
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Participant',
            'email' => 'participant@example.com',
            'password' => Hash::make('password'),
            'role' => 'participant',
            'is_active' => true,
        ]);

        // PACKAGE TOEFL ITP
        $package = Package::create([
            'name' => 'TOEFL ITP Simulation',
            'description' => 'Listening, Structure, Reading',
            'status' => 'published',
        ]);

        // LISTENING PART A (contoh)
        Question::create([
            'package_id' => $package->id,
            'section' => 'listening',
            'part' => 'A',
            'number' => 1,
            'type' => 'mc',
            'content_html' => 'What does the man mean?',
            'options' => [
                'A' => 'He agrees',
                'B' => 'He disagrees',
                'C' => 'He is unsure',
                'D' => 'He is surprised',
            ],
            'answer_key' => 'A',
            // 'audio_path' => 'audio/listening1.mp3',
            'cue_start' => 12,
            'cue_end' => 20,
        ]);

        // STRUCTURE
        Question::create([
            'package_id' => $package->id,
            'section' => 'structure',
            'number' => 31,
            'type' => 'error',
            'content_html' =>
                'The students <u>A</u> was <u>B</u> late for <u>C</u> class <u>D</u>.',
            'options' => [
                'A' => 'students',
                'B' => 'was',
                'C' => 'late',
                'D' => 'class',
            ],
            'answer_key' => 'B',
        ]);

        // READING
        Question::create([
            'package_id' => $package->id,
            'section' => 'reading',
            'number' => 51,
            'type' => 'reading',
            'passage_group' => 'R1',
            'passage_html' =>
                '<p>This passage discusses the importance of education...</p>',
            'content_html' =>
                'What is the main idea of the passage?',
            'options' => [
                'A' => 'Education systems',
                'B' => 'Teaching methods',
                'C' => 'Learning outcomes',
                'D' => 'School management',
            ],
            'answer_key' => 'A',
        ]);
    }
}
