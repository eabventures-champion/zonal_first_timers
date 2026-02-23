@extends('layouts.app')
@section('title', 'Register First Timer')
@section('page-title', 'Register First Timer')
@section('back-link', route('admin.first-timers.index'))

@section('content')
    <div class="max-w-3xl">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
            <form method="POST" action="{{ route('admin.first-timers.store') }}"
                x-data="{
                    categories: {{ Js::from($categories) }},
                    selectedCategory: '',
                    selectedGroup: '',
                    selectedChurch: '',
                    groups: [],
                    churches: [],
                    bringers: [],
                    selectedBringerId: '',
                    selectedOfficer: '',
                    
                    // Bringer Contact Validation
                    bringerContact: '{{ old('bringer_contact') }}',
                    bringerContactError: '',

                    updateGroups() {
                        const category = this.categories.find(c => c.id == this.selectedCategory);
                        this.groups = category ? category.groups : [];
                        this.selectedGroup = '';
                        this.churches = [];
                        this.selectedChurch = '';
                        this.broughtBies = [];
                        this.selectedBringerId = '';
                        this.selectedOfficer = '';
                    },
                    
                    updateChurches() {
                        const group = this.groups.find(g => g.id == this.selectedGroup);
                        this.churches = group ? group.churches : [];
                        this.selectedChurch = '';
                        this.selectedOfficer = '';
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

                    async checkContact() {
                        if (this.primaryContact.length < 5) {
                            this.contactError = '';
                            return;
                        }
                        
                        this.isValidating = true;
                        try {
                            const response = await fetch('{{ route('admin.first-timers.check-contact') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ contact: this.primaryContact })
                            });
                            const data = await response.json();
                            this.contactError = data.exists ? data.message : '';
                        } catch (e) {
                            console.error('Validation failed', e);
                        } finally {
                            this.isValidating = false;
                        }
                    },

                    async checkBringerContact() {
                        if (this.bringerContact.length < 5) {
                            this.bringerContactError = '';
                            return;
                        }
                        
                        try {
                            const response = await fetch('{{ route('admin.bringers.check-contact') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ contact: this.bringerContact })
                            });
                            const data = await response.json();
                            this.bringerContactError = data.exists ? data.message : '';
                        } catch (e) {
                            console.error('Validation failed', e);
                        }
                    }
                }">
                @csrf

                <div class="flex items-center gap-2 mb-4 pb-2 border-b dark:border-slate-800">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-slate-200">Personal Information</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Full Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" required
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('full_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Primary Contact <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="primary_contact" required x-model="primaryContact"
                            @input.debounce.500ms="checkContact()"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            :class="contactError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''">
                        <p x-show="contactError" x-text="contactError" class="mt-1 text-xs text-red-600" style="display: none;"></p>
                        @error('primary_contact') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Alternate Contact</label>
                        <input type="text" name="alternate_contact" value="{{ old('alternate_contact') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Gender <span
                                class="text-red-500">*</span></label>
                        <select name="gender" required
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select</option>
                            <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Date of Birth (Day & Month)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <select name="dob_day"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Day</option>
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" 
                                        {{ old('dob_day') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                            <select name="dob_month"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Month</option>
                                @foreach (['01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('dob_month') == $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Marital Status</label>
                        <select name="marital_status"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select</option>
                            @foreach(['Single', 'Married', 'Divorced', 'Widowed'] as $ms)
                                <option value="{{ $ms }}" {{ old('marital_status') === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Occupation</label>
                        <input type="text" name="occupation" value="{{ old('occupation') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Residential Address <span
                            class="text-red-500">*</span></label>
                    <textarea name="residential_address" rows="2" required
                        class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('residential_address') }}</textarea>
                    @error('residential_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-2 mb-4 pb-2 border-b dark:border-slate-800">
                    <div class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-500/10 flex items-center justify-center text-teal-600 dark:text-teal-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-slate-200">Church & Visit Details</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Category <span class="text-red-500">*</span></label>
                        <select x-model="selectedCategory" @change="updateGroups()"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Category</option>
                            <template x-for="category in categories" :key="category.id">
                                <option :value="category.id" x-text="category.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Group <span class="text-red-500">*</span></label>
                        <select x-model="selectedGroup" @change="updateChurches()" :disabled="!selectedCategory"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-400 dark:disabled:bg-slate-800/10">
                            <option value="">Select Group</option>
                            <template x-for="group in groups" :key="group.id">
                                <option :value="group.id" x-text="group.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Church <span class="text-red-500">*</span></label>
                        <select name="church_id" x-model="selectedChurch" required :disabled="!selectedGroup" @change="updateOfficer()"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-400 dark:disabled:bg-slate-800/10">
                            <option value="">Select Church</option>
                            <template x-for="church in churches" :key="church.id">
                                <option :value="church.id" x-text="church.name"></option>
                            </template>
                        </select>
                        @error('church_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Date of Visit <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="date_of_visit" value="{{ old('date_of_visit', date('Y-m-d')) }}" required
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('date_of_visit') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Church Event</label>
                        <input type="text" name="church_event" value="{{ old('church_event') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="e.g., Sunday Service">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Retaining Officer</label>
                        <select name="retaining_officer_id" x-model="selectedOfficer" :disabled="!selectedChurch"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-400 dark:disabled:bg-slate-800/10">
                            <option value="">Auto-assign from church</option>
                            @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" {{ old('retaining_officer_id') == $officer->id ? 'selected' : '' }}>{{ $officer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-4 pb-2 border-b dark:border-slate-800">Who Brought Them / Credentials</h3>
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Select Person (Optional)</label>
                        <select name="bringer_id" x-model="selectedBringerId"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">New Person / Not Listed (Use fields below)</option>
                            <template x-for="person in bringers" :key="person.id">
                                <option :value="person.id" x-text="`${person.name} (${person.contact})${person.is_ro ? ' (RO)' : ''}`"></option>
                            </template>
                        </select>
                        <p class="mt-1 text-[10px] text-gray-400 dark:text-slate-500">If the person is already in the system, select them here. Otherwise, fill the details below.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="!selectedBringerId">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Bringer Name</label>
                            <input type="text" name="bringer_name" value="{{ old('bringer_name') }}"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Bringer Contact</label>
                            <input type="text" name="bringer_contact" x-model="bringerContact"
                                @input.debounce.500ms="checkBringerContact()"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                :class="bringerContactError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''">
                            <p x-show="bringerContactError" x-text="bringerContactError" class="mt-1 text-[10px] text-red-600" style="display: none;"></p>
                        </div>
                    </div>
                    <div x-show="!selectedBringerId && !selectedOfficer" class="p-2 bg-amber-50 dark:bg-amber-100/10 rounded-lg border border-amber-100 dark:border-amber-100/20">
                        <p class="text-[10px] text-amber-700 dark:text-amber-400 italic">If left empty, this first timer will be linked to the Retaining Officer ({{ $officers->find(old('retaining_officer_id'))?->name ?? 'assigned later' }}).</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="born_again" value="1" {{ old('born_again') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Born Again</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="water_baptism" value="1" {{ old('water_baptism') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Water Baptism</span>
                    </label>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Prayer Requests</label>
                    <textarea name="prayer_requests" rows="3"
                        class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('prayer_requests') }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Register
                        First Timer</button>
                    <a href="{{ route('admin.first-timers.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection