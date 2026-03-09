<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\FirstTimerService;
use Illuminate\Http\UploadedFile;

// Cleanup testing
\App\Models\FirstTimer::where('bringer_name', 'Wisdom Awudi')->forceDelete();
\App\Models\FirstTimer::where('bringer_name', 'Clifford Adifu')->forceDelete();

$service = app(FirstTimerService::class);
$filePath = __DIR__ . '/public/first_timers_import_template.csv';

// Make sure the file exists or copy it if it's elsewhere
$file = new UploadedFile($filePath, 'first_timers_import_template.csv', 'text/csv', null, true);

// Execute the import wrapper but print out exact exceptions in the service layer
$importMethod = new \ReflectionMethod(FirstTimerService::class, 'importFromCsv');
$importMethod->setAccessible(true);
$results = $service->importFromCsv($file, 1); // use church id 1

print_r($results);

// Now read laravel.log to see if any errors were logged? We will just print the errors here.
