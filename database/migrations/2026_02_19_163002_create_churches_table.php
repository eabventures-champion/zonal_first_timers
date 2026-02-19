<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('churches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_group_id')->constrained('church_groups')->cascadeOnDelete();
            $table->string('name');
            $table->text('address')->nullable();
            $table->foreignId('retaining_officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('church_group_id');
            $table->index('retaining_officer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('churches');
    }
};
