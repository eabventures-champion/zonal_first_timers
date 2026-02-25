<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Spatie\Permission\Models\Role;

$role = Role::findByName('Retaining Officer');
echo "Permissions for Retaining Officer:\n";
print_r($role->permissions->pluck('name')->toArray());
