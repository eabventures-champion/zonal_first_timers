<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\User;

echo "USERS:\n";
foreach (User::all() as $user) {
    $role = $user->roles->first()?->name ?? 'No Role';
    $church = $user->church;
    $group = $church ? $church->group : null;
    $category = $group ? $group->category : null;

    printf(
        "ID: %d | Name: %-20s | Role: %-15s | Church: %-15s | Group: %-15s | Cat: %s\n",
        $user->id,
        $user->name,
        $role,
        $church ? $church->name : 'NULL',
        $group ? $group->name : 'NULL',
        $category ? $category->name : 'NULL'
    );
}
