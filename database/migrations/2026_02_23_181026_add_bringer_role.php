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
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view-dashboards']);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Bringer']);
        $role->givePermissionTo('view-dashboards');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $role = \Spatie\Permission\Models\Role::where('name', 'Bringer')->first();
        if ($role) {
            $role->delete();
        }
    }
};
