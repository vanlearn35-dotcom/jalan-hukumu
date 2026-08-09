<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\ExamSession;
use App\Models\Package;
use App\Models\Question;
use Illuminate\Http\Request;

class ExamSessionController extends Controller
{
    /* =====================================================
     | CONFIG SECTION
     ===================================================== */
    private $sectionConfig = [
        'listening' => 0,      // audio driven
        'structure' => 1500,   // 25 menit
        'reading' => 3300,     // 55 menit
    ];

    private $sectionOrder = ['listening', 'structure', 'reading'];

    /* =====================================================
     | 1. DASHBOARD
     ===================================================== */
    public function index()
    {
        $packages = Package::where('status', 'published')
            ->withCount('questions')
            ->latest()
            ->get();

        $userSessions = ExamSession::where('user_id', auth()->id())
            ->get()
            ->keyBy('package_id');

        return view('exam.index', compact('packages', 'userSessions'));
    }

    /* =====================================================
     | 2. PREPARATION (TOKEN)
     ===================================================== */
    public function preparation(Package $package)
    {
        $ongoing = ExamSession::where('user_id', auth()->id())
            ->where('package_id', $package->id)
            ->where('status', 'ongoing')
            ->first();

        if ($ongoing) {
            return redirect()->route('exam.run', $ongoing->id);
        }

        $completed = ExamSession::where('user_id', auth()->id())
            ->where('package_id', $package->id)
            ->where('status', 'completed')
            ->first();

        if ($completed) {
            return redirect()->route('exam.result', $completed->id);
        }

        return view('exam.preparation', compact('package'));
    }

    public function allQuestions(ExamSession $session)
    {
        try {
            // Ambil semua soal dengan urutan yang benar
            $questions = Question::where('package_id', $session->package_id)
                ->orderByRaw("CASE 
                WHEN section = 'listening' THEN 1 
                WHEN section = 'structure' THEN 2 
                WHEN section = 'reading' THEN 3 
                ELSE 4 END")
                ->orderBy('number', 'asc')
                ->get();

            // Ambil semua instruksi untuk paket ini
            $instructions = \App\Models\Instruction::where('package_id', $session->package_id)->get();

            // Map soal agar membawa data instruksi yang relevan (jika nomor 1 di partnya)
            $data = $questions->map(function ($q) use ($instructions) {
                // Ambil instruksi hanya jika ini soal pertama di part/section tersebut
                $isFirst = ! Question::where('package_id', $q->package_id)
                    ->where('section', $q->section)
                    ->where('part', $q->part)
                    ->where('number', '<', $q->number)
                    ->exists();

                $instr = null;
                if ($isFirst) {
                    $instr = $instructions->where('section', $q->section)
                        ->where('part', $q->part)
                        ->first();
                }

                $q->instruction_data = $instr ? $instr->content_html : null;

                return $q;
            });

            return response()->json(['questions' => $data]);
        } catch (\Throwable $e) {
            // Ini akan membantu Anda melihat error asli di Tab Network -> Preview
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /* =====================================================
     | 3. START EXAM
     ===================================================== */
    public function start(Request $request, Package $package)
    {
        $inputToken = strtoupper(trim($request->token));
        $dbToken = strtoupper(trim($package->token_secret));

        if ($inputToken !== $dbToken) {
            return back()->with('error', 'Token Salah.');
        }

        $existing = ExamSession::where('user_id', auth()->id())
            ->where('package_id', $package->id)
            ->where('status', 'ongoing')
            ->first();

        if ($existing) {
            return redirect()->route('exam.run', $existing->id);
        }

        $firstSection = null;
        foreach ($this->sectionOrder as $section) {
            if ($package->questions()->where('section', $section)->exists()) {
                $firstSection = $section;
                break;
            }
        }

        if (! $firstSection) {
            return back()->with('error', 'Paket belum memiliki soal.');
        }

        $session = ExamSession::create([
            'user_id' => auth()->id(),
            'package_id' => $package->id,
            'status' => 'ongoing',
            'current_section' => $firstSection,
            'current_question_num' => 1,
            'remaining_time' => $this->sectionConfig[$firstSection],
            'started_at' => now(),
            'section_started_at' => now(),
        ]);

        return redirect()->route('exam.run', $session->id);
    }

    /* =====================================================
     | 4. RUN EXAM (VIEW ONLY)
     ===================================================== */
    public function run(ExamSession $session)
    {
        $user = auth()->user();

        if ($user->role !== 'admin' && $session->user_id !== $user->id) {
            abort(403);
        }

        if ($session->status === 'completed') {
            return redirect()->route('exam.result', $session->id);
        }

        return view('exam.run', compact('session'));
    }

    /* =====================================================
     | 5. FLOW DATA (CORE API)
     ===================================================== */
    public function flowData(ExamSession $session)
    {
        $questions = Question::where('package_id', $session->package_id)
            ->orderByRaw('COALESCE(cue_start, 999999)')
            ->orderBy('number')
            ->get();
        $package_name = Package::where('id',$session->package_id)
            ->get('name');
        return response()->json([
            'session' => [
                'id' => $session->id,
                'section' => $session->current_section,
                'time_left' => $session->remaining_time,
            ],
            'pacname' =>$package_name,
            'audio_url' => $session->package->audio_path
                ? asset('storage/'.$session->package->audio_path)
                : null,
            'items' => $questions->map(fn ($q) => [
                'id' => $q->id,
                'section' => $q->section,
                'type' => $q->type,                 // instruction | question
                'number' => $q->number,
                'cue_start' => $q->cue_start,        // null = instruction cue
                'content_html' => $q->content_html,
                'options' => is_array($q->options)
                    ? $q->options
                    : json_decode($q->options, true),
                'passage_group' => $q->passage_group,
            ]),
        ]);
    }

    /* =====================================================
     | 6. SAVE ANSWER
     ===================================================== */
    public function saveAnswer(Request $request, ExamSession $session)
    {
        // session_id sudah didapat otomatis dari parameter {session} di URL
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected' => 'nullable',
        ]);

        $question = Question::findOrFail($request->question_id);

        $isCorrect = strtoupper(trim($request->selected)) ===
                     strtoupper(trim($question->answer_key));

        Answer::updateOrCreate(
            [
                'exam_session_id' => $session->id, // Menggunakan ID dari route
                'question_id' => $request->question_id,
            ],
            [
                'selected' => $request->selected,
                'is_correct' => $isCorrect,
                'answered_at' => now(),
            ]
        );

        return response()->json(['status' => 'saved']);
    }

    /* =====================================================
     | 7. HEARTBEAT
     ===================================================== */
    public function heartbeat(Request $request)
    {
        $session = ExamSession::find($request->session_id);

        if ($session && $session->status === 'ongoing') {
            $session->update([
                'remaining_time' => $request->remaining_time,
                'last_activity' => now(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    /* =====================================================
     | 8. NEXT SECTION
     ===================================================== */
    public function nextSection(ExamSession $session)
    {
        return $this->processNextSection($session);
    }

    private function processNextSection(ExamSession $session)
    {
        $currentIndex = array_search($session->current_section, $this->sectionOrder);

        for ($i = $currentIndex + 1; $i < count($this->sectionOrder); $i++) {
            $section = $this->sectionOrder[$i];

            if (Question::where('package_id', $session->package_id)
                ->where('section', $section)
                ->exists()) {

                $session->update([
                    'current_section' => $section,
                    'remaining_time' => $this->sectionConfig[$section],
                    'section_started_at' => now(),
                ]);

                return response()->json(['status' => 'next', 'section' => $section]);
            }
        }

        $session->update([
            'status' => 'completed',
            'end_time' => now(),
            'remaining_time' => 0,
        ]);

        return response()->json(['status' => 'completed']);
    }

    /* =====================================================
     | 9. RESULT
     ===================================================== */
    public function result(ExamSession $session)
    {
        if (auth()->user()->role !== 'admin' &&
            $session->user_id !== auth()->id()) {
            abort(403);
        }

        $total = Question::where('package_id', $session->package_id)->count();
        $correct = Answer::where('exam_session_id', $session->id)
            ->where('is_correct', 1)
            ->count();

        $score = $total ? round(($correct / $total) * 100) : 0;

        return view('exam.result', compact('session', 'score'));
    }
}
