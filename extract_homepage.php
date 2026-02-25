<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HomepageSetting;

$data = HomepageSetting::all()->toArray();
file_put_contents('homepage_settings.json', json_encode($data, JSON_PRETTY_PRINT));
echo "Done.";
