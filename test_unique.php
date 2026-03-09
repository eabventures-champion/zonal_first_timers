<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\FirstTimer;
use App\Models\User;

try {
    $data1 = [
        'church_id' => 1,
        'full_name' => 'Testing Row 6',
        'primary_contact' => null,
        'alternate_contact' => null,
        'gender' => 'Male',
        'residential_address' => 'Test',
        'marital_status' => 'Single',
        'email' => null,
        'bringer_name' => 'Wisdom Awudi',
        'bringer_contact' => '0278019810',
        'date_of_visit' => now(),
        'status' => 'New',
    ];
    $data2 = $data1;
    $data2['full_name'] = 'Testing Row 7';

    // Cleanup previous runs
    FirstTimer::where('full_name', 'like', 'Testing Row%')->forceDelete();
    User::where('name', 'like', 'Testing Row%')->forceDelete();

    // Row 6
    echo "Creating Row 6...\n";
    $firstTimer1 = FirstTimer::create($data1);

    $user1 = User::firstOrCreate(
        ['phone' => $firstTimer1->primary_contact],
        [
            'name' => $firstTimer1->full_name,
            'email' => $firstTimer1->email ?? ($firstTimer1->primary_contact . '@church.com'),
            'password' => $firstTimer1->primary_contact,
            'church_id' => $firstTimer1->church_id,
        ]
    );
    echo "Row 6 created: FT ID {$firstTimer1->id}, User ID {$user1->id}\n";

    // Row 7
    echo "Creating Row 7...\n";
    $firstTimer2 = FirstTimer::create($data2);

    $user2 = User::firstOrCreate(
        ['phone' => $firstTimer2->primary_contact],
        [
            'name' => $firstTimer2->full_name,
            'email' => $firstTimer2->email ?? ($firstTimer2->primary_contact . '@church.com'),
            'password' => $firstTimer2->primary_contact,
            'church_id' => $firstTimer2->church_id,
        ]
    );
    echo "Row 7 created: FT ID {$firstTimer2->id}, User ID {$user2->id}\n";

} catch (\Exception $e) {
    echo "Exception Class: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
}
