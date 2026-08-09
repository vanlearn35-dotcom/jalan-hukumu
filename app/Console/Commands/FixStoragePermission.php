<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixStoragePermission extends Command
{
    protected $signature = 'storage:fix';
    protected $description = 'Ensure storage folders exist and are writable';

    public function handle()
    {
        $paths = [
            storage_path('app'),
            storage_path('app/public'),
            storage_path('app/public/audios'),
        ];

        foreach ($paths as $path) {
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
                $this->info("Created: $path");
            }

            if (!is_writable($path)) {
                @chmod($path, 0777);
                $this->warn("Fixed permission: $path");
            }
        }

        $this->info('Storage permission check completed.');
    }
}
