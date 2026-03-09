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
        Schema::table('first_timers', function (Blueprint $table) {
            $table->enum('gender', ['Male', 'Female'])->nullable()->change();
            $table->boolean('born_again')->nullable()->default(null)->change();
            $table->boolean('water_baptism')->nullable()->default(null)->change();
        });

        Schema::table('members', function (Blueprint $table) {
            $table->enum('gender', ['Male', 'Female'])->nullable()->change();
            $table->boolean('born_again')->nullable()->default(null)->change();
            $table->boolean('water_baptism')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('first_timers', function (Blueprint $table) {
            $table->enum('gender', ['Male', 'Female'])->nullable(false)->change();
            $table->boolean('born_again')->default(false)->change();
            $table->boolean('water_baptism')->default(false)->change();
        });

        Schema::table('members', function (Blueprint $table) {
            $table->enum('gender', ['Male', 'Female'])->nullable(false)->change();
            $table->boolean('born_again')->default(false)->change();
            $table->boolean('water_baptism')->default(false)->change();
        });
    }
};
