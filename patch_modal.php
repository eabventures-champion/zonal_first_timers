<?php
$file = 'c:\laragon\www\Apps\zonal first timers\resources\views\admin\bringers\index.blade.php';
$lines = file($file);

$newContent = <<<'EOF'
                    <div class="space-y-3">
                        <template x-for="(ft, index) in firstTimers" :key="index">
                            <div
                                class="flex items-center justify-between p-3 rounded-xl border"
                                :class="ft.status === 'Retained' ? 'bg-slate-900/5 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700' : 'bg-slate-50 dark:bg-slate-800/30 border-slate-100 dark:border-slate-800/50'">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full flex items-center justify-center"
                                        :class="ft.status === 'Retained' ? 'bg-black text-white dark:bg-black dark:text-white' : 'bg-indigo-100 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200"
                                            x-text="ft.name"></span>
                                        <span class="text-[10px] font-bold uppercase tracking-wider mt-0.5"
                                            :class="ft.status === 'Retained' ? 'text-slate-800 dark:text-white' : 'text-slate-400'"
                                            x-text="ft.status === 'Retained' ? 'Member' : 'First Timer'"></span>
                                    </div>
                                </div>
                                <span
                                    class="text-[10px] font-bold text-slate-400 uppercase bg-white dark:bg-slate-900 px-2 py-1 rounded-md border border-slate-100 dark:border-slate-800 shadow-sm"
                                    x-text="ft.date"></span>
                            </div>
                        </template>
                    </div>
EOF;

array_splice($lines, 163, 19, $newContent . "\n");
file_put_contents($file, implode("", $lines));
echo "Patched successfully.\n";
