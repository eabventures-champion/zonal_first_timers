<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$superAdmins = \App\Models\User::role('Super Admin')->get();
echo "Super Admins:\n";
foreach ($superAdmins as $admin) {
    echo "ID: {$admin->id}, Name: {$admin->name}, Email: {$admin->email}, Phone: {$admin->phone}\n";
}

$firstUser = \App\Models\User::find(1);
echo "\nUser 1:\n";
if ($firstUser) {
    echo "ID: {$firstUser->id}, Name: {$firstUser->name}, Email: {$firstUser->email}, Phone: {$firstUser->phone}\n";
    echo "Roles: " . implode(', ', $firstUser->getRoleNames()->toArray()) . "\n";
}

$count = \App\Models\User::count();
echo "\nTotal users: {$count}\n";
