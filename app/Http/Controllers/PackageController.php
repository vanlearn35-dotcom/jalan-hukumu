<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    /* =======================
     | ADMIN (MANAGEMENT)
     ======================= */

    public function adminIndex()
    {
        $packages = Package::withCount('questions')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        // $package = Package::create($request->all());

        // Storage::disk('public')->makeDirectory(
        //     'audios/package-'.$package->id);

        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            // 'type' => 'required|in:listening,structure,reading',
            'audio_path' => 'nullable|file|mimes:mp3,wav',
            'duration' => 'nullable|integer',
        ]);

        if ($request->hasFile('audio_path')) {
            $data['audio_path'] = $request->file('audio_path')->store('audios', 'public');
        }

        Package::create($data);

        return redirect()->route('admin.packages')
            ->with('success', 'Paket berhasil ditambahkan');
    }

    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $package->update($validated);

        return redirect()
            ->route('admin.packages')
            ->with('success', 'Paket berhasil diperbarui');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return back()->with('success', 'Paket dihapus');
    }

    public function publish(Package $package)
    {
        // ❗ Optional safety: jangan publish jika belum ada soal
        if ($package->questions()->count() === 0) {
            return back()->withErrors([
                'publish' => 'Paket tidak dapat dipublish karena belum memiliki soal.',
            ]);
        }

        $package->update([
            'status' => $package->status === 'published'
                ? 'draft'
                : 'published',
        ]);

        return back()->with('success', 'Status paket berhasil diperbarui.');
    }

    public function refreshToken($id)
    {
        $package = \App\Models\Package::findOrFail($id);

        // Generate 5 karakter huruf besar acak
        $package->token_secret = strtoupper(Str::random(5));
        $package->save();

        return back()->with('success', 'Token berhasil diperbarui: '.$package->token_secret);
    }

    /* =======================
     | EXAM EXPERIENCE
     | (PARTICIPANT + ADMIN)
     ======================= */

    public function index()
    {
        $packages = Package::where('status', 'published')
            ->latest()
            ->get();

        return view('exam.index', compact('packages'));
    }

    public function show(Package $package)
    {
        abort_if($package->status !== 'published', 403);

        return view('exam.packages.show', compact('package'));
    }
}
