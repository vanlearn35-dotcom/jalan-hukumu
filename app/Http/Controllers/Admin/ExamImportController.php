<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\QuestionsImport;
use App\Imports\InstructionsImport;

class ExamImportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function show()
    {
        return view('admin.exam.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);

        Excel::import(new QuestionsImport, $request->file('file'));
        Excel::import(new InstructionsImport, $request->file('file'));

        return redirect()
            ->back()
            ->with('success', 'Soal & instruksi berhasil diimport');
    }
}
