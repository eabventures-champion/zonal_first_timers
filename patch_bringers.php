<?php
$file = 'c:\laragon\www\Apps\zonal first timers\resources\views\admin\bringers\index.blade.php';
$lines = file($file);

$newContent = <<<'EOF'
        <div class="space-y-8">
            @foreach($categories as $category)
                <div x-data="{ expandedCategory: true }"
                    class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div @click="expandedCategory = !expandedCategory"
                        class="cursor-pointer bg-slate-50 dark:bg-slate-800/50 px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-slate-400 transition-transform duration-200"
                                :class="{'rotate-180': expandedCategory}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ $category->name }}</h2>
                        </div>
                        @php
                            $categoryBringerCount = $category->groups->sum(fn($g) => $g->churches->sum(fn($c) => $c->bringers->count()));
                            $categoryMemberCount = $category->groups->sum(fn($g) => $g->churches->sum(fn($c) => $c->bringers->sum(fn($b) => $b->firstTimers->where('status', 'Retained')->count())));
                        @endphp
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 bg-black text-white text-[10px] font-bold rounded-full shadow-sm"
                                title="Total Members in Category">
                                {{ $categoryMemberCount }} Members
                            </span>
                            <span class="px-2 py-0.5 bg-orange-500 text-white text-[10px] font-bold rounded-full shadow-sm"
                                title="Total Bringers in Category">
                                {{ $categoryBringerCount }}
                            </span>
                        </div>
                    </div>

                    <div x-show="expandedCategory" x-collapse class="p-6 space-y-8">
                        @foreach($category->groups as $group)
                            <div>
                                <h3 class="text-md font-bold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                    <span>{{ $group->name }}</span>
                                    @php
                                        $groupBringerCount = $group->churches->sum(fn($c) => $c->bringers->count());
                                        $groupMemberCount = $group->churches->sum(fn($c) => $c->bringers->sum(fn($b) => $b->firstTimers->where('status', 'Retained')->count()));
                                    @endphp
                                    <span
                                        class="px-2 py-0.5 bg-indigo-600 text-white text-[10px] font-bold rounded-full shadow-sm ml-2"
                                        title="Total Bringers in Group">
                                        {{ $groupBringerCount }}
                                    </span>
                                    <span
                                        class="px-2 py-0.5 bg-black text-white text-[10px] font-bold rounded-full shadow-sm"
                                        title="Total Members in Group">
                                        {{ $groupMemberCount }} Members
                                    </span>
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach($group->churches as $church)
                                        @php
                                            $bringerCount = $church->bringers->count();
                                        @endphp
                                        <div
                                            class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 border border-slate-200 dark:border-slate-800">
                                            <div
                                                class="flex items-center justify-between mb-4 border-b border-slate-200 dark:border-slate-700 pb-3">
                                                <h4 class="font-bold text-slate-800 dark:text-white">{{ $church->name }}</h4>
                                                <span
                                                    class="px-2 py-0.5 bg-indigo-600 text-white text-[10px] font-bold rounded-full shadow-sm"
                                                    title="Total Bringers in Church">
                                                    {{ $bringerCount }}
                                                </span>
                                            </div>

                                            <div class="space-y-1">
                                                @forelse($church->bringers as $bringer)
                                                                @php
                                                                    $firstTimerCount = $bringer->firstTimers->where('status', '!=', 'Retained')->count();
                                                                    $memberCount = $bringer->firstTimers->where('status', 'Retained')->count();
                                                                @endphp
                                                                <div
                                                                    class="flex items-center justify-between py-2 px-2 hover:bg-white dark:hover:bg-slate-900 rounded-md transition-colors group">
                                                                    <div class="flex items-center gap-2">
                                                                        <span
                                                                            class="font-semibold text-sm text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $bringer->name }}</span>
                                                                        @if($bringer->is_ro)
                                                                            <span
                                                                                class="px-2 py-0.5 text-[10px] bg-indigo-100 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 rounded-full font-bold uppercase">RO</span>
                                                                        @endif
                                                                    </div>

                                                                    <div class="flex items-center gap-1 cursor-pointer" @click="openModal('{{ addslashes($bringer->name) }}', {{ 
                                                                                                                                                                                                                                                                            $bringer->firstTimers->map(fn($ft) => [
                                                    'name' => $ft->full_name,
                                                    'date' => $ft->date_of_visit->format('M d, Y'),
                                                    'status' => $ft->status
                                                ])->toJson() 
                                                                                                                                                                                                                                                                        }})">
                                                                        <span class="px-2.5 py-1 bg-black text-white text-[10px] font-bold rounded-full transition-colors shadow-sm" title="Members">
                                                                            {{ $memberCount }} M
                                                                        </span>
                                                                        <span class="px-2.5 py-1 bg-slate-700 hover:bg-indigo-600 dark:bg-slate-700 dark:hover:bg-indigo-600 text-white text-[10px] font-bold rounded-full transition-colors shadow-sm" title="First Timers">
                                                                            {{ $firstTimerCount }} FT
                                                                        </span>
                                                                    </div>
                                                                </div>
EOF;

array_splice($lines, 22, 79, $newContent . "\n");
file_put_contents($file, implode("", $lines));
echo "Patched successfully.\n";
