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
            $table->dropUnique(['first_timer_id', 'week_number']);
            $table->integer('month')->after('week_number')->nullable();
            $table->integer('year')->after('month')->nullable();

            // New unique constraint allowing weekly recording per month
            $table->unique(['first_timer_id', 'month', 'year', 'week_number'], 'wa_ft_month_year_week_unique');
            $table->unique(['member_id', 'month', 'year', 'week_number'], 'wa_m_month_year_week_unique');
        });

        // Migrate existing data
        DB::table('weekly_attendances')->get()->each(function ($wa) {
            $month = date('n', strtotime($wa->service_date));
            $year = date('Y', strtotime($wa->service_date));
            DB::table('weekly_attendances')->where('id', $wa->id)->update([
                'month' => $month,
                'year' => $year
            ]);
        });

        Schema::table('weekly_attendances', function (Blueprint $table) {
            $table->integer('month')->nullable(false)->change();
            $table->integer('year')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_attendances', function (Blueprint $table) {
            $table->dropUnique('wa_ft_month_year_week_unique');
            $table->dropUnique('wa_m_month_year_week_unique');
            $table->dropColumn(['month', 'year']);
            $table->unique(['first_timer_id', 'week_number']);
        });
    }
};
