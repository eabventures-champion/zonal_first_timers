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
        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Foundation School classes and progression tracking</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            @foreach($classes as $class)
                <div
                    class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-5 hover:shadow-md transition-shadow relative group">
                    @if(auth()->user()->hasRole('Super Admin'))
                        <button @click="openEdit({{ json_encode($class) }})"
                            class="absolute top-4 right-4 p-1.5 rounded-lg bg-gray-50 dark:bg-slate-800 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    @endif

                    <div class="flex items-center gap-3 mb-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                            {{ $class->class_number }}
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-100">{{ $class->name }}</h3>
                        </div>
                    </div>
                    @if($class->description)
                        <p class="text-sm text-gray-500 dark:text-slate-400">{{ $class->description }}</p>
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

        {{-- Search & Grouped Progress --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300">First Timer Progress</h3>
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
                @forelse($groupedData as $category)
                    <div x-data="{ expanded: {{ $search ? 'true' : 'false' }} }"
                        class="border border-gray-100 dark:border-slate-800 rounded-lg overflow-hidden transition-all">
                        <button @click="expanded = !expanded"
                            class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-slate-800/50 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="p-1 rounded-md bg-white dark:bg-slate-900 text-gray-400">
                                    <svg class="w-4 h-4 transition-transform" :class="expanded ? 'rotate-90' : ''" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                                <span
                                    class="font-semibold text-gray-900 dark:text-slate-200 text-sm">{{ $category->name }}</span>
                                <span
                                    class="bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                                    {{ $category->first_timers_count }}
                                </span>
                            </div>
                        </button>

                        <div x-show="expanded" x-collapse>
                            <div class="p-4 space-y-4">
                                @foreach($category->groups as $group)
                                    @if($group->first_timers_count > 0)
                                        <div x-data="{ expanded: {{ $search ? 'true' : 'false' }} }" class="ml-4">
                                            <button @click="expanded = !expanded"
                                                class="flex items-center gap-2 mb-2 text-sm font-medium text-gray-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400">
                                                <svg class="w-3 h-3 transition-transform" :class="expanded ? 'rotate-90' : ''"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7" />
                                                </svg>
                                                {{ $group->name }}
                                                <span
                                                    class="text-[10px] text-gray-400 font-normal">({{ $group->first_timers_count }})</span>
                                            </button>

                                            <div x-show="expanded" x-collapse
                                                class="ml-5 space-y-4 border-l-2 border-gray-100 dark:border-slate-800 pl-4 py-2">
                                                @foreach($group->churches as $church)
                                                    @if($church->first_timers_count > 0)
                                                        <div x-data="{ expanded: {{ $search ? 'true' : 'false' }} }">
                                                            <button @click="expanded = !expanded"
                                                                class="flex items-center gap-2 mb-2 text-xs font-semibold text-indigo-600 dark:text-indigo-400">
                                                                <svg class="w-3 h-3 transition-transform" :class="expanded ? 'rotate-90' : ''"
                                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M9 5l7 7-7 7" />
                                                                </svg>
                                                                {{ $church->name }}
                                                                <span
                                                                    class="text-[10px] text-gray-400 font-normal">({{ $church->first_timers_count }})</span>
                                                            </button>

                                                            <div x-show="expanded" x-collapse class="space-y-1">
                                                                @foreach($church->firstTimers as $ft)
                                                                    <div
                                                                        class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 group/item transition-colors">
                                                                        <div class="flex items-center gap-3">
                                                                            <div
                                                                                class="w-2 h-2 rounded-full {{ $ft->status === 'Member' ? 'bg-emerald-500' : ($ft->status === 'In Progress' ? 'bg-blue-500' : 'bg-amber-500') }}">
                                                                            </div>
                                                                            <span
                                                                                class="text-xs font-medium text-gray-900 dark:text-slate-200">{{ $ft->full_name }}</span>
                                                                            <span
                                                                                class="text-[10px] items-center px-1.5 py-0.5 rounded-full font-medium {{ $ft->status === 'New' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-500' : ($ft->status === 'In Progress' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400') }}">
                                                                                {{ $ft->status }}
                                                                            </span>
                                                                        </div>
                                                                        <a href="{{ route('admin.foundation-school.show', $ft) }}"
                                                                            class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline uppercase tracking-wider transition-all opacity-0 group-hover/item:opacity-100">
                                                                            View Progress
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-gray-400 dark:text-slate-600">
                        <svg class="w-12 h-12 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p class="text-sm">No first timers found matching your criteria.</p>
                        @if($search)
                            <a href="{{ route('admin.foundation-school.index') }}"
                                class="text-indigo-600 dark:text-indigo-400 text-xs font-bold mt-2 inline-block">Clear Search</a>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection