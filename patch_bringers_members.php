<?php
$file = 'c:\laragon\www\Apps\zonal first timers\resources\views\admin\bringers\index.blade.php';
$content = file_get_contents($file);

// Replace category member count logic
$content = str_replace(
    "\$categoryMemberCount = \$category->groups->sum(fn(\$g) => \$g->churches->sum(fn(\$c) => \$c->bringers->sum(fn(\$b) => \$b->firstTimers->where('status', 'Retained')->count())));",
    "\$categoryMemberCount = \$category->groups->sum(fn(\$g) => \$g->churches->sum(fn(\$c) => \$c->bringers->sum(fn(\$b) => \$b->members->count())));",
    $content
);

// Replace group member count logic
$content = str_replace(
    "\$groupMemberCount = \$group->churches->sum(fn(\$c) => \$c->bringers->sum(fn(\$b) => \$b->firstTimers->where('status', 'Retained')->count()));",
    "\$groupMemberCount = \$group->churches->sum(fn(\$c) => \$c->bringers->sum(fn(\$b) => \$b->members->count()));",
    $content
);

// Replace individual bringer counts logic
$content = str_replace(
    "\$firstTimerCount = \$bringer->firstTimers->where('status', '!=', 'Retained')->count();\n                                                                    \$memberCount = \$bringer->firstTimers->where('status', 'Retained')->count();",
    "\$firstTimerCount = \$bringer->firstTimers->count();\n                                                                    \$memberCount = \$bringer->members->count();",
    $content
);

// We also need to update the data passed to the modal to include both FirstTimers and Members
$modalMapOld = <<<EOF
\$bringer->firstTimers->map(fn(\$ft) => [
                                                    'name' => \$ft->full_name,
                                                    'date' => \$ft->date_of_visit->format('M d, Y'),
                                                    'status' => \$ft->status
                                                ])->toJson()
EOF;

// Since we have separate models, we need a way to combine them into an array before converting to JSON.
// We can use collect() to merge them.
$modalMapNew = <<<EOF
collect(\$bringer->firstTimers->map(fn(\$ft) => [
                                                    'name' => \$ft->full_name,
                                                    'date' => \$ft->date_of_visit ? \$ft->date_of_visit->format('M d, Y') : '',
                                                    'status' => 'First Timer'
                                                ]))->merge(\$bringer->members->map(fn(\$m) => [
                                                    'name' => \$m->full_name,
                                                    'date' => \$m->migrated_at ? \$m->migrated_at->format('M d, Y') : (\$m->date_of_visit ? \$m->date_of_visit->format('M d, Y') : ''),
                                                    'status' => 'Retained'
                                                ]))->toJson()
EOF;

$content = str_replace($modalMapOld, $modalMapNew, $content);

file_put_contents($file, $content);
echo "Patched successfully.\n";
