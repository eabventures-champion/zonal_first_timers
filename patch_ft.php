<?php
$file = 'c:\laragon\www\Apps\zonal first timers\resources\views\admin\first-timers\index.blade.php';
$lines = file($file);

$newContent = <<<'EOF'
    {{-- Table --}}
    <div x-data="{ 
            expandedCategories: new Set(),
            toggleCategory(name) {
                if (this.expandedCategories.has(name)) {
                    this.expandedCategories.delete(name);
                } else {
                    this.expandedCategories.add(name);
                }
            },
            expandedChurches: new Set(),
            toggleChurch(name) {
                if (this.expandedChurches.has(name)) {
                    this.expandedChurches.delete(name);
                } else {
                    this.expandedChurches.add(name);
                }
            },
            showHistory: false, 
            historyName: '', 
            historyDates: [] 
        }" class="space-y-8">
        @forelse($groupedFirstTimers as $categoryName => $churches)
            <div class="space-y-4">
                {{-- Category Header --}}
                <div class="flex items-center gap-2 px-1">
                    <button @click="toggleCategory('{{ addslashes($categoryName) }}')"
                        class="flex items-center gap-2 hover:opacity-70 transition cursor-pointer">
                        <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                            :class="{ '-rotate-90': !expandedCategories.has('{{ addslashes($categoryName) }}') }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">
                            {{ $categoryName }}
                        </h2>
                        @php
                            $categoryTotal = $churches->sum(fn($churchItems) => $churchItems->count());
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400">
                            {{ $categoryTotal }} {{ Str::plural('First Timer', $categoryTotal) }}
                        </span>
                    </button>
                    <div class="flex-1 border-t border-gray-200 dark:border-slate-700 ml-2"></div>
                </div>

                {{-- Churches List --}}
                <div x-show="expandedCategories.has('{{ addslashes($categoryName) }}')" x-collapse class="pl-4 space-y-8 border-l-2 border-gray-100 dark:border-slate-800 ml-3 py-2">
                    @foreach($churches as $churchName => $churchItems)
                        <div class="space-y-3">
                            {{-- Church Header --}}
                            <div class="flex items-center gap-2 px-1">
                                <button @click="toggleChurch('{{ addslashes($categoryName . '_' . $churchName) }}')"
                                    class="flex items-center gap-2 hover:opacity-70 transition cursor-pointer">
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                        :class="{ '-rotate-90': !expandedChurches.has('{{ addslashes($categoryName . '_' . $churchName) }}') }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                    <h3 class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                        {{ $churchName }}
                                    </h3>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                        {{ $churchItems->count() }} {{ Str::plural('First Timer', $churchItems->count()) }}
                                    </span>
                                </button>
                                <div class="flex-1 border-t border-gray-100 dark:border-slate-800 ml-2"></div>
                            </div>
                            
                            <div x-show="expandedChurches.has('{{ addslashes($categoryName . '_' . $churchName) }}')" x-collapse
                                class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
                                
                                {{-- Desktop Table --}}
                                <div class="hidden md:block overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-50 dark:bg-slate-800/50">
                                            <tr>
                                                <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Name</th>
                                                <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Contact</th>
                                                <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Church</th>
                                                <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">Attendance</th>
                                                <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">FS Level</th>
                                                <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">Status</th>
                                                <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-slate-400">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                                            @foreach($churchItems as $ft)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                                                    <td class="px-6 py-3">
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-medium text-gray-900 dark:text-white">{{ $ft->full_name }}</span>
                                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                                                {{ $ft->total_attended }} {{ Str::plural('Service', $ft->total_attended) }}
                                                            </span>
                                                        </div>
                                                        <div class="text-[10px] text-gray-400 dark:text-slate-500">Joined: {{ $ft->date_of_visit?->format('M d, Y') }}</div>
                                                    </td>
                                                    <td class="px-6 py-3 text-gray-500 dark:text-slate-400">{{ $ft->primary_contact }}</td>
                                                    <td class="px-6 py-3 text-gray-500 dark:text-slate-400">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-1.5 h-1.5 rounded-full bg-indigo-400"></div>
                                                            <span>{{ $ft->church->name ?? '—' }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-3 text-center">
                                                        <button type="button" @click="historyName = '{{ $ft->full_name }}'; historyDates = {{ json_encode($ft->attendance_dates) }}; showHistory = true"
                                                            class="inline-flex items-center gap-1.5 px-2 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-md hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition cursor-pointer">
                                                            <span class="text-xs font-bold">{{ $ft->total_attended }}</span>
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        </button>
                                                    </td>
                                                    <td class="px-6 py-3 text-center">
                                                        <div class="flex flex-col items-center gap-1">
                                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded {{ $ft->foundation_school_status === 'in-progress' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-slate-400 bg-gray-100 dark:bg-slate-800' }}">
                                                                {{ $ft->foundation_school_status }}
                                                            </span>
                                                            @if($ft->foundation_school_status === 'in-progress')
                                                                <span class="text-[10px] text-blue-600 dark:text-blue-400 font-medium italic">
                                                                    {{ $ft->current_foundation_level }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-3 text-center">
                                                        @php
                                                            $sc = [
                                                                'New' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-500',
                                                                'Developing' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                                                'Retained' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                                            ];
                                                        @endphp
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $sc[$ft->status] ?? 'bg-gray-100 dark:bg-slate-800' }}">{{ $ft->status }}</span>
                                                    </td>
                                                    <td class="px-6 py-3 text-right space-x-1">
                                                        <a href="{{ route('admin.first-timers.show', $ft) }}" class="text-sky-600 hover:text-sky-800 dark:text-sky-400 dark:hover:text-sky-300 text-xs font-medium px-2 py-1">View</a>
                                                        <a href="{{ route('admin.first-timers.edit', $ft) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs font-medium px-2 py-1">Edit</a>
                                                        <form method="POST" action="{{ route('admin.first-timers.destroy', $ft) }}" class="inline" onsubmit="return confirm('Delete this record?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs font-medium px-2 py-1">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Mobile Card List --}}
                                <div class="md:hidden divide-y divide-gray-100 dark:divide-slate-800">
                                    @foreach($churchItems as $ft)
                                        <div class="p-4 space-y-4">
                                            <div class="flex items-start justify-between">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xs shrink-0">
                                                        {{ strtoupper(substr($ft->full_name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $ft->full_name }}</h4>
                                                        <p class="text-[11px] text-gray-500 dark:text-slate-400">{{ $ft->primary_contact }}</p>
                                                    </div>
                                                </div>
                                                @php
                                                    $sc = [
                                                        'New' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-500',
                                                        'Developing' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                                        'Retained' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                                    ];
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider {{ $sc[$ft->status] ?? 'bg-gray-100 dark:bg-slate-800 text-gray-500' }}">
                                                    {{ $ft->status }}
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3 pb-2">
                                                <div class="bg-gray-50 dark:bg-slate-800/50 p-2 rounded-lg border border-gray-100 dark:border-slate-800/50">
                                                    <p class="text-[9px] text-gray-400 dark:text-slate-500 uppercase tracking-widest font-bold mb-1">Attendance</p>
                                                    <button type="button" @click="historyName = '{{ $ft->full_name }}'; historyDates = {{ json_encode($ft->attendance_dates) }}; showHistory = true"
                                                        class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                                                        <span class="text-xs font-bold">{{ $ft->total_attended }} Services</span>
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="bg-gray-50 dark:bg-slate-800/50 p-2 rounded-lg border border-gray-100 dark:border-slate-800/50">
                                                    <p class="text-[9px] text-gray-400 dark:text-slate-500 uppercase tracking-widest font-bold mb-1">FS Level</p>
                                                    <span class="text-[11px] font-bold text-gray-700 dark:text-slate-300">
                                                        {{ $ft->foundation_school_status === 'in-progress' ? ($ft->current_foundation_level ?: 'In Progress') : 'Not Started' }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between pt-2 border-t border-gray-50 dark:border-slate-800/50">
                                                <div class="flex items-center gap-1.5 text-[10px] text-gray-400 dark:text-slate-500 font-medium">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                                    {{ $ft->church->name ?? '—' }}
                                                </div>
                                                <div class="flex gap-2">
                                                    <a href="{{ route('admin.first-timers.show', $ft) }}" class="p-2 bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 rounded-lg transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                    </a>
                                                    <a href="{{ route('admin.first-timers.edit', $ft) }}" class="p-2 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-lg transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.first-timers.destroy', $ft) }}" class="inline" onsubmit="return confirm('Delete this record?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="p-2 bg-red-50 dark:bg-red-500/10 text-red-500 dark:text-red-400 rounded-lg transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
EOF;

array_splice($lines, 101, 185, $newContent . "\n");
file_put_contents($file, implode("", $lines));
echo "Patched successfully.\n";
