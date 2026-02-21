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
            $table->foreignId('user_id')->nullable()->after('retaining_officer_id')->constrained('users')->nullOnDelete();
        });

        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('retaining_officer_id')->constrained('users')->nullOnDelete();
            $table->timestamp('migrated_at')->nullable()->after('membership_approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('first_timers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'migrated_at']);
        });
    }
};
