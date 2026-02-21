<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage-church-categories',
            'manage-church-groups',
            'manage-churches',
            'manage-users',
            'view-all-churches',
            'manage-first-timers',
            'view-first-timers',
            'manage-foundation-school',
            'view-dashboards',
            'delete-system-data',
            'import-first-timers',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo([
            'manage-churches',
            'view-all-churches',
            'manage-first-timers',
            'view-first-timers',
            'manage-foundation-school',
            'view-dashboards',
            'import-first-timers',
        ]);

        $retainingOfficer = Role::firstOrCreate(['name' => 'Retaining Officer']);
        $retainingOfficer->givePermissionTo([
            'manage-first-timers',
            'view-first-timers',
            'manage-foundation-school',
            'view-dashboards',
        ]);

        $member = Role::firstOrCreate(['name' => 'Member']);
        $member->givePermissionTo([
            'view-dashboards',
        ]);
    }
}
