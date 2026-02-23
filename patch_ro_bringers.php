<?php
$file = 'c:\laragon\www\Apps\zonal first timers\resources\views\ro\bringers\index.blade.php';
$content = file_get_contents($file);

// Replace individual bringer button and add variables
$oldBringerBtn = <<<EOF
                                                    <button @click="openModal('{{ addslashes(\$bringer->name) }}', {{ 
                                                                    \$bringer->firstTimers->map(fn(\$ft) => [
                                    'name' => \$ft->full_name,
                                    'date' => \$ft->date_of_visit->format('M d, Y')
                                ])->toJson() 
                                                                }})"
                                                        class="px-2.5 py-1 bg-black dark:bg-slate-700 hover:bg-indigo-600 dark:hover:bg-indigo-600 text-white text-xs font-bold rounded-full transition-colors cursor-pointer shadow-sm">
                                                        {{ \$bringer->firstTimers->count() }}
                                                    </button>
EOF;

// if this string is somehow slightly off, we use preg_replace 
$newBringerBtn = <<<EOF
                                                    @php
                                                        \$firstTimerCount = \$bringer->firstTimers->count();
                                                        \$memberCount = \$bringer->members->count();
                                                    @endphp
                                                    <div class="flex items-center gap-1 cursor-pointer" @click="openModal('{{ addslashes(\$bringer->name) }}', {{ 
                                                        collect(\$bringer->firstTimers->map(fn(\$ft) => [
                                                            'name' => \$ft->full_name,
                                                            'contact' => \$ft->primary_contact ?? 'N/A',
                                                            'date' => \$ft->date_of_visit ? \$ft->date_of_visit->format('M d, Y') : '',
                                                            'status' => 'First Timer'
                                                        ]))->merge(\$bringer->members->map(fn(\$m) => [
                                                            'name' => \$m->full_name,
                                                            'contact' => \$m->primary_contact ?? 'N/A',
                                                            'date' => \$m->migrated_at ? \$m->migrated_at->format('M d, Y') : (\$m->date_of_visit ? \$m->date_of_visit->format('M d, Y') : ''),
                                                            'status' => 'Retained'
                                                        ]))->toJson() 
                                                    }})">
                                                        <span class="px-2.5 py-1 bg-black text-white text-[10px] font-bold rounded-full transition-colors shadow-sm" title="Members">
                                                            {{ \$memberCount }} M
                                                        </span>
                                                        <span class="px-2.5 py-1 bg-slate-700 hover:bg-indigo-600 dark:bg-slate-700 dark:hover:bg-indigo-600 text-white text-[10px] font-bold rounded-full transition-colors shadow-sm" title="First Timers">
                                                            {{ \$firstTimerCount }} FT
                                                        </span>
                                                    </div>
EOF;
// Let's replace the whole block exactly via preg_replace.
// We look for the button block down to the {{ bringer->count() }} inside it.
$content = preg_replace(
    '/<button\s+@click="openModal.*?<\/button>/s',
    $newBringerBtn,
    $content,
    1
);

// We need to also replace the modal HTML to include the status, contact logic, and remove footer.
// Let's just grab the whole modal div template body inside `firstTimers` loop
$oldModalHtml = <<<EOF
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200"
                                        x-text="ft.name"></span>
                                </div>
                                <span
                                    class="text-[10px] font-bold text-slate-400 uppercase bg-white dark:bg-slate-900 px-2 py-1 rounded-md border border-slate-100 dark:border-slate-800 shadow-sm"
                                    x-text="ft.date"></span>
EOF;

// The div enclosing it is:
// <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-800/50">

$content = preg_replace(
    "/<div\s+class=\"flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800\/30 border border-slate-100 dark:border-slate-800\/50\">.*?<\/div>/s",
    "<div class=\"flex items-center justify-between p-3 rounded-xl border\" :class=\"ft.status === 'Retained' ? 'bg-slate-900/5 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700' : 'bg-slate-50 dark:bg-slate-800/30 border-slate-100 dark:border-slate-800/50'\">\n" .
    "                                <div class=\"flex items-center gap-3\">\n" .
    "                                    <div class=\"w-8 h-8 rounded-full flex items-center justify-center\"\n" .
    "                                        :class=\"ft.status === 'Retained' ? 'bg-black text-white dark:bg-black dark:text-white' : 'bg-indigo-100 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'\">\n" .
    "                                        <svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">\n" .
    "                                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\" />\n" .
    "                                        </svg>\n" .
    "                                    </div>\n" .
    "                                    <div class=\"flex flex-col\">\n" .
    "                                        <span class=\"text-sm font-bold text-slate-700 dark:text-slate-200\" x-text=\"ft.name\"></span>\n" .
    "                                        <div class=\"flex items-center gap-2 mt-0.5\">\n" .
    "                                            <span class=\"text-[10px] font-bold uppercase tracking-wider\"\n" .
    "                                                :class=\"ft.status === 'Retained' ? 'text-slate-800 dark:text-white' : 'text-slate-400'\"\n" .
    "                                                x-text=\"ft.status === 'Retained' ? 'Member' : 'First Timer'\"></span>\n" .
    "                                            <span class=\"text-[10px] text-slate-500 flex items-center gap-1\">\n" .
    "                                                <svg class=\"w-3 h-3\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z\"></path></svg>\n" .
    "                                                <span x-text=\"ft.contact\"></span>\n" .
    "                                            </span>\n" .
    "                                        </div>\n" .
    "                                    </div>\n" .
    "                                </div>\n" .
    "                                <span class=\"text-[10px] font-bold text-slate-400 uppercase bg-white dark:bg-slate-900 px-2 py-1 rounded-md border border-slate-100 dark:border-slate-800 shadow-sm\" x-text=\"ft.date\"></span>\n" .
    "                            </div>",
    $content,
    1
);

// 3. Remove footer 
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
