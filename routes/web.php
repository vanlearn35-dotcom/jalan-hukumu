<?php

use App\Http\Controllers\admin\ExamPreviewController;
use App\Http\Controllers\AudioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamSessionController;
use App\Http\Controllers\InstructionController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ROOT & AUTH (GUEST) - (TIDAK BERUBAH)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->guest('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USERS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'approved'])->group(function () {

    // LOGOUT & DASHBOARD (TIDAK BERUBAH)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | [MODUL EXAM BARU] - SISWA & ENGINE
    |--------------------------------------------------------------------------
    | Ini adalah bagian yang diperbaiki agar alur Preparation -> Token -> Start jalan.
    */
    Route::prefix('exam')->name('exam.')->group(function () {

        // Dashboard Siswa (List Ujian)
        Route::get('/', [ExamSessionController::class, 'index'])->name('index');

        // 1. Preparation (Input Token) - BARU
        Route::get('/{package}/preparation', [ExamSessionController::class, 'preparation'])->name('preparation');

        // 2. Start (Validasi & Buat Sesi)
        Route::post('/{package}/start', [ExamSessionController::class, 'start'])->name('start');

        Route::get('/{package}', [ExamSessionController::class, 'start']);

        Route::get('/api/{session}/questions', [ExamSessionController::class, 'allQuestions']);

        Route::post('/api/{session}/answer', [ExamSessionController::class, 'saveAnswer']);

        Route::post('/api/{session}/finish', [ExamSessionController::class, 'finish']);

        // 4. Result
        Route::get('/result/{session}', [ExamSessionController::class, 'result'])->name('result');

        Route::get('/run/{session}', [ExamSessionController::class, 'run'])->name('run');

        Route::get('/api/{session}/flow', [ExamSessionController::class, 'flowData'])->name('exam.api.flow');

        Route::post('/{session}/answer', [ExamSessionController::class, 'saveAnswer']);

        Route::post('/{session}/finish', [ExamSessionController::class, 'finish']);

        Route::get('{session}/questions', [ExamSessionController::class, 'allQuestions']);
        Route::post('{session}/answer', [ExamSessionController::class, 'saveAnswer']);
        Route::post('{session}/finish', [ExamSessionController::class, 'finish']);
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN AREA - (MANAGEMENT FITUR LAMA DIPERTAHANKAN)
    |--------------------------------------------------------------------------
    | Bagian ini saya biarkan SAMA PERSIS dengan kode lama Anda agar
    | Users, Packages, Questions, Audios, Instructions TIDAK ERROR.
    */
    Route::prefix('admin')->middleware('role:admin')->group(function () {

        // USERS (LAMA)
        Route::get('/users', [UserController::class, 'index'])->name('admin.users');
        Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/users/{user}/approve', [UserController::class, 'approve'])->name('admin.users.approve');
        Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('admin.users.deactivate');
        Route::post('/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.role');

        // PACKAGES (LAMA - Mempertahankan method 'adminIndex')
        Route::get('/packages', [PackageController::class, 'adminIndex'])->name('admin.packages');
        Route::get('/packages/create', [PackageController::class, 'create'])->name('admin.packages.create');
        Route::post('/packages', [PackageController::class, 'store'])->name('admin.packages.store');
        Route::get('/packages/{package}/edit', [PackageController::class, 'edit'])->name('admin.packages.edit');
        Route::put('/packages/{package}', [PackageController::class, 'update'])->name('admin.packages.update');
        Route::post('/packages/{package}/publish', [PackageController::class, 'publish'])->name('admin.packages.publish');
        Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('admin.packages.destroy');

        Route::post('/package/{id}/token', [App\Http\Controllers\PackageController::class, 'refreshToken'])
            ->name('admin.package.token');

        // FIX COUNTS UTILITY
        Route::get('/fix-counts', function () {
            $packages = \App\Models\Package::all();
            foreach ($packages as $pkg) {
                $pkg->updateQuietly(['total_questions' => $pkg->questions()->count()]);
            }

            return 'Berhasil sinkronisasi jumlah soal.';
        });

        // LIST UJIAN (MONITORING)
        Route::get('/exam', [ExamPreviewController::class, 'index'])->name('admin.exam');

        // QUESTIONS (LAMA)
        Route::prefix('packages/{package}/questions')->group(function () {
            Route::get('/', [QuestionController::class, 'index'])->name('admin.questions.index');
            Route::get('/create', [QuestionController::class, 'create'])->name('admin.questions.create');
            Route::post('/', [QuestionController::class, 'store'])->name('admin.questions.store');
            Route::get('/{question}/edit', [QuestionController::class, 'edit'])->name('admin.questions.edit');
            Route::put('/{question}', [QuestionController::class, 'update'])->name('admin.questions.update');
            Route::delete('/{question}', [QuestionController::class, 'destroy'])->name('admin.questions.destroy');
            Route::post('/import', [QuestionController::class, 'import'])->name('admin.questions.import');
            Route::get('{question}/ajax', [QuestionController::class, 'getAjaxData'])->name('admin.questions.ajax');
        });

        // AUDIOS (LAMA)
        Route::prefix('packages/{package}/audios')->group(function () {
            Route::get('/', [AudioController::class, 'index'])->name('admin.audios.index');
            Route::post('/store', [AudioController::class, 'store'])->name('admin.audios.store');
            Route::post('/{audio}/select', [AudioController::class, 'select'])->name('admin.audios.select');
            Route::delete('/{audio}', [AudioController::class, 'destroy'])->name('admin.audios.destroy');
        });

        // INSTRUCTIONS (LAMA)
        Route::prefix('packages/{package}/instruction')->group(function () {
            Route::controller(InstructionController::class)->group(function () {
                Route::get('/', 'index')->name('admin.instructions.index');
                Route::post('/store', 'store')->name('admin.instructions.store');
                Route::post('/import', 'import')->name('admin.instructions.import');
                Route::put('/update', 'update')->name('admin.instructions.update');
                Route::delete('/{instruction}', 'destroy')->name('admin.instructions.destroy');
            });
        });

        /*
        |--------------------------------------------------------------------------
        | ADMIN EXAM PREVIEW & MONITORING - (YANG KITA PERBAIKI)
        |--------------------------------------------------------------------------
        */
        Route::prefix('/exams')->group(function () {
            // List Ujian (Sama dengan admin.exam diatas, tapi beda alias)
            Route::get('/', [ExamPreviewController::class, 'index'])->name('admin.exams.index');

            // [FITUR BARU] Preview Mode: Masuk sebagai Admin (Tanpa Token, Full Control)
            // Mengarah ke ExamSessionController@preview yang sudah kita edit
            Route::get('/{package}/preview', [ExamSessionController::class, 'preview'])->name('admin.exam.preview');

            // Monitoring Live
            Route::get('/{package}/monitor', [ExamPreviewController::class, 'monitor'])->name('admin.exams.monitor');
            Route::get('/{package}/live-data', [ExamPreviewController::class, 'getLiveData']);
        });

    }); // End Admin Group
});
