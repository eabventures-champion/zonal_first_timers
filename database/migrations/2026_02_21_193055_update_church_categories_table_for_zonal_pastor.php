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
        Schema::table('church_categories', function (Blueprint $table) {
            $table->renameColumn('description', 'zonal_pastor_name');
            $table->string('zonal_pastor_contact')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('church_categories', function (Blueprint $table) {
            $table->renameColumn('zonal_pastor_name', 'description');
            $table->dropColumn('zonal_pastor_contact');
        });
    }
};
