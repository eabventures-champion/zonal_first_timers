<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$users = User::role('Retaining Officer')->get();
echo "Found " . $users->count() . " Retaining Officers.\n";

foreach ($users as $user) {
    if (!$user->phone) {
        echo "Skipping {$user->name} (no phone)\n";
        continue;
    }

    echo "Processing {$user->name} ({$user->phone})...\n";
    $user->password = Hash::make($user->phone);
    $user->save();

    // Verify
    $user->refresh();
    if (Hash::check($user->phone, $user->password)) {
        echo "  [OK] Password set to phone number.\n";
    } else {
        echo "  [ERROR] Failed to set password for {$user->name}.\n";
    }
}
echo "Done.\n";
