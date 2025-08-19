<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('work_date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->integer('break_time')->default(0)->comment('休憩時間（分）');
            $table->text('note')->nullable()->comment('備考');
            $table->boolean('break_time_flag')->default(false)->comment('休憩中フラグ');
            $table->time('break_start_time')->nullable()->comment('休憩開始時刻');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
