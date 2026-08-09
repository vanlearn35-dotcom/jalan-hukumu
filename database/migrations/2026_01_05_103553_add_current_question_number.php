<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->timestamp('last_activity')->nullable(); // Deteksi Online/Offline
            $table->integer('current_question_num')->nullable(); // Posisi soal
            // Pastikan kolom ini ada (biasanya sudah ada dari setup awal Anda)
            // $table->string('current_section')->nullable();
        });
    }

    public function down()
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropColumn(['last_activity', 'current_question_num']);
        });
    }
};
