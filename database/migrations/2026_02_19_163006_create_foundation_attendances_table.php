<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('foundation_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('first_timer_id')->constrained('first_timers')->cascadeOnDelete();
            $table->foreignId('foundation_class_id')->constrained('foundation_classes')->cascadeOnDelete();
            $table->boolean('attended')->default(false);
            $table->boolean('completed')->default(false);
            $table->date('attendance_date')->nullable();
            $table->timestamps();

            $table->unique(['first_timer_id', 'foundation_class_id']);
            $table->index('first_timer_id');
            $table->index('foundation_class_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foundation_attendances');
    }
};
