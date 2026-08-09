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
        

        Schema::table('answers', function (Blueprint $table) {
            // Fitur Review/Ragu-ragu
            $table->boolean('is_doubtful')->default(false)->after('is_correct');
        });

        Schema::table('packages', function (Blueprint $table) {
            // Token Ujian (Opsional)
            $table->string('token', 10)->nullable()->after('description');
        });
    }

    public function down()
    {
        
        Schema::table('answers', function (Blueprint $table) {
            $table->dropColumn('is_doubtful');
        });
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
};
