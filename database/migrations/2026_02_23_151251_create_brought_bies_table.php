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
        Schema::create('brought_bies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('contact');
            $table->string('fellowship')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Links to a User record if they are an RO or Member
            $table->boolean('is_ro')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brought_bies');
    }
};
