<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class DatabaseCleanupSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key constraints
        Schema::disableForeignKeyConstraints();

        // Tables to truncate
        $tables = [
            'weekly_attendances',
            'foundation_attendances',
            'members',
            'first_timers',
            'churches',
            'church_groups',
            'church_categories',
            'foundation_classes',
            'settings',
            'sessions',
            'cache',
            'cache_locks',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->command->info("Truncated table: {$table}");
            }
        }

        // Keep only Super Admin users
        $superAdminIds = User::role('Super Admin')->pluck('id')->toArray();

        if (empty($superAdminIds)) {
            $this->command->error("No Super Admin found! Aborting user cleanup to prevent lockout.");
        } else {
            // Delete users who are NOT super admins
            User::whereNotIn('id', $superAdminIds)->delete();

            // Clean up model_has_roles for deleted users
            DB::table('model_has_roles')
                ->where('model_type', User::class)
                ->whereNotIn('model_id', $superAdminIds)
                ->delete();

            $this->command->info("Deleted all users except Super Admins.");
        }

        // Re-enable foreign key constraints
        Schema::enableForeignKeyConstraints();

        $this->command->info("Database cleanup completed successfully!");
    }
}
