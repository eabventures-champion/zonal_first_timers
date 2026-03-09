<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$u = \App\Models\User::withTrashed()->where('email', 'superadmin@church.com')->first();
if ($u) {
    echo "Found user: ID={$u->id}, Name={$u->name}, Email={$u->email}, Phone={$u->phone}\n";
    echo "Deleted At: " . ($u->deleted_at ?? 'NULL') . "\n";
    echo "Password starts with: " . substr($u->password, 0, 10) . "...\n";
    echo "Roles: " . implode(', ', $u->getRoleNames()->toArray()) . "\n";
} else {
    echo "User superadmin@church.com not found at all!\n";
}
