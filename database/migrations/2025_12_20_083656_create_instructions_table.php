<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('instructions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->enum('section', ['listening', 'structure', 'reading']);
            $table->string('part')->nullable();
            $table->longText('content_html');
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->unique(['package_id', 'section', 'part'], 'idx_pkg_instr');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructions');
    }
};
