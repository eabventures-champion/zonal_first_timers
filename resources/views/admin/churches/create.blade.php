@extends('layouts.app')
@section('title', 'Create Church')
@section('page-title', 'Create Church')
@section('back-link', route('admin.churches.index'))

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
            <form method="POST" action="{{ route('admin.churches.store') }}">
                @csrf

                <div class="mb-5">
                    <label for="church_group_id"
                        class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Group <span
                            class="text-red-500">*</span></label>
                    <select name="church_group_id" id="church_group_id" required
                        class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select a group</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('church_group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->category->name ?? '' }} → {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('church_group_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div x-data="{ 
                        entries: {{ old('churches') ? Js::from(old('churches')) : '[{ name: \'\', leader_name: \'\', leader_contact: \'\' }]' }},
                        contactErrors: {},
                        contactChecking: {},
                        addEntry() {
                            this.entries.push({ name: '', leader_name: '', leader_contact: '' });
                        },
                        removeEntry(index) {
                            if (this.entries.length > 1) {
                                this.entries.splice(index, 1);
                                delete this.contactErrors[index];
                                delete this.contactChecking[index];
                            }
                        },
                        async checkLeaderContact(index, contact) {
                            if (!contact || contact.length < 3) {
                                this.contactErrors[index] = '';
                                return;
                            }
                            this.contactChecking[index] = true;
                            try {
                                const response = await fetch('{{ route('admin.churches.check-leader-contact') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                    },
                                    body: JSON.stringify({ contact })
                                });
                                const data = await response.json();
                                this.contactErrors[index] = data.exists ? data.message : '';
                            } catch (e) {
                                this.contactErrors[index] = '';
                            }
                            this.contactChecking[index] = false;
                        }
                    }" class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-gray-800 dark:text-slate-200">Church Details</h3>
                        </div>
                        <button type="button" @click="addEntry()"
                            class="inline-flex items-center px-3 py-1.5 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 text-xs font-bold rounded-lg transition-colors border border-indigo-100 dark:border-indigo-500/20">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                            Add Another Church
                        </button>
                    </div>

                    <div class="space-y-6">
                        <template x-for="(entry, index) in entries" :key="index">
                            <div
                                class="p-5 bg-gray-50/50 dark:bg-slate-800/40 rounded-xl border border-gray-100 dark:border-slate-800/60 relative group/entry">
                                <button type="button" @click="removeEntry(index)" x-show="entries.length > 1"
                                    class="absolute -top-2 -right-2 p-1.5 bg-white dark:bg-slate-800 border border-red-100 dark:border-red-900/30 text-red-500 rounded-full shadow-sm hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors z-10 opacity-0 group-hover/entry:opacity-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>

                                <div class="mb-4">
                                    <label :for="'name_' + index"
                                        class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Church Name
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" :name="'churches[' + index + '][name]'" :id="'name_' + index"
                                        x-model="entry.name" required
                                        class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label :for="'leader_name_' + index"
                                            class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Name of
                                            leader</label>
                                        <input type="text" :name="'churches[' + index + '][leader_name]'"
                                            :id="'leader_name_' + index" x-model="entry.leader_name"
                                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <div>
                                        <label :for="'leader_contact_' + index"
                                            class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Contact
                                            of leader</label>
                                        <input type="text" :name="'churches[' + index + '][leader_contact]'"
                                            :id="'leader_contact_' + index" x-model="entry.leader_contact"
                                            x-on:input.debounce.500ms="checkLeaderContact(index, $el.value)"
                                            :class="contactErrors[index] ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-slate-700 focus:border-indigo-500 focus:ring-indigo-500'"
                                            class="w-full rounded-lg dark:bg-slate-800 dark:text-white shadow-sm text-sm">
                                        <p x-show="contactErrors[index]" x-text="contactErrors[index]"
                                            class="mt-1 text-xs text-red-600" x-cloak></p>
                                        <p x-show="contactChecking[index]" class="mt-1 text-xs text-gray-400" x-cloak>
                                            Checking...</p>
                                    </div>
                                </div>

                                {{-- Display validation errors for current index --}}
                                <div class="mt-2">
                                    @foreach($errors->all() as $error)
                                        @php
                                            preg_match('/churches\.(\d+)/', $error, $matches);
                                            $errIndex = $matches[1] ?? null;
                                        @endphp
                                        <template x-if="index == {{ $errIndex ?? -1 }}">
                                            <p class="text-xs text-red-600">{{ $error }}</p>
                                        </template>
                                    @endforeach
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mb-6" x-data="{ 
                            open: false, 
                            selectedId: '{{ old('retaining_officer_id') }}',
                            selectedName: '{{ old('retaining_officer_id') ? ($officers->firstWhere('id', old('retaining_officer_id'))->name ?? 'None') : 'None' }}',
                            officers: {{ Js::from($officers->map(fn($o) => [
        'id' => $o->id,
        'name' => $o->name,
        'church' => $o->church->name ?? null
    ])) }}
                        }">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Retaining Officer
                        (Shared for all entries)</label>
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
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Create
                        Churches</button>
                    <a href="{{ route('admin.churches.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-slate-400 dark:hover:text-slate-300">Cancel</a>
                </div>
        </div>
        </form>
    </div>
    </div>
@endsection