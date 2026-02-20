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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_id')->constrained('churches')->cascadeOnDelete();
            $table->string('full_name');
            $table->string('primary_contact')->unique();
            $table->string('alternate_contact')->nullable()->unique();
            $table->enum('gender', ['Male', 'Female']);
            $table->date('date_of_birth')->nullable();
            $table->integer('age')->nullable();
            $table->text('residential_address');
            $table->string('occupation')->nullable();
            $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed']);
            $table->string('email')->nullable()->unique();
            $table->string('bringer_name')->nullable();
            $table->string('bringer_contact')->nullable();
            $table->string('bringer_fellowship')->nullable();
            $table->boolean('born_again')->default(false);
            $table->boolean('water_baptism')->default(false);
            $table->text('prayer_requests')->nullable();
            $table->date('date_of_visit');
            $table->string('church_event')->nullable();
            $table->string('status')->default('Retained');
            $table->timestamp('membership_requested_at')->nullable();
            $table->timestamp('membership_approved_at')->nullable();
            $table->foreignId('retaining_officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('church_id');
            $table->index('status');
            $table->index('retaining_officer_id');
            $table->index('date_of_visit');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
