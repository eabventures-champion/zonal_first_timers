<?php
$file = 'c:\laragon\www\Apps\zonal first timers\resources\views\admin\bringers\index.blade.php';
$content = file_get_contents($file);

// Replace the individual bringer counts logic
$oldLogic1 = <<<EOF
                                                                @php
                                                                    \$firstTimerCount = \$bringer->firstTimers->where('status', '!=', 'Retained')->count();
                                                                    \$memberCount = \$bringer->firstTimers->where('status', 'Retained')->count();
                                                                @endphp
EOF;

// Since we replaced it already but maybe it failed due to some reason or we used a different spacing:
// Let's use preg_replace to be 100% sure.
$content = preg_replace(
    "/@php\s+\\\$firstTimerCount = \\\$bringer->firstTimers->where\('status', '!=', 'Retained'\)->count\(\);\s+\\\$memberCount = \\\$bringer->firstTimers->where\('status', 'Retained'\)->count\(\);\s+@endphp/s",
    "@php\n                                                                    \$firstTimerCount = \$bringer->firstTimers->count();\n                                                                    \$memberCount = \$bringer->members->count();\n                                                                @endphp",
    $content
);

// We also need to add the church member count right next to the church bringer count
// Let's find:
// <h4 class="font-bold text-slate-800 dark:text-white">{{ $church->name }}</h4>
// <span
//     class="px-2 py-0.5 bg-indigo-600 text-white text-[10px] font-bold rounded-full shadow-sm"
//     title="Total Bringers in Church">
//     {{ $bringerCount }}
// </span>

$oldChurchLogic = <<<EOF
                                                <h4 class="font-bold text-slate-800 dark:text-white">{{ \$church->name }}</h4>
                                                <span
                                                    class="px-2 py-0.5 bg-indigo-600 text-white text-[10px] font-bold rounded-full shadow-sm"
                                                    title="Total Bringers in Church">
                                                    {{ \$bringerCount }}
                                                </span>
EOF;

$newChurchLogic = <<<EOF
                                                <h4 class="font-bold text-slate-800 dark:text-white">{{ \$church->name }}</h4>
                                                @php
                                                    \$churchMemberCount = \$church->bringers->sum(fn(\$b) => \$b->members->count());
                                                @endphp
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="px-2 py-0.5 bg-black text-white text-[10px] font-bold rounded-full shadow-sm"
                                                        title="Total Members in Church">
                                                        {{ \$churchMemberCount }} Members
                                                    </span>
                                                    <span
                                                        class="px-2 py-0.5 bg-indigo-600 text-white text-[10px] font-bold rounded-full shadow-sm"
                                                        title="Total Bringers in Church">
                                                        {{ \$bringerCount }}
                                                    </span>
                                                </div>
EOF;

// If the string hasn't been replaced yet
if (strpos($content, "@php\n                                                    \$churchMemberCount = \$church->bringers->sum(fn(\$b) => \$b->members->count());") === false) {
    // try exact string replacement
    $content = str_replace($oldChurchLogic, $newChurchLogic, $content);
}

file_put_contents($file, $content);
echo "Patched successfully.\n";
