<?php
$file = 'c:\laragon\www\Apps\zonal first timers\resources\views\admin\bringers\index.blade.php';
$content = file_get_contents($file);

// 1. Category
$content = preg_replace(
    "/\\\$categoryBringerCount = \\\$category->groups->sum\(fn\(\\\$g\) => \\\$g->churches->sum\(fn\(\\\$c\) => \\\$c->bringers->count\(\)\)\);/s",
    "\$categoryBringerCount = \$category->groups->sum(fn(\$g) => \$g->churches->sum(fn(\$c) => \$c->bringers->sum(fn(\$b) => \$b->firstTimers->count())));",
    $content
);
$content = str_replace('title="Total Bringers in Category"', 'title="Total First Timers in Category"', $content);

// 2. Group
$content = preg_replace(
    "/\\\$groupBringerCount = \\\$group->churches->sum\(fn\(\\\$c\) => \\\$c->bringers->count\(\)\);/s",
    "\$groupBringerCount = \$group->churches->sum(fn(\$c) => \$c->bringers->sum(fn(\$b) => \$b->firstTimers->count()));",
    $content
);
$content = str_replace('title="Total Bringers in Group"', 'title="Total First Timers in Group"', $content);

// 3. Church
$content = preg_replace(
    "/\\\$bringerCount = \\\$church->bringers->count\(\);/s",
    "\$bringerCount = \$church->bringers->sum(fn(\$b) => \$b->firstTimers->count());",
    $content
);
$content = str_replace('title="Total Bringers in Church"', 'title="Total First Timers in Church"', $content);

// We should also append "FT" to the numbers to match the "Members" tag and avoid confusion
$content = preg_replace(
    "/{{ \\\$categoryBringerCount }}\s*<\/span>\s*<\/div>/s",
    "{{ \$categoryBringerCount }} FT\n                            </span>\n                        </div>",
    $content
);

$content = preg_replace(
    "/{{ \\\$groupBringerCount }}\s*<\/span>\s*<span/s",
    "{{ \$groupBringerCount }} FT\n                                    </span>\n                                    <span",
    $content
);

$content = preg_replace(
    "/{{ \\\$bringerCount }}\s*<\/span>\s*<\/div>\s*<\/div>/s",
    "{{ \$bringerCount }} FT\n                                                    </span>\n                                                </div>\n                                            </div>",
    $content
);

file_put_contents($file, $content);
echo "Patched successfully.\n";
