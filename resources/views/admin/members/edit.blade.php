@extends('layouts.app')
@section('title', 'Edit Member')
@section('page-title', 'Edit: ' . $member->full_name)
@section('back-link', route('admin.members.show', $member))

@section('content')
    <div class="max-w-3xl">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
            <form method="POST" action="{{ route('admin.members.update', $member) }}" x-data="{
                        categories: {{ Js::from($categories) }},
                        selectedCategory: '{{ $member->church->group->category->id ?? '' }}',
                        selectedGroup: '{{ $member->church->group->id ?? '' }}',
                        selectedChurch: '{{ $member->church_id }}',
                        selectedOfficer: '{{ $member->retaining_officer_id }}',
                        churches: [],
                        bringers: [],
                        selectedBringerId: '{{ $member->bringer_id }}',

                        init() {
                            this.updateGroups(true);
                        },

                        updateGroups(initial = false) {
                            const category = this.categories.find(c => c.id == this.selectedCategory);
                            this.groups = category ? category.groups : [];
                            if (!initial) {
                                this.selectedGroup = '';
                                this.churches = [];
                                this.selectedChurch = '';
                                this.selectedOfficer = '';
                            } else {
                                this.updateChurches(true);
                            }
                        },

                        updateChurches(initial = false) {
                            const group = this.groups.find(g => g.id == this.selectedGroup);
                            this.churches = group ? group.churches : [];
                            if (!initial) {
                                this.selectedChurch = '';
                                this.selectedOfficer = '';
                            } else {
                                this.loadBringers();
                            }
                        },

                        updateOfficer() {
                            const church = this.churches.find(c => c.id == this.selectedChurch);
                            if (church && church.retaining_officer_id) {
                                this.selectedOfficer = church.retaining_officer_id;
                            } else {
                                this.selectedOfficer = '';
                            }
                            this.loadBringers();
                        },

                        async loadBringers() {
                            if (!this.selectedChurch) {
                                this.bringers = [];
                                return;
                            }
                            try {
                                const response = await fetch(`/admin/bringers/church/${this.selectedChurch}`);
                                this.bringers = await response.json();
                            } catch (e) {
                                console.error('Failed to load bringers', e);
                            }
                        },
                    }">
                @csrf @method('PUT')

                <div class="flex items-center gap-2 mb-4 pb-2 border-b dark:border-slate-800">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-slate-200">Personal Information</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Full Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name', $member->full_name) }}" required
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('full_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $member->email) }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Primary Contact
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="primary_contact"
                            value="{{ old('primary_contact', $member->primary_contact) }}" required
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('primary_contact') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Alternate
                            Contact</label>
                        <input type="text" name="alternate_contact"
                            value="{{ old('alternate_contact', $member->alternate_contact) }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Gender <span
                                class="text-red-500">*</span></label>
                        <select name="gender" required
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="Male" {{ old('gender', $member->gender) === 'Male' ? 'selected' : '' }}>Male
                            </option>
                            <option value="Female" {{ old('gender', $member->gender) === 'Female' ? 'selected' : '' }}>Female
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Date of Birth (Day & Month)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <select name="dob_day"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Day</option>
                                @for ($i = 1; $i <= 31; $i++)
                                    @php $dayVal = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $dayVal }}"
                                        {{ old('dob_day', $member->date_of_birth?->format('d')) == $dayVal ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                            <select name="dob_month"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Month</option>
                                @foreach (['01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('dob_month', $member->date_of_birth?->format('m')) == $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Marital
                            Status</label>
                        <select name="marital_status"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select</option>
                            @foreach(['Single', 'Married', 'Divorced', 'Widowed'] as $ms)
                                <option value="{{ $ms }}" {{ old('marital_status', $member->marital_status) === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Occupation</label>
                        <input type="text" name="occupation" value="{{ old('occupation', $member->occupation) }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Residential
                        Address</label>
                    <textarea name="residential_address" rows="2"
                        class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('residential_address', $member->residential_address) }}</textarea>
                </div>

                <div class="flex items-center gap-2 mb-4 pb-2 border-b dark:border-slate-800">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-slate-200">Who Brought Them / Credentials</h3>
                </div>
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Select Person (Optional)</label>
                        <select name="bringer_id" x-model="selectedBringerId"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">New Person / Not Listed (Use fields below)</option>
                            <template x-for="person in bringers" :key="person.id">
                                <option :value="person.id" x-text="`${person.name} (${person.contact})${person.is_ro ? ' (RO)' : ''}`" :selected="person.id == selectedBringerId"></option>
                            </template>
                        </select>
                        <p class="mt-1 text-[10px] text-gray-400 dark:text-slate-500">If the person is already in the system, select them here. Otherwise, fill the details below.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="!selectedBringerId">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Bringer Name</label>
                            <input type="text" name="bringer_name" value="{{ old('bringer_name', $member->bringer_name) }}"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Bringer Contact</label>
                            <input type="text" name="bringer_contact" value="{{ old('bringer_contact', $member->bringer_contact) }}"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="born_again" value="1" {{ old('born_again', $member->born_again) ? 'checked' : '' }}
                            class="rounded border-gray-300 dark:border-slate-700 dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">Born Again</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="water_baptism" value="1" {{ old('water_baptism', $member->water_baptism) ? 'checked' : '' }}
                            class="rounded border-gray-300 dark:border-slate-700 dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">Water Baptism</span>
                    </label>
                </div>

                <div class="flex items-center gap-2 mb-4 pb-2 border-b dark:border-slate-800">
                    <div class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-500/10 flex items-center justify-center text-teal-600 dark:text-teal-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-slate-200">Church Assignment</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Category <span
                                class="text-red-500">*</span></label>
                        <select x-model="selectedCategory" @change="updateGroups()"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Category</option>
                            <template x-for="category in categories" :key="category.id">
                                <option :value="category.id" x-text="category.name"
                                    :selected="category.id == selectedCategory"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Group <span
                                class="text-red-500">*</span></label>
                        <select x-model="selectedGroup" @change="updateChurches()" :disabled="!selectedCategory"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50">
                            <option value="">Select Group</option>
                            <template x-for="group in groups" :key="group.id">
                                <option :value="group.id" x-text="group.name" :selected="group.id == selectedGroup">
                                </option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Church <span
                                class="text-red-500">*</span></label>
                        <select name="church_id" x-model="selectedChurch" required :disabled="!selectedGroup"
                            @change="updateOfficer()"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50">
                            <option value="">Select Church</option>
                            <template x-for="church in churches" :key="church.id">
                                <option :value="church.id" x-text="church.name" :selected="church.id == selectedChurch">
                                </option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Retaining
                            Officer</label>
                        <select name="retaining_officer_id" x-model="selectedOfficer" :disabled="!selectedChurch"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50">
                            <option value="">Auto-assign</option>
                            @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" {{ old('retaining_officer_id', $member->retaining_officer_id) == $officer->id ? 'selected' : '' }}>{{ $officer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Update
                        Member</button>
                    <a href="{{ route('admin.members.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-slate-400 dark:hover:text-slate-300">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection