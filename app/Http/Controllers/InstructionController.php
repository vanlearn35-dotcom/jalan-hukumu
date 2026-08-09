<?php

namespace App\Http\Controllers;

use App\Imports\InstructionsImport;
use App\Models\Instruction;
use App\Models\Package;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InstructionController extends Controller
{
    public function index(Package $package)
    {
        $instructions = $package->instructions()->orderBy('section')->get();

        return view('admin.instructions.index', compact('package', 'instructions'));
    }

    /**
     * Menyimpan instruksi baru
     */
    public function store(Request $request, Package $package)
{
    $data = $request->validate([
        'id'           => 'nullable|exists:instructions,id', // Validasi ID jika ada
        'section'      => 'required|in:listening,structure,reading',
        'part'         => 'nullable|string|max:2',
        'content_html' => 'required|string',
    ]);

    // Jika ID ada, Laravel akan mencari berdasarkan ID tersebut.
    // Jika ID null, Laravel akan mencari berdasarkan section & part dalam package ini.
    Instruction::updateOrCreate(
        [
            'id' => $request->id, 
        ],
        [
            'package_id'   => $package->id,
            'section'      => $data['section'],
            'part'         => $data['part'],
            'content_html' => $data['content_html']
        ]
    );

    $message = $request->id ? 'Instruksi diperbarui!' : 'Instruksi baru ditambahkan!';
    return back()->with('success', $message);
}

    /**
     * Update instruksi yang sudah ada
     */
    public function update(Request $request, Package $package, Instruction $instruction)
    {
        // Pastikan instruksi milik package yang benar
        if ($instruction->package_id !== $package->id) {
            return back()->with('error', 'Aksi tidak diizinkan.');
        }

        $data = $request->validate([
            'section' => 'required|in:listening,structure,reading',
            'part' => 'nullable|string|max:2',
            'content_html' => 'required|string',
        ]);

        $instruction->update($data);

        return redirect()->route('admin.instructions.index', $package->id)
            ->with('success', 'Instruksi berhasil diperbarui!');
    }

    public function import(Request $request, Package $package)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:2048']);

        try {
            Excel::import(new InstructionsImport($package->id), $request->file('file'));

            return back()->with('success', 'Data instruksi berhasil diimpor!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor data: '.$e->getMessage());
        }
    }

    public function destroy(Package $package, Instruction $instruction)
    {
        if ($instruction->package_id !== $package->id) {
            return back()->with('error', 'Instruksi tidak valid.');
        }

        $instruction->delete();

        return back()->with('success', 'Instruksi berhasil dihapus.');
    }
}
