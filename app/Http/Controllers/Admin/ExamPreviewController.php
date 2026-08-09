<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use App\Models\Package;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExamPreviewController extends Controller
{
    // Halaman List Paket (Pilih paket mana yang mau dimonitor)
    public function index()
    {
        $packages = Package::withCount(['questions', 'examSessions' => function($q){
            $q->where('status', 'ongoing');
        }])->latest()->get();

        return view('admin.exams.index', compact('packages'));
    }

    // Halaman Detail Monitoring
    public function monitor(Package $package)
    {
        return view('admin.exams.monitor', compact('package'));
    }

    // API JSON: Live Data untuk AJAX Polling
    public function getLiveData(Package $package)
    {
        // Ambil sesi yang sedang ongoing atau baru selesai 5 menit terakhir
        $sessions = ExamSession::with('user')
            ->where('package_id', $package->id)
            ->where(function($q) {
                $q->where('status', 'ongoing')
                  ->orWhere('updated_at', '>=', Carbon::now()->subMinutes(5));
            })
            ->orderBy('last_activity', 'desc')
            ->get();

        $data = $sessions->map(function($s) use ($package) {
            // Logika Status Online/Offline
            $lastSeen = Carbon::parse($s->last_activity);
            $isOnline = $lastSeen->diffInSeconds(now()) < 30; // Jika heartbeat < 30 detik lalu = Online

            // Hitung Progress
            $totalQ = $package->total_questions > 0 ? $package->total_questions : 1;
            // Ini perkiraan kasar progress berdasarkan soal terakhir yg dibuka
            // (Untuk akurasi tinggi bisa query count ke tabel answers, tapi berat)
            $progress = 0; // Anda bisa sesuaikan logic ini
            
            return [
                'user_name' => $s->user->name,
                'user_email' => $s->user->email,
                'section' => ucfirst($s->current_section),
                'current_q' => $s->current_question_num,
                'remaining_time' => gmdate("i:s", $s->remaining_time),
                'status' => $s->status,
                'is_online' => $isOnline,
                'last_seen' => $lastSeen->diffForHumans()
            ];
        });

        return response()->json(['data' => $data]);
    }
}