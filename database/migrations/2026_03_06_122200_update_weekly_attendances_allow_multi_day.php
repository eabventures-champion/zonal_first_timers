<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Force-delete any soft-deleted records to prevent unique constraint conflicts
        DB::table('weekly_attendances')->whereNotNull('deleted_at')->delete();

        // Temporarily disable foreign key checks so we can drop the unique indexes
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $indexes = collect(DB::select("SHOW INDEX FROM weekly_attendances"))->pluck('Key_name')->unique();

        Schema::table('weekly_attendances', function (Blueprint $table) use ($indexes) {
            // Drop old indexes safely
            if ($indexes->contains('wa_ft_month_year_week_unique')) {
                $table->dropUnique('wa_ft_month_year_week_unique');
            }
            if ($indexes->contains('wa_m_month_year_week_unique')) {
                // To drop this unique index, we need another index on member_id to support the FK
                $table->index('member_id', 'wa_m_tmp_index');
                $table->dropUnique('wa_m_month_year_week_unique');
            }

            // Create new unique constraints that include service_date
            if (!$indexes->contains('wa_ft_month_year_week_date_unique')) {
                $table->unique(['first_timer_id', 'month', 'year', 'week_number', 'service_date'], 'wa_ft_month_year_week_date_unique');
            }
            if (!$indexes->contains('wa_m_month_year_week_date_unique')) {
                $table->unique(['member_id', 'month', 'year', 'week_number', 'service_date'], 'wa_m_month_year_week_date_unique');
            }

            // Re-drop the temporary index if the new unique one satisfies the FK
            // (InnoDB allows any index that has the column as the first part)
        });

        Schema::table('weekly_attendances', function (Blueprint $table) {
            // This second step ensures wa_m_tmp_index can be dropped if the new unique one exists
            $table->dropIndex('wa_m_tmp_index');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $indexes = collect(DB::select("SHOW INDEX FROM weekly_attendances"))->pluck('Key_name')->unique();

        Schema::table('weekly_attendances', function (Blueprint $table) use ($indexes) {
            if ($indexes->contains('wa_ft_month_year_week_date_unique')) {
                $table->dropUnique('wa_ft_month_year_week_date_unique');
            }
            if ($indexes->contains('wa_m_month_year_week_date_unique')) {
                $table->dropUnique('wa_m_month_year_week_date_unique');
            }

            $table->unique(['first_timer_id', 'month', 'year', 'week_number'], 'wa_ft_month_year_week_unique');
            $table->unique(['member_id', 'month', 'year', 'week_number'], 'wa_m_month_year_week_unique');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
