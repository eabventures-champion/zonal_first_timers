<?php
$file = 'c:\laragon\www\Apps\zonal first timers\resources\views\admin\bringers\index.blade.php';
$content = file_get_contents($file);

// 1. Add contact to the modal data array
$oldMap = <<<EOF
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

// if it hasn't been added yet
if (strpos($content, "'contact' => \$ft->primary_contact") === false) {
    // Regex based replace to be safe against spacing
    $content = preg_replace(
        "/collect\(\\\$bringer->firstTimers->map\(fn\(\\\$ft\) => \[\s*'name' => \\\$ft->full_name,\s*'date' => \\\$ft->date_of_visit \? \\\$ft->date_of_visit->format\('M d, Y'\) : '',\s*'status' => 'First Timer'\s*\]\)\)->merge\(\\\$bringer->members->map\(fn\(\\\$m\) => \[\s*'name' => \\\$m->full_name,\s*'date' => \\\$m->migrated_at \? \\\$m->migrated_at->format\('M d, Y'\) : \(\\\$m->date_of_visit \? \\\$m->date_of_visit->format\('M d, Y'\) : ''\),\s*'status' => 'Retained'\s*\]\)\)->toJson\(\) /s",
        "collect(\$bringer->firstTimers->map(fn(\$ft) => [\n                                                    'name' => \$ft->full_name,\n                                                    'contact' => \$ft->primary_contact ?? 'N/A',\n                                                    'date' => \$ft->date_of_visit ? \$ft->date_of_visit->format('M d, Y') : '',\n                                                    'status' => 'First Timer'\n                                                ]))->merge(\$bringer->members->map(fn(\$m) => [\n                                                    'name' => \$m->full_name,\n                                                    'contact' => \$m->primary_contact ?? 'N/A',\n                                                    'date' => \$m->migrated_at ? \$m->migrated_at->format('M d, Y') : (\$m->date_of_visit ? \$m->date_of_visit->format('M d, Y') : ''),\n                                                    'status' => 'Retained'\n                                                ]))->toJson() ",
        $content
    );
}

// 2. Add contact to the HTML
$oldHtml = <<<EOF
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200"
                                            x-text="ft.name"></span>
                                        <span class="text-[10px] font-bold uppercase tracking-wider mt-0.5"
                                            :class="ft.status === 'Retained' ? 'text-slate-800 dark:text-white' : 'text-slate-400'"
                                            x-text="ft.status === 'Retained' ? 'Member' : 'First Timer'"></span>
                                    </div>
EOF;

$newHtml = <<<EOF
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200"
                                            x-text="ft.name"></span>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[10px] font-bold uppercase tracking-wider"
                                                :class="ft.status === 'Retained' ? 'text-slate-800 dark:text-white' : 'text-slate-400'"
                                                x-text="ft.status === 'Retained' ? 'Member' : 'First Timer'"></span>
                                            <span class="text-[10px] text-slate-500 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                <span x-text="ft.contact"></span>
                                            </span>
                                        </div>
                                    </div>
EOF;

// Replace if not already replaced
if (strpos($content, "x-text=\"ft.contact\"></span>") === false) {
    $content = str_replace($oldHtml, $newHtml, $content);
}

// 3. Remove duplicate </template> block
$content = preg_replace('/(\s*<\/template>\s*<\/div>\s*)<\/template>\s*<\/div>/s', '$1', $content);

// 4. Remove footer layout block (the floating close button)
$footerBlock = <<<EOF
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    <button @click="showModal = false"
                        class="w-full py-2.5 bg-slate-900 dark:bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-slate-800 dark:hover:bg-indigo-700 transition-all shadow-lg shadow-slate-200 dark:shadow-none">
                        Close
                    </button>
                </div>
EOF;

$content = str_replace($footerBlock, "", $content);

file_put_contents($file, $content);
echo "Patched successfully.\n";
