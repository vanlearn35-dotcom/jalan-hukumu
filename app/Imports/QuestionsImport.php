<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class QuestionsImport implements ToModel, WithHeadingRow, WithMultipleSheets
{
    protected $packageId;
    public $rows = [];

    public function __construct($packageId)
    {
        $this->packageId = $packageId;
    }

    /**
     * Menangani banyak sheet. 
     * Logika pengolahan setiap sheet diarahkan ke class ini sendiri ($this).
     */
    public function sheets(): array
    {
        return [
            'listening' => $this,
            'structure' => $this,
            'reading'   => $this,
        ];
    }

    /**
     * Logika pemetaan baris Excel ke array data database.
     */
    public function model(array $row)
    {
        // Skip jika kolom number kosong atau bukan angka
        if (!isset($row['number']) || empty($row['number'])) {
            return null;
        }

        // Dekode JSON options secara otomatis
        $options = null;
        if (isset($row['options'])) {
            // Bersihkan string jika ada karakter aneh dan dekode ke array
            $decoded = json_decode($row['options'], true);
            $options = is_array($decoded) ? $decoded : null;
        }

        // Mapping field berdasarkan header Excel Anda
        $data = [
            'package_id'    => $this->packageId,
            'number'        => (int) $row['number'],
            'section'       => isset($row['section']) ? strtolower($row['section']) : null,
            'part'          => $row['part'] ?? null,
            'type'          => $row['type'] ?? 'mc',
            'passage_group' => $row['passage_group'] ?? null,
            'passage_html'  => $row['passage_html'] ?? null,
            'content_html'  => $row['content_html'] ?? null,
            'answer_key'    => $row['answer_key'] ?? null,
            'score_weight'  => $row['score_weight'] ?? 1,
            // Field khusus Listening
            'cue_start'     => isset($row['cue_start']) ? (int) $row['cue_start'] : null,
            'cue_end'       => isset($row['cue_end']) ? (int) $row['cue_end'] : null,
            'options'       => $options,
        ];

        // Simpan ke array public agar bisa diambil oleh Controller untuk Preview
        $this->rows[] = $data;

        /**
         * Return null karena kita tidak ingin menyimpan langsung ke DB lewat class ini.
         * Penyimpanan dilakukan di QuestionController@importConfirm menggunakan transaksi DB.
         */
        return null;
    }
}