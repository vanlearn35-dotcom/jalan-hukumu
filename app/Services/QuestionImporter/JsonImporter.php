<?php

namespace App\Services\QuestionImporter;

use RuntimeException;

class JsonImporter implements ImporterInterface
{
    public function parse(string $path): array
    {
        $json = json_decode(file_get_contents($path), true);

        if (!$json || !isset($json['questions'])) {
            throw new RuntimeException('Format JSON tidak valid');
        }

        $data = [];

        foreach ($json['questions'] as $q) {
            $data[] = [
                'section'       => $json['section'],
                'part'          => $json['part'] ?? null,
                'number'        => $q['number'],
                'type'          => $json['section'] === 'structure' ? 'error' : 'mc',
                'content_html'  => $q['content_html'] ?? null,
                'options'       => $q['options'] ?? null,
                'answer_key'    => $q['answer_key'] ?? null,
                'audio_path'    => $q['audio_path'] ?? null,
                'cue_start'     => $q['cue_start'] ?? null,
                'cue_end'       => $q['cue_end'] ?? null,
                'score_weight'  => 1,
            ];
        }

        return $data;
    }
}
