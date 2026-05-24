<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cv_analyses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('candidate_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->text('summary');

            $table->json('skills');

            $table->integer('experience_years');

            $table->integer('score');

            $table->string('recommendation');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_analyses');
    }
};
