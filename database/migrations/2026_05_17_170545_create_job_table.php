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
        Schema::create('job', function (Blueprint $table) {
            $table->id('job_id')->autoIncrement();
            $table->string('title');
            $table->text('description');
            $table->text('requirments');
            $table->enum('status', ['open', 'closed']);
            $table->enum('Location', ['onsite','remote','hybrid']);
            $table->integer('salary')->nullable();
            $table->timestamps();
        });
    }
   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job');
    }

};
