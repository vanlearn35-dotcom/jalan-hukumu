<?php

namespace App\Services\QuestionImporter;

class AikenImporter implements ImporterInterface
{
    public function parse(string $path): array
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES);
        $data = [];
        $buffer = [];

        foreach ($lines as $line) {
            if (trim($line) === '') {
                if ($buffer) {
                    $data[] = $this->parseBlock($buffer);
                    $buffer = [];
                }
            } else {
                $buffer[] = $line;
            }
        }

        if ($buffer) {
            $data[] = $this->parseBlock($buffer);
        }

        return $data;
    }

    protected function parseBlock(array $lines): array
    {
        $question = array_shift($lines);
        $options = [];
        $answer = null;

        foreach ($lines as $line) {
            if (str_starts_with($line, 'ANSWER:')) {
                $answer = trim(str_replace('ANSWER:', '', $line));
            } else {
                [$key, $text] = explode('.', $line, 2);
                $options[trim($key)] = trim($text);
            }
        }

        return [
            'section' => 'structure',
            'type' => 'mc',
            'content_html' => $question,
            'options' => $options,
            'answer_key' => $answer,
            'score_weight' => 1,
        ];
    }
}

