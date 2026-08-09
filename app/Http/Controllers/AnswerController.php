<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\ExamSession;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnswerController extends Controller
{
    /**
     * Simpan jawaban sementara / auto-save
     */
    public function save(Request $request, $sessionId)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required|string'
        ]);

        $session = ExamSession::findOrFail($sessionId);

        $answer = Answer::updateOrCreate(
            [
                'exam_session_id' => $session->id,
                'question_id' => $request->question_id
            ],
            ['answer' => $request->answer]
        );

        return response()->json(['success' => true, 'message' => 'Answer saved.', 'answer' => $answer]);
    }

    /**
     * Submit jawaban akhir
     */
    public function submit(Request $request, $sessionId)
    {
        $session = ExamSession::with('answers.question')->findOrFail($sessionId);

        $answers = $session->answers;

        $score = 0;
        $total = $answers->count();

        foreach ($answers as $ans) {
            if ($ans->answer === $ans->question->correct_answer) {
                $score++;
            }
        }

        // Simpan score
        $session->score()->updateOrCreate([], [
            'total_score' => $score,
            'total_questions' => $total,
        ]);

        $session->status = 'completed';
        $session->finished_at = now();
        $session->save();

        return response()->json([
            'success' => true,
            'message' => 'Exam submitted successfully.',
            'score' => $score,
            'total_questions' => $total
        ]);
    }

    /**
     * Ambil jawaban peserta (bisa untuk review)
     */
    public function review($sessionId)
    {
        $session = ExamSession::with('answers.question', 'score')->findOrFail($sessionId);

        return view('answers.review', compact('session'));
    }
}
