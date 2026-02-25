@extends('layouts.app')
@section('title', 'Edit First Timer')
@section('page-title', 'Edit: ' . $firstTimer->full_name)
@section('back-link', route('admin.first-timers.show', $firstTimer))

@section('content')
    <div class="max-w-3xl">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
            <form method="POST" action="{{ route('admin.first-timers.update', $firstTimer) }}"
                @submit.prevent="if(contactError || alternateContactError || (!selectedBringerId && bringerContactError) || isValidating) { return; } $el.submit();"
                x-data="{
                    categories: {{ Js::from($categories) }},
                    selectedCategory: '{{ $firstTimer->church->group->category->id ?? '' }}',
                    selectedGroup: '{{ $firstTimer->church->group->id ?? '' }}',
                    selectedChurch: '{{ $firstTimer->church_id }}',
                    selectedOfficer: '{{ $firstTimer->retaining_officer_id }}',
                    fullName: '{{ old('full_name', $firstTimer->full_name) }}',
                    groups: [],
                    churches: [],
                    bringers: [],
                    selectedBringerId: '{{ $firstTimer->bringer_id }}',
                    
                    // Contact Validation
                    primaryContact: '{{ old('primary_contact', $firstTimer->primary_contact) }}',
                    contactError: '',
                    alternateContact: '{{ old('alternate_contact', $firstTimer->alternate_contact) }}',
                    alternateContactError: '',
                    isValidating: false,
                    bringerConfirmed: false,

                    canSubmit() {
                        const isCommonValid = this.fullName && this.fullName.trim() && this.primaryContact && this.primaryContact.trim() && this.selectedChurch && !this.contactError && !this.alternateContactError && !this.isValidating;
                        const isBringerValid = this.selectedBringerId || (this.bringerConfirmed && this.bringerName && this.bringerName.trim() && this.bringerContact && this.bringerContact.trim() && !this.bringerContactError);
                        return isCommonValid && isBringerValid;
                    },
                    excludeId: '{{ $firstTimer->id }}',
                    bringerName: '{{ old('bringer_name', $firstTimer->bringer_name) }}',

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

                    async checkContact() {
                        const phone = this.primaryContact.trim();
                        if (phone.length === 0) {
                            this.contactError = '';
                            return;
                        }
                        
                        // Length validation
                        if (phone.length !== 10) {
                            this.contactError = 'Phone number must be exactly 10 digits.';
                            return;
                        }

                        this.contactError = '';
                        this.isValidating = true;
                        try {
                            const response = await fetch('{{ route('admin.first-timers.check-contact') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ 
                                    contact: phone,
                                    exclude_id: this.excludeId,
                                    type: 'first_timer'
                                })
                            });
                            const data = await response.json();
                            this.contactError = data.exists ? data.message : '';
                        } catch (e) {
                            console.error('Validation failed', e);
                        } finally {
                            this.isValidating = false;
                        }
                    },

                    checkAlternateContact() {
                        const phone = this.alternateContact.trim();
                        if (phone.length === 0) {
                            this.alternateContactError = '';
                            return;
                        }
                        if (phone.length !== 10) {
                            this.alternateContactError = 'Phone number must be exactly 10 digits.';
                            return;
                        }
                        this.alternateContactError = '';
                    },

                    bringerContact: '{{ old('bringer_contact', $firstTimer->bringer_contact) }}',
                    bringerContactError: '',

                    async checkBringerContact() {
                        const phone = this.bringerContact.trim();
                        if (phone.length === 0) {
                            this.bringerContactError = '';
                            return;
                        }
                        if (phone.length !== 10) {
                            this.bringerContactError = 'Phone number must be exactly 10 digits.';
                            return;
                        }
                        
                        this.bringerContactError = '';
                        try {
                            const response = await fetch('{{ route('admin.bringers.check-contact') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ contact: phone })
                            });
                            const data = await response.json();
                            this.bringerContactError = data.exists ? data.message : '';
                        } catch (e) {
                            console.error('Validation failed', e);
                        }
                    }
                }">
                @csrf @method('PUT')

                <h3 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b">Personal Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Full Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="full_name" x-model="fullName" required
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('full_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $firstTimer->email) }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Primary Contact <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="primary_contact" required x-model="primaryContact"
                            @input.debounce.500ms="checkContact()" minlength="10" maxlength="20"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            :class="contactError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''">
                        <p x-show="contactError" x-text="contactError" class="mt-1 text-xs text-red-600" style="display: none;"></p>
                        @error('primary_contact') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Alternate Contact</label>
                        <input type="text" name="alternate_contact" x-model="alternateContact"
                            @input.debounce.500ms="checkAlternateContact()" minlength="10" maxlength="20"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            :class="alternateContactError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''">
                        <p x-show="alternateContactError" x-text="alternateContactError" class="mt-1 text-xs text-red-600" style="display: none;"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Gender <span
                                class="text-red-500">*</span></label>
                        <select name="gender" required
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="Male" {{ old('gender', $firstTimer->gender) === 'Male' ? 'selected' : '' }}>Male
                            </option>
                            <option value="Female" {{ old('gender', $firstTimer->gender) === 'Female' ? 'selected' : '' }}>
                                Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Date of Birth (Day & Month)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <select name="dob_day"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Day</option>
                                @for ($i = 1; $i <= 31; $i++)
                                    @php $dayVal = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $dayVal }}" 
                                        {{ old('dob_day', $firstTimer->date_of_birth?->format('d')) == $dayVal ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                            <select name="dob_month"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Month</option>
                                @foreach (['01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('dob_month', $firstTimer->date_of_birth?->format('m')) == $val ? 'selected' : '' }}>
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
                                <option value="{{ $ms }}" {{ old('marital_status', $firstTimer->marital_status) === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Occupation</label>
                        <input type="text" name="occupation" value="{{ old('occupation', $firstTimer->occupation) }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Residential Address <span
                            class="text-red-500">*</span></label>
                    <textarea name="residential_address" rows="2" required
                        class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('residential_address', $firstTimer->residential_address) }}</textarea>
                </div>

                <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-4 pb-2 border-b dark:border-slate-800">Church & Visit Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Category <span class="text-red-500">*</span></label>
                        <select x-model="selectedCategory" @change="updateGroups()"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Category</option>
                            <template x-for="category in categories" :key="category.id">
                                <option :value="category.id" x-text="category.name" :selected="category.id == selectedCategory"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Group <span class="text-red-500">*</span></label>
                        <select x-model="selectedGroup" @change="updateChurches()" :disabled="!selectedCategory"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-400 dark:disabled:bg-slate-800/50">
                            <option value="">Select Group</option>
                            <template x-for="group in groups" :key="group.id">
                                <option :value="group.id" x-text="group.name" :selected="group.id == selectedGroup"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Church <span class="text-red-500">*</span></label>
                        <select name="church_id" x-model="selectedChurch" required :disabled="!selectedGroup" @change="updateOfficer()"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-400 dark:disabled:bg-slate-800/50">
                            <option value="">Select Church</option>
                            <template x-for="church in churches" :key="church.id">
                                <option :value="church.id" x-text="church.name" :selected="church.id == selectedChurch"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Date of Visit <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="date_of_visit"
                            value="{{ old('date_of_visit', $firstTimer->date_of_visit?->format('Y-m-d')) }}" required
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Church Event</label>
                        <input type="text" name="church_event" value="{{ old('church_event', $firstTimer->church_event) }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Retaining Officer</label>
                        <select name="retaining_officer_id" x-model="selectedOfficer" :disabled="!selectedChurch"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-400 dark:disabled:bg-slate-800/50">
                            <option value="">Auto-assign</option>
                            @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" {{ old('retaining_officer_id', $firstTimer->retaining_officer_id) == $officer->id ? 'selected' : '' }}>{{ $officer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-4 pb-2 border-b dark:border-slate-800">Invited By</h3>
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Select Person</label>
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Bringer
                                Name</label>
                            <input type="text" name="bringer_name" x-model="bringerName"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Bringer
                                Contact</label>
                            <input type="text" name="bringer_contact" x-model="bringerContact"
                                @input.debounce.500ms="checkBringerContact()" minlength="10" maxlength="20"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                :class="bringerContactError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''">
                            <p x-show="bringerContactError" x-text="bringerContactError" class="mt-1 text-[10px] text-red-600" style="display: none;"></p>
                            
                            <div x-show="!selectedBringerId && bringerName.trim() && bringerContact.trim() && !bringerContactError" style="display: none;" class="mt-2">
                                <button type="button" @click="bringerConfirmed = true" x-show="!bringerConfirmed"
                                    class="text-[10px] px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition">
                                    Use this Bringer
                                </button>
                                <p x-show="bringerConfirmed" 
                                   class="text-[10px] text-green-600 flex items-center gap-1">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Bringer confirmed and ready.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div x-show="!selectedBringerId && !selectedOfficer" class="p-2 bg-amber-50 dark:bg-amber-100/10 rounded-lg border border-amber-100 dark:border-amber-100/20">
                        <p class="text-[10px] text-amber-700 dark:text-amber-400 italic">If left empty, this first timer will be linked to the Retaining Officer ({{ $officers->find(old('retaining_officer_id', $firstTimer->retaining_officer_id))?->name ?? 'assigned later' }}).</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="born_again" value="1" {{ old('born_again', $firstTimer->born_again) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-slate-700 dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">Born Again</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="water_baptism" value="1" {{ old('water_baptism', $firstTimer->water_baptism) ? 'checked' : '' }}
                            class="rounded border-gray-300 dark:border-slate-700 dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">Water Baptism</span>
                    </label>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Prayer Requests</label>
                    <textarea name="prayer_requests" rows="3"
                        class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('prayer_requests', $firstTimer->prayer_requests) }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" 
                        :disabled="!canSubmit()"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!isValidating">Update First Timer</span>
                        <span x-show="isValidating">Validating...</span>
                    </button>
                    <a href="{{ route('admin.first-timers.index') }}"
                        class="text-sm text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection