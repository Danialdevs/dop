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
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropForeign(['schedule_id']);
            $table->dropColumn('schedule_id');
            
            $table->tinyInteger('day_of_week')->after('lesson_date'); // 1-7 (понедельник-воскресенье)
            $table->time('start_time')->after('day_of_week');
            $table->time('end_time')->after('start_time');
            $table->string('subject')->after('end_time');
            $table->string('classroom')->nullable()->after('subject');
            $table->integer('week_number')->default(1)->after('classroom'); // номер недели
            
            $table->index(['group_id', 'day_of_week', 'week_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex(['group_id', 'day_of_week', 'week_number']);
            
            $table->dropColumn([
                'day_of_week',
                'start_time', 
                'end_time',
                'subject',
                'classroom',
                'week_number'
            ]);
            
            $table->foreignId('schedule_id')->after('group_id')->constrained()->onDelete('cascade');
        });
    }
};
