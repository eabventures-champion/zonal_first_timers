@extends('layouts.app')
@section('title', 'Foundation School')
@section('page-title', 'Foundation School')

@section('content')
    <div x-data="{ 
                        selectedClass: { id: null, name: '', description: '' },
                        openEdit(item) {
                            this.selectedClass = { ...item };
                            $dispatch('open-modal', 'edit-class-modal');
                        }
                    }">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18 18.247 18.477 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white">Foundation School</h1>
                    <p class="text-xs text-gray-500 dark:text-slate-400">Classes and progression tracking</p>
                </div>
            </div>
            @if(auth()->user()->hasRole('Super Admin'))
                <button @click="$dispatch('open-modal', 'add-class-modal')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Class
                </button>
            @endif
        </div>

        @php $fsClasses = collect($classes); @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            @foreach($fsClasses as $fClass)
                <div
                    class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-5 hover:shadow-md transition-shadow relative group">
                    @if(auth()->user()->hasRole('Super Admin'))
                        <div class="absolute top-4 right-4 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="openEdit({{ json_encode($fClass) }})"
                                class="p-1.5 rounded-lg bg-gray-50 dark:bg-slate-800 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                title="Edit Class">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <form action="{{ route('admin.foundation-school.classes.destroy', $fClass) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this class?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg bg-gray-50 dark:bg-slate-800 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors" title="Delete Class">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endif

                    <div class="flex items-center gap-3 mb-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                            {{ $fClass->class_number }}
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-100">{{ $fClass->name }}</h3>
                        </div>
                    </div>
                    @if($fClass->description)
                        <p class="text-sm text-gray-500 dark:text-slate-400">{{ $fClass->description }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Edit Class Modal --}}
        <x-modal name="edit-class-modal" focusable>
            <form
                :action="'{{ route('admin.foundation-school.classes.update', '__ID__') }}'.replace('__ID__', selectedClass.id)"
                method="POST" class="p-6">
                @csrf
                @method('PUT')

                <h2 class="text-lg font-medium text-gray-900 dark:text-slate-100 mb-4">
                    Edit Class <span x-text="selectedClass.class_number"></span>
                </h2>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="edit_name" value="Class Title" />
                        <x-text-input id="edit_name" name="name" type="text" class="mt-1 block w-full"
                            x-model="selectedClass.name" required />
                    </div>

                    <div>
                        <x-input-label for="edit_number" value="Class Number" />
                        <x-text-input id="edit_number" name="class_number" type="text" class="mt-1 block w-full"
                            x-model="selectedClass.class_number" required />
                    </div>

                    <div>
                        <x-input-label for="edit_description" value="Description" />
                        <textarea id="edit_description" name="description"
                            class="mt-1 block w-full border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            rows="3" x-model="selectedClass.description"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Cancel
                    </x-secondary-button>

                    <x-primary-button>
                        Save Changes
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        {{-- Add Class Modal --}}
        <x-modal name="add-class-modal" focusable>
            <form action="{{ route('admin.foundation-school.classes.store') }}" method="POST" class="p-6">
                @csrf

                <h2 class="text-lg font-medium text-gray-900 dark:text-slate-100 mb-4">
                    Add New Foundation School Class
                </h2>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="add_name" value="Class Title" />
                        <x-text-input id="add_name" name="name" type="text" class="mt-1 block w-full"
                            placeholder="e.g., Class 1 - New Life" required />
                    </div>

                    <div>
                        <x-input-label for="add_number" value="Class Number" />
                        <x-text-input id="add_number" name="class_number" type="text" class="mt-1 block w-full"
                            placeholder="e.g., 4A" required />
                    </div>

                    <div>
                        <x-input-label for="add_description" value="Description" />
                        <textarea id="add_description" name="description"
                            class="mt-1 block w-full border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            rows="3" placeholder="Brief summary of the class content..."></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Cancel
                    </x-secondary-button>

                    <x-primary-button>
                        Add Class
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        {{-- Search & Grouped Progress --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300">First Timers Progress in Foundation School</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                        Total in Progress: <span
                            class="font-bold text-indigo-600 dark:text-indigo-400">{{ $totalInProgress }}</span>
                    </p>
                </div>

                <form action="{{ route('admin.foundation-school.index') }}" method="GET"
                    class="relative group w-full md:w-64">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by name..."
                        class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-slate-200">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>
            </div>

            <div class="space-y-4">
                @forelse($hierarchicalData as $category)
                    <div x-data="{ expanded: false }"
                        class="border border-gray-100 dark:border-slate-800 rounded-lg overflow-hidden transition-all shadow-sm">
                        <button @click="expanded = !expanded"
                            class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-slate-800/50 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="p-1 rounded-md bg-white dark:bg-slate-900 text-gray-400 shadow-sm border border-gray-100 dark:border-slate-800">
                                    <svg class="w-4 h-4 transition-transform" :class="expanded ? 'rotate-90' : ''" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                                <span class="font-bold text-gray-900 dark:text-slate-200 text-sm tracking-tight">{{ $category['name'] }}</span>
                                <span class="text-[10px] text-gray-500 font-medium">({{ $category['total_students'] }} Students)</span>
                            </div>
                        </button>

                        <div x-show="expanded" x-collapse>
                            <div class="p-4 space-y-6">
                                @foreach($category['groups'] as $group)
                                    <div x-data="{ expanded: true }" class="ml-4">
                                        <button @click="expanded = !expanded"
                                            class="flex items-center justify-between w-full mb-3 group/grp">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-3 h-3 text-gray-400 transition-transform group-hover/grp:text-indigo-500"
                                                    :class="expanded ? 'rotate-90' : ''" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7" />
                                                </svg>
                                                <span class="text-xs font-bold text-gray-600 dark:text-slate-400 uppercase tracking-wider">{{ $group['name'] }}</span>
                                                <span class="text-[10px] text-gray-400 font-normal">({{ $group['total_students'] }} Students)</span>
                                            </div>
                                        </button>

                                        <div x-show="expanded" x-collapse
                                            class="ml-5 border-l-2 border-gray-100 dark:border-slate-800 space-y-4">
                                            @foreach($group['churches'] as $church)
                                                <div x-data="{ expanded: false }" class="bg-gray-50/30 dark:bg-slate-800/20 rounded-lg p-3">
                                                    <button @click="expanded = !expanded" class="w-full flex items-center justify-between mb-2">
                                                        <div class="flex items-center gap-2">
                                                            <span class="p-0.5 rounded bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 text-gray-400">
                                                                <svg class="w-3 h-3 transition-transform" :class="expanded ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                                </svg>
                                                            </span>
                                                            <span class="text-sm font-semibold text-gray-800 dark:text-slate-300">{{ $church['name'] }}</span>
                                                            <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50 dark:bg-indigo-500/10 px-1.5 py-0.5 rounded">{{ $church['total_students'] }}</span>
                                                        </div>
                                                    </button>

                                                    <div x-show="expanded" x-collapse class="mt-3 space-y-4">
                                                        @foreach($church['students_grouped'] as $status => $students)
                                                            @if($students->isNotEmpty())
                                                                <div class="space-y-2">
                                                                    @php
                                                                        $statusColors = [
                                                                            'not yet' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400',
                                                                            'in-progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                                                            'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
                                                                        ];
                                                                    @endphp
                                                                    <div class="flex items-center gap-2 mb-2 px-2">
                                                                        <span class="w-1.5 h-1.5 rounded-full {{ str_contains($statusColors[$status], 'bg-emerald') ? 'bg-emerald-500' : (str_contains($statusColors[$status], 'bg-blue') ? 'bg-blue-500' : 'bg-slate-400') }}"></span>
                                                                        <span class="text-[10px] items-center font-bold uppercase tracking-widest text-gray-500 dark:text-slate-500">{{ $status }}</span>
                                                                        <span class="text-[9px] font-bold text-gray-400">({{ $students->count() }})</span>
                                                                    </div>
                                                                    
                                                                    {{-- Desktop Table --}}
                                                                    <div class="hidden sm:block overflow-x-auto">
                                                                        <table class="w-full text-left text-xs">
                                                                            <thead class="text-[9px] uppercase font-bold text-gray-400 dark:text-slate-600 border-b border-gray-100 dark:border-slate-800/50">
                                                                                <tr>
                                                                                    <th class="px-3 py-1.5">Name</th>
                                                                                    <th class="px-3 py-1.5 text-center">Progress</th>
                                                                                    <th class="px-3 py-1.5 text-right">Action</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody class="divide-y divide-gray-50 dark:divide-slate-800/30">
                                                                                @foreach($students as $ft)
                                                                                    @php
                                                                                        $completedCount = $ft->foundationAttendances->where('completed', true)->count();
                                                                                        $totalClasses = $classes->count();
                                                                                        $pct = $totalClasses > 0 ? round(($completedCount / $totalClasses) * 100) : 0;
                                                                                    @endphp
                                                                                    <tr class="hover:bg-gray-100/50 dark:hover:bg-slate-800/50 transition-colors">
                                                                                        <td class="px-3 py-2">
                                                                                            <div class="font-semibold text-gray-800 dark:text-slate-200">{{ $ft->full_name }}</div>
                                                                                            <div class="text-[9px] text-gray-400">{{ $ft->primary_contact }}</div>
                                                                                        </td>
                                                                                        <td class="px-3 py-2">
                                                                                            <div class="flex items-center gap-2 min-w-[80px]">
                                                                                                <div class="flex-1 bg-gray-100 dark:bg-slate-800 rounded-full h-1 overflow-hidden">
                                                                                                    <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $pct }}%"></div>
                                                                                                </div>
                                                                                                <span class="text-[9px] font-bold text-gray-400">{{ $completedCount }}/{{ $totalClasses }}</span>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="px-3 py-2 text-right">
                                                                                            <a href="{{ route('admin.foundation-school.show', ['id' => $ft->id, 'member' => $ft->is_member_record ? 1 : 0]) }}"
                                                                                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-[10px] font-bold uppercase tracking-wider">
                                                                                                View
                                                                                            </a>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>

                                                                    {{-- Mobile Card List --}}
                                                                    <div class="sm:hidden space-y-2">
                                                                        @foreach($students as $ft)
                                                                            @php
                                                                                $completedCount = $ft->foundationAttendances->where('completed', true)->count();
                                                                                $totalClasses = $classes->count();
                                                                                $pct = $totalClasses > 0 ? round(($completedCount / $totalClasses) * 100) : 0;
                                                                            @endphp
                                                                            <div class="bg-white dark:bg-slate-800/40 p-3 rounded-lg border border-gray-100 dark:border-slate-800/60 shadow-sm flex items-center justify-between gap-3">
                                                                                <div class="flex-1 min-w-0">
                                                                                    <div class="font-bold text-gray-900 dark:text-slate-200 text-xs truncate">{{ $ft->full_name }}</div>
                                                                                    <div class="flex items-center gap-2 mt-1">
                                                                                        <div class="flex-1 bg-gray-100 dark:bg-slate-700 rounded-full h-1 overflow-hidden max-w-[60px]">
                                                                                            <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $pct }}%"></div>
                                                                                        </div>
                                                                                        <span class="text-[10px] font-bold text-gray-500 dark:text-slate-500">{{ $completedCount }}/{{ $totalClasses }}</span>
                                                                                    </div>
                                                                                </div>
                                                                                <a href="{{ route('admin.foundation-school.show', ['id' => $ft->id, 'member' => $ft->is_member_record ? 1 : 0]) }}"
                                                                                    class="p-2 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-lg shrink-0">
                                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                                                </a>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-gray-400 dark:text-slate-600 italic">
                        No progress data found matching your criteria.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection