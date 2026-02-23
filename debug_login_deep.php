<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

$phoneNumber = '0278019810';
echo "Searching for phone: '$phoneNumber'\n";
$user = User::where('phone', $phoneNumber)->first();

if ($user) {
    echo "User found: " . $user->name . " (ID: " . $user->id . ")\n";
    echo "Raw Phone in DB: [" . $user->phone . "]\n";
    echo "Password Hash in DB: [" . $user->password . "]\n";

    if (empty($user->password)) {
        echo "WARNING: Password is EMPTY in database!\n";
    }

    // Try manual Hash check
    try {
        $match = Hash::check($phoneNumber, $user->password ?: '');
        echo "Hash check for '$phoneNumber': " . ($match ? "MATCH" : "NO MATCH") . "\n";
    } catch (\Exception $e) {
        echo "Hash::check ERROR: " . $e->getMessage() . "\n";
    }

    // Test Auth::attempt logic
    try {
        $attempt = Auth::attempt(['phone' => $phoneNumber, 'password' => $phoneNumber]);
        echo "Auth::attempt (phone/pass) result: " . ($attempt ? "SUCCESS" : "FAILURE") . "\n";
    } catch (\Exception $e) {
        echo "Auth::attempt ERROR: " . $e->getMessage() . "\n";
    }

} else {
    echo "User not found\n";
}
