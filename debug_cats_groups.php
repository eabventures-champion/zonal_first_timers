<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\ChurchCategory;
use App\Models\ChurchGroup;

echo "CATEGORIES:\n";
foreach (ChurchCategory::all() as $cat) {
    echo "ID: {$cat->id} | Name: {$cat->name}\n";
}

echo "\nGROUPS:\n";
foreach (ChurchGroup::all() as $group) {
    echo "ID: {$group->id} | Name: {$group->name} | Category ID: {$group->church_category_id}\n";
}
