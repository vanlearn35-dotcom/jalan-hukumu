<?php

namespace App\Services\QuestionImporter;

use Maatwebsite\Excel\Facades\Excel;

class ExcelImporter implements ImporterInterface
{
    public function parse(string $path): array
    {
        $rows = Excel::toArray([], $path)[0];
        unset($rows[0]); // header

        $data = [];

        foreach ($rows as $row) {
            $data[] = [
                'section' => $row[0],
                'number' => $row[1],
                'type' => 'mc',
                'content_html' => $row[2],
                'options' => [
                    'A' => $row[3],
                    'B' => $row[4],
                    'C' => $row[5],
                    'D' => $row[6],
                ],
                'answer_key' => $row[7],
            ];
        }

        return $data;
    }
}

