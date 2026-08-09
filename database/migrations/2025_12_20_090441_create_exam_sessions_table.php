<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();

            // exam | preview
            $table->enum('mode', ['exam', 'preview'])->default('exam');

            // lifecycle
            $table->enum('status', ['ongoing', 'paused', 'completed'])
                ->default('ongoing');

            // engine
            $table->string('current_section')->nullable();
            $table->json('section_state')->nullable();

            // timing
            $table->timestamp('started_at')->nullable();
            $table->timestamp('section_started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            // legacy / fallback (tidak dipakai engine)
            $table->integer('remaining_time')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};
