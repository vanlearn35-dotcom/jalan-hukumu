<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AudioController extends Controller
{
    /* =========================
     | LIST AUDIO PER PACKAGE
     ========================= */
    public function index(Package $package)
    {
        $audios = Audio::where('package_id', $package->id)
            ->orderByDesc('version')
            ->get();

        return view('admin.audios.index', compact('package', 'audios'));
    }

    /* =========================
     | SIMPAN AUDIO FILE dan data
     ========================= */
    public function store(Request $request, Package $package)
    {
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,ogg|max:51200', // 50MB
        ]);

        return DB::transaction(function () use ($request, $package) {

            // Pastikan folder package ada
            $dir = "audios/{$package->id}";
            Storage::disk('public')->makeDirectory($dir);

            // Hitung versi
            $lastVersion = Audio::where('package_id', $package->id)->max('version');
            $version = ($lastVersion ?? 0) + 1;

            $slug = Str::slug($package->name, '_');
            $ext = $request->file('audio')->getClientOriginalExtension();

            $filename = "listening_{$slug}_v{$version}.{$ext}";
            $path = $request->file('audio')->storeAs($dir, $filename, 'public');

            // Nonaktifkan audio lama
            Audio::where('package_id', $package->id)->update(['is_active' => false]);

            // Simpan audio
            Audio::create([
                'package_id' => $package->id,
                'filename' => $filename,
                'path' => $path,
                'version' => $version,
                'is_active' => true,
            ]);

            // Update package
            $package->update(['audio_path' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Audio berhasil diupload',
            ]);
        });
    }

    /* =========================
     | SET AUDIO AKTIF MANUAL
     ========================= */
    public function select(Package $package, Audio $audio)
    {
        if ($audio->package_id !== $package->id) {
            abort(403, 'Audio tidak sesuai dengan package');
        }

        Audio::where('package_id', $package->id)
            ->update(['is_active' => false]);

        $audio->update(['is_active' => true]);

        $package->update([
            'audio_path' => $audio->path,
        ]);

        return back()->with('success', 'Audio berhasil diaktifkan');
    }

    /* =========================
     | HAPUS AUDIO (NON AKTIF SAJA)
     ========================= */
    public function destroy(Package $package, Audio $audio)
    {
        // pastikan audio milik package ini
        if ($audio->package_id !== $package->id) {
            abort(403, 'Audio tidak sesuai dengan package');
        }

        if ($audio->is_active) {
            return back()->with('error', 'Audio aktif tidak boleh dihapus');
        }

        Storage::disk('public')->delete($audio->path);
        $audio->delete();

        return back()->with('success', 'Audio berhasil dihapus');
    }
}
