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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->date('lesson_date');
            $table->string('topic')->nullable(); // тема урока из КТП
            $table->text('objectives')->nullable(); // цели урока
            $table->text('materials')->nullable(); // материалы
            $table->text('homework')->nullable(); // домашнее задание
            $table->text('notes')->nullable(); // заметки
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
            
            $table->index(['schedule_id', 'lesson_date']);
            $table->index(['group_id', 'lesson_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
