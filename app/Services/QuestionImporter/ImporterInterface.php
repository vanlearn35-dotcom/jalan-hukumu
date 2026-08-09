<?php

namespace App\Services\QuestionImporter;

interface ImporterInterface
{
    public function parse(string $path): array;
}
