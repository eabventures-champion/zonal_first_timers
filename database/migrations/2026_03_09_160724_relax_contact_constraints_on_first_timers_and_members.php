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
            $table->string('primary_contact')->nullable()->change();
            $table->dropUnique(['alternate_contact']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->string('primary_contact')->nullable()->change();
            $table->dropUnique(['alternate_contact']);
        });
    }

    public function down(): void
    {
        Schema::table('first_timers', function (Blueprint $table) {
            $table->string('primary_contact')->nullable(false)->change();
            $table->unique('alternate_contact');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->string('primary_contact')->nullable(false)->change();
            $table->unique('alternate_contact');
        });
    }
};
