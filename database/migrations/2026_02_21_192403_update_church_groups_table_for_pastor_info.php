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
        Schema::table('church_groups', function (Blueprint $table) {
            $table->renameColumn('description', 'pastor_name');
            $table->string('pastor_contact')->nullable()->after('name'); // Using 'name' as anchor
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('church_groups', function (Blueprint $table) {
            $table->renameColumn('pastor_name', 'description');
            $table->dropColumn('pastor_contact');
        });
    }
};
