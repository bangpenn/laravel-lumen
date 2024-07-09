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
        Schema::create('nilai_akademik_new', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->uuid('student_key');
            $table->uuid('course_key');
            $table->string('semester');
            $table->uuid('key_bobot');
            $table->float('nilai_bobot');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_akademik_new');
    }
};
