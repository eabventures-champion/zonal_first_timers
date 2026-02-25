<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            FoundationClassSeeder::class,
            DatabaseCleanupSeeder::class,
            HomepageSettingsSeeder::class,
            SyncBringersToUsersSeeder::class,
            ChurchHierarchySeeder::class,
            // DemoDataSeeder::class,
        ]);
    }
}
