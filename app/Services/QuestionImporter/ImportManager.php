<?php

namespace App\Services\QuestionImporter;

use InvalidArgumentException;

class ImportManager
{
    public static function make(string $format): ImporterInterface
    {
        return match ($format) {
            'json'  => new JsonImporter(),
            'aiken' => new AikenImporter(),
            'excel' => new ExcelImporter(),
            default => throw new InvalidArgumentException('Format import tidak dikenali'),
        };
    }
}

