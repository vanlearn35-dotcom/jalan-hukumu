<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class LfmAudioPackage
{
    public function handle(Request $request, Closure $next)
    {
        if (
            $request->is('laravel-filemanager*') &&
            $request->get('type') === 'Audio'
        ) {
            $packageId = $request->get('package_id');

            if ($packageId) {
                $folder = "audios/{$packageId}";

                // 1️⃣ Buat folder jika belum ada
                if (!Storage::disk('public')->exists($folder)) {
                    Storage::disk('public')->makeDirectory($folder);
                }

                // 2️⃣ OVERRIDE ROOT AUDIO
                config([
                    'lfm.folder_categories.audio.folder_name' => $folder,
                ]);

                // 3️⃣ ⚠️ INI YANG PALING PENTING
                // RESET SESSION PATH LFM
                Session::forget('lfm_last_path');
                Session::forget('lfm_current_path');

                // Set paksa ke folder package
                Session::put('lfm_current_path', $folder);
            }
        }

        return $next($request);
    }
}
