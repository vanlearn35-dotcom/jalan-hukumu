<?php

namespace App\Imports;

use App\Models\Instruction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InstructionsImport implements ToModel, WithHeadingRow
{
    protected int $packageId;

    public function __construct(int $packageId) {
        $this->packageId = $packageId;
    }

    public function model(array $row) {
        return Instruction::updateOrCreate(
            [
                'package_id' => $this->packageId,
                'section'    => $row['section'],
                'part'       => $row['part'] ?? null,
            ],
            [
                'content_html' => $row['content_html'],
                'order'        => $row['order'] ?? 0,
            ]
        );
    }
}