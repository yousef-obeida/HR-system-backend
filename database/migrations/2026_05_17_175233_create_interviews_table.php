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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id('interview_id');
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->dateTime('scheduled_at');
            $table->dateTime('interview_date')->nullable();
            $table->string('job_title');
            $table->foreignId('candidate_name')->constrained()->cascadeOnDelete();
            $table->text ('notes')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
