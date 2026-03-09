<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::withTrashed()->find(1);
if ($user) {
    if ($user->trashed()) {
        $user->restore();
        echo "User restored successfully.\n";
    } else {
        echo "User is not trashed.\n";
    }
} else {
    echo "User 1 not found entirely.\n";
}
