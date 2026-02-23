<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$phone = '0278019810';
$user = User::where('phone', $phone)->first();

if (!$user) {
    die("User not found\n");
}

echo "User: {$user->name}\n";
echo "Phone: {$user->phone}\n";
echo "Hashed password in DB: {$user->password}\n";

$isCorrect = Hash::check($phone, $user->password);
echo "Does '{$phone}' match hash? " . ($isCorrect ? "YES" : "NO") . "\n";

if (!$isCorrect) {
    echo "Resetting password to phone number...\n";
    $user->password = Hash::make($phone);
    $user->save();

    // Verify again
    $user->refresh();
    $isCorrectNow = Hash::check($phone, $user->password);
    echo "New hash: {$user->password}\n";
    echo "Does it match now? " . ($isCorrectNow ? "YES" : "NO") . "\n";
}
