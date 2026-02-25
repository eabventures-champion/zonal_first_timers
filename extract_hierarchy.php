<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ChurchCategory;
use App\Models\ChurchGroup;
use App\Models\Church;

$data = [
    'categories' => ChurchCategory::all()->toArray(),
    'groups' => ChurchGroup::all()->toArray(),
    'churches' => Church::all()->toArray(),
];

file_put_contents('hierarchy.json', json_encode($data, JSON_PRETTY_PRINT));
echo "Done.";
