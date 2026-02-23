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
        Schema::table('bringers', function (Blueprint $table) {
            $table->dropColumn('fellowship');
        });

        Schema::table('first_timers', function (Blueprint $table) {
            $table->dropColumn('bringer_fellowship');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('bringer_fellowship');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('bringer_fellowship')->nullable()->after('bringer_contact');
        });

        Schema::table('first_timers', function (Blueprint $table) {
            $table->string('bringer_fellowship')->nullable()->after('bringer_contact');
        });

        Schema::table('bringers', function (Blueprint $table) {
            $table->string('fellowship')->nullable()->after('contact');
        });
    }
};
