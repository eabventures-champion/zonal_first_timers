<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('weekly_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('first_timer_id')->constrained('first_timers')->cascadeOnDelete();
            $table->foreignId('church_id')->constrained('churches')->cascadeOnDelete();
            $table->integer('week_number');
            $table->date('service_date');
            $table->boolean('attended')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['first_timer_id', 'week_number']);
            $table->index('church_id');
            $table->index('first_timer_id');
            $table->index('service_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_attendances');
    }
};
