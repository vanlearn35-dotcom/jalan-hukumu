<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->onDelete('cascade');

            // Kolom Inti
            $table->integer('number'); // Nomor soal (1, 2, dst)
            $table->string('section'); // listening, structure, reading
            $table->string('part')->nullable(); // A, B, C
            $table->string('type')->default('mc'); // mc atau error

            // Kolom Konten
            $table->string('passage_group')->nullable();
            $table->text('passage_html')->nullable();
            $table->text('content_html')->nullable();

            // Kolom Jawaban & Teknis
            $table->json('options');
            $table->string('answer_key', 5);
            $table->integer('score_weight')->default(1);

            // Kolom Audio (Listening)
            $table->integer('cue_start')->nullable();
            $table->integer('cue_end')->nullable();

            $table->timestamps();

            // PENTING: Index unik gabungan agar No 1 bisa ada di setiap section
            $table->unique(['package_id', 'section', 'number'], 'unique_question_per_section');
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
};
