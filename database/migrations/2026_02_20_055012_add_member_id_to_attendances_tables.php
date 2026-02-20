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
        Schema::table('weekly_attendances', function (Blueprint $table) {
            $table->foreignId('member_id')->nullable()->after('first_timer_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('first_timer_id')->nullable()->change();
        });

        Schema::table('foundation_attendances', function (Blueprint $table) {
            $table->foreignId('member_id')->nullable()->after('first_timer_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('first_timer_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('weekly_attendances', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropColumn('member_id');
            $table->foreignId('first_timer_id')->nullable(false)->change();
        });

        Schema::table('foundation_attendances', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropColumn('member_id');
            $table->foreignId('first_timer_id')->nullable(false)->change();
        });
    }
};
