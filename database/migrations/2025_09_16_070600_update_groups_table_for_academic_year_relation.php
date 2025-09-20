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
        Schema::table('groups', function (Blueprint $table) {
            // Удаляем старое текстовое поле
            $table->dropColumn('academic_year');
            // Добавляем внешний ключ
            $table->foreignId('academic_year_id')
                  ->nullable()
                  ->constrained('academic_years')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // Удаляем внешний ключ
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn('academic_year_id');
            // Возвращаем текстовое поле
            $table->string('academic_year')->default('2024-2025');
        });
    }
};
