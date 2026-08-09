<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Package;
use App\Models\Question;
use App\Models\Instruction;
use Illuminate\Support\Facades\File;

class ImportToeflPackage extends Command
{
    protected $signature = 'toefl:import {package_id} {path}';
    protected $description = 'Import TOEFL questions & instructions';

    public function handle()
    {
        $package = Package::findOrFail($this->argument('package_id'));
        $path = rtrim($this->argument('path'), '/');

        // ================= INSTRUCTION =================
        if (File::exists("$path/instructions.json")) {
            $data = json_decode(File::get("$path/instructions.json"), true);

            foreach ($data as $section => $content) {
                if (isset($content['content_html'])) {
                    Instruction::updateOrCreate(
                        [
                            'package_id' => $package->id,
                            'section' => $section,
                            'part' => null,
                        ],
                        [
                            'content_html' => $content['content_html'],
                            'order' => $content['order'] ?? 0,
                        ]
                    );
                } else {
                    foreach ($content as $part => $inst) {
                        Instruction::updateOrCreate(
                            [
                                'package_id' => $package->id,
                                'section' => $section,
                                'part' => $part,
                            ],
                            [
                                'content_html' => $inst['content_html'],
                                'order' => $inst['order'] ?? 0,
                            ]
                        );
                    }
                }
            }
        }

        // ================= QUESTIONS =================
        foreach (['listening', 'structure', 'reading'] as $section) {
            $file = "$path/$section.json";
            if (!File::exists($file)) continue;

            $questions = json_decode(File::get($file), true);

            foreach ($questions as $q) {
                Question::updateOrCreate(
                    [
                        'package_id' => $package->id,
                        'number' => $q['number'],
                    ],
                    [
                        'section' => $section,
                        'part' => $q['part'] ?? null,
                        'type' => $q['type'],
                        'content_html' => $q['content_html'] ?? null,
                        'passage_html' => $q['passage_html'] ?? null,
                        'passage_group' => $q['passage_group'] ?? null,
                        'options' => $q['options'] ?? null,
                        'answer_key' => $q['answer_key'] ?? null,
                        'audio_path' => $q['audio_path'] ?? null,
                        'cue_start' => $q['cue_start'] ?? null,
                        'cue_end' => $q['cue_end'] ?? null,
                    ]
                );
            }
        }

        $this->info('✅ Package imported successfully');
    }
}
