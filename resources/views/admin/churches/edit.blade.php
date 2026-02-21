@extends('layouts.app')
@section('title', 'Edit Church')
@section('page-title', 'Edit Church')
@section('back-link', route('admin.churches.index'))

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('admin.churches.update', $church) }}">
                @csrf @method('PUT')

                <div class="mb-5">
                    <label for="church_group_id" class="block text-sm font-medium text-gray-700 mb-1">Group <span
                            class="text-red-500">*</span></label>
                    <select name="church_group_id" id="church_group_id" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('church_group_id', $church->church_group_id) == $group->id ? 'selected' : '' }}>
                                {{ $group->category->name ?? '' }} → {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('church_group_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Church Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $church->name) }}" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="leader_name" class="block text-sm font-medium text-gray-700 mb-1">Name of leader</label>
                        <input type="text" name="leader_name" id="leader_name"
                            value="{{ old('leader_name', $church->leader_name) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('leader_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="leader_contact" class="block text-sm font-medium text-gray-700 mb-1">Contact of
                            leader</label>
                        <input type="text" name="leader_contact" id="leader_contact"
                            value="{{ old('leader_contact', $church->leader_contact) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('leader_contact') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>


                <div class="mb-6" x-data="{ 
                        open: false, 
                        selectedId: '{{ old('retaining_officer_id', $church->retaining_officer_id) }}',
                        selectedName: '{{ old('retaining_officer_id', $church->retaining_officer_id) ? ($officers->firstWhere('id', old('retaining_officer_id', $church->retaining_officer_id))->name ?? 'None') : 'None' }}',
                        officers: {{ Js::from($officers->map(fn($o) => [
        'id' => $o->id,
        'name' => $o->name,
        'church' => $o->church->name ?? null
    ])) }}
                    }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Retaining Officer</label>
                    <input type="hidden" name="retaining_officer_id" :value="selectedId">

                    <div class="relative">
                        <button type="button" @click="open = !open" @click.away="open = false"
                            class="relative w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-800 rounded-lg shadow-sm pl-3 pr-10 py-2.5 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all duration-200">
                            <span class="block truncate text-gray-900 dark:text-white" x-text="selectedName"></span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>

                        <div x-show="open" x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="absolute z-10 mt-1 w-full bg-white dark:bg-slate-900 shadow-xl max-h-60 rounded-xl py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm border border-gray-100 dark:border-slate-800 custom-scrollbar"
                            x-cloak>
                            <div class="cursor-pointer select-none relative py-2.5 pl-3 pr-9 border-b border-gray-50 dark:border-slate-800/50 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors"
                                @click="selectedId = ''; selectedName = 'None'; open = false">
                                <span class="font-normal block truncate text-gray-500 italic">None</span>
                            </div>
                            <template x-for="officer in officers" :key="officer.id">
                                <div class="cursor-pointer select-none relative py-2.5 pl-3 pr-4 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-colors group"
                                    @click="selectedId = officer.id; selectedName = officer.name; open = false">
                                    <div class="flex items-center justify-between gap-3">
                                        <span
                                            class="font-medium block truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 text-gray-900 dark:text-white"
                                            x-text="officer.name"></span>
                                        <template x-if="officer.church">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 whitespace-nowrap"
                                                x-text="officer.church"></span>
                                        </template>
                                    </div>
                                    <span x-show="selectedId == officer.id"
                                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Update
                        Church</button>
                    <a href="{{ route('admin.churches.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
        </div>
        </form>
    </div>
    </div>
@endsection