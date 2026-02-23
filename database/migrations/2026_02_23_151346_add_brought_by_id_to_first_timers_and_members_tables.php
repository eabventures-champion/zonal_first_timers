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
            $table->foreignId('brought_by_id')->nullable()->after('retaining_officer_id')->constrained('brought_bies')->onDelete('set null');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('brought_by_id')->nullable()->after('retaining_officer_id')->constrained('brought_bies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('first_timers', function (Blueprint $table) {
            $table->dropForeign(['brought_by_id']);
            $table->dropColumn('brought_by_id');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['brought_by_id']);
            $table->dropColumn('brought_by_id');
        });
    }
};
