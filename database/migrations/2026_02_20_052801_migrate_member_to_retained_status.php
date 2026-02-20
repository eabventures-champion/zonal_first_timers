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
        // First, change the enum to include 'Retained'
        Schema::table('first_timers', function (Blueprint $table) {
            $table->string('status')->change(); // Temporarily change to string to allow any value
        });

        \Illuminate\Support\Facades\DB::table('first_timers')
            ->where('status', 'Member')
            ->update(['status' => 'Retained']);

        // Then, change it back to enum including 'Retained' but removing 'Member'
        Schema::table('first_timers', function (Blueprint $table) {
            $table->enum('status', ['New', 'In Progress', 'Retained'])->default('New')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('first_timers', function (Blueprint $table) {
            $table->string('status')->change();
        });

        \Illuminate\Support\Facades\DB::table('first_timers')
            ->where('status', 'Retained')
            ->update(['status' => 'Member']);

        Schema::table('first_timers', function (Blueprint $table) {
            $table->enum('status', ['New', 'In Progress', 'Member'])->default('New')->change();
        });
    }
};
