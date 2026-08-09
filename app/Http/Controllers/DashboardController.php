<?php

namespace App\Http\Controllers;

use App\Models\ExamSession;
use App\Models\Package;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller // <--- pastikan extend Controller
{
    // Middleware agar hanya user terautentikasi
    public function __construct()
    {
        $this->middleware('auth'); // <-- ini hanya valid karena extend Controller
    }

    public function index()
    {
        $user = Auth::user();

        // ADMIN
        if ($user->role === 'admin') {
            return view('admin.index', [
                'total_users' => User::count(),
                'pending_users' => User::where('is_active', false)->count(),
                'total_packages' => Package::count(),
                'total_sessions' => ExamSession::count(),
            ]);
        }

        // PESERTA
        if ($user->role === 'participant') {
            return view('participant.index', [
                'packages' => Package::where('status', 'published')->get(),
                'active_session' => ExamSession::where('user_id', $user->id)
                    ->where('status', 'in_progress')->first(),
                'last_score' => Score::whereHas(
                    'examSession',
                    fn ($q) => $q->where('user_id', $user->id)
                )->latest()->first(),
            ]);
        }
    }

    public function history()
    {
        $user = Auth::user();

        $data = [
            'total_packages' => Package::count(),
            'total_sessions' => ExamSession::where('user_id', $user->id)->count(),
            'completed_sessions' => ExamSession::where('user_id', $user->id)
                ->where('status', 'completed')->count(),
            'last_score' => Score::whereHas('examSession', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->latest()->first(),
        ];

        // 🔑 Role-based view
        if ($user->role === 'admin') {
            return view('admin.index', $data);
        } elseif ($user->role === 'participant') {
            return view('participant.history', $data);
        }
    }
}
