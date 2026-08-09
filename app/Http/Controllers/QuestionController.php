<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Package $package)
    {
        return view('admin.questions.index', [
            'package' => $package,
            'questions' => $package->questions()->orderBy('number')->get(),
        ]);
    }

    public function getAjaxData(Package $package, Question $question)
    {
        try {
            // Cek jika soal butuh teks dari Leader (Passage Group)
            if (empty($question->passage_html) && ! empty($question->passage_group)) {
                $leader = Question::where('package_id', $package->id)
                    ->where('passage_group', $question->passage_group)
                    ->whereNotNull('passage_html')
                    ->first();

                $question->passage_html = $leader ? $leader->passage_html : '<p class="text-muted">No passage text found for this group.</p>';
            }

            return response()->json($question);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Store atau Update Soal (Satu Method untuk handle keduanya dari modal/form)
     */
    public function store(Request $request, Package $package)
    {
        $data = $this->validatedData($request);

        // Cek jika ID ada, maka UPDATE. Jika tidak ada, maka CREATE.
        // Ini agar tombol EDIT di index bisa berfungsi ke satu route ini.
        $question = Question::updateOrCreate(
            ['id' => $request->id],
            array_merge($data, [
                'package_id' => $package->id,
                // Options dari form dikirim sebagai array ['A' => '...', 'B' => '...']
                // Cast di model akan otomatis mengubahnya ke JSON
                'options' => $request->options,
            ])
        );

        $package->updateQuietly([
            'total_questions' => $package->questions()->count(),
        ]);

        return redirect()
            ->route('admin.questions.index', $package->id)
            ->with('success', 'Soal berhasil disimpan');
    }

    public function import(Request $request, Package $package)
    {
        try {
            $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);

            // Mengasumsikan Anda menggunakan Laravel Excel
            // Data diambil dari file yang diupload
            $import = new \App\Imports\QuestionsImport($package->id);
            \Excel::import($import, $request->file('file'));

            \DB::transaction(function () use ($package, $import) {
                // Hapus semua soal lama di paket ini sebelum memasukkan yang baru
                $package->questions()->delete();

                foreach ($import->rows as $row) {
                    $package->questions()->create([

                        'number' => $row['number'],
                        'section' => strtolower($row['section']),
                        'part' => $row['part'],
                        'type' => $row['type'],
                        'passage_group' => $row['passage_group'] ?? null,
                        'passage_html' => $row['passage_html'] ?? null,
                        'content_html' => $row['content_html'],
                        'options' => is_array($row['options']) ? $row['options'] : json_decode($row['options'], true),
                        'answer_key' => $row['answer_key'],
                        'cue_start' => $row['cue_start'] ?? null,
                        'cue_end' => $row['cue_end'] ?? null,
                        'score_weight' => $row['score_weight'] ?? 1,
                    ]);
                }
            });

            return response()->json(['status' => 'success', 'message' => 'Berhasil mengimport '.count($import->rows).' soal.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Package $package, Question $question)
    {
        abort_if($question->package_id !== $package->id, 404);
        $question->delete();

        $package->updateQuietly([
            'total_questions' => $package->questions()->count(),
        ]);

        return back()->with('success', 'Soal dihapus');
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'number' => 'required|integer|min:1',
            'section' => 'required|in:listening,structure,reading',
            'part' => 'nullable|string|max:1',
            'type' => 'required|in:mc,error',
            'content_html' => 'required|string',
            'passage_html' => 'nullable|string',
            'passage_group' => 'nullable|string',
            'answer_key' => 'required|string|max:1',
            'cue_start' => 'nullable|integer',
            'cue_end' => 'nullable|integer',
            'score_weight' => 'nullable|integer|min:1',
        ]);
    }
}
