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
        Schema::rename('brought_bies', 'bringers');

        Schema::table('first_timers', function (Blueprint $table) {
            $table->renameColumn('brought_by_id', 'bringer_id');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->renameColumn('brought_by_id', 'bringer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->renameColumn('bringer_id', 'brought_by_id');
        });

        Schema::table('first_timers', function (Blueprint $table) {
            $table->renameColumn('bringer_id', 'brought_by_id');
        });

        Schema::rename('bringers', 'brought_bies');
    }
};
