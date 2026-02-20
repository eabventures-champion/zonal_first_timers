@extends('layouts.app')
@section('title', 'Register First Timer')
@section('page-title', 'Register First Timer')

@section('content')
    <div class="max-w-3xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('admin.first-timers.store') }}"
                x-data="{
                    categories: {{ Js::from($categories) }},
                    selectedCategory: '',
                    selectedGroup: '',
                    selectedChurch: '',
                    groups: [],
                    churches: [],
                    selectedOfficer: '',
                    
                    // Contact Validation
                    primaryContact: '{{ old('primary_contact') }}',
                    contactError: '',
                    isValidating: false,

                    updateGroups() {
                        const category = this.categories.find(c => c.id == this.selectedCategory);
                        this.groups = category ? category.groups : [];
                        this.selectedGroup = '';
                        this.churches = [];
                        this.selectedChurch = '';
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
                    }
                }">
                @csrf

                <h3 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b">Personal Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" required
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('full_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Primary Contact <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="primary_contact" required x-model="primaryContact"
                            @input.debounce.500ms="checkContact()"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            :class="contactError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''">
                        <p x-show="contactError" x-text="contactError" class="mt-1 text-xs text-red-600" style="display: none;"></p>
                        @error('primary_contact') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alternate Contact</label>
                        <input type="text" name="alternate_contact" value="{{ old('alternate_contact') }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender <span
                                class="text-red-500">*</span></label>
                        <select name="gender" required
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select</option>
                            <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth (Day & Month)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <select name="dob_day"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Day</option>
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" 
                                        {{ old('dob_day') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                            <select name="dob_month"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Marital Status</label>
                        <select name="marital_status"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select</option>
                            @foreach(['Single', 'Married', 'Divorced', 'Widowed'] as $ms)
                                <option value="{{ $ms }}" {{ old('marital_status') === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Occupation</label>
                        <input type="text" name="occupation" value="{{ old('occupation') }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Residential Address <span
                            class="text-red-500">*</span></label>
                    <textarea name="residential_address" rows="2" required
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('residential_address') }}</textarea>
                    @error('residential_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <h3 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b">Church & Visit Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                        <select x-model="selectedCategory" @change="updateGroups()"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Category</option>
                            <template x-for="category in categories" :key="category.id">
                                <option :value="category.id" x-text="category.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Group <span class="text-red-500">*</span></label>
                        <select x-model="selectedGroup" @change="updateChurches()" :disabled="!selectedCategory"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-400">
                            <option value="">Select Group</option>
                            <template x-for="group in groups" :key="group.id">
                                <option :value="group.id" x-text="group.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Church <span class="text-red-500">*</span></label>
                        <select name="church_id" x-model="selectedChurch" required :disabled="!selectedGroup" @change="updateOfficer()"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-400">
                            <option value="">Select Church</option>
                            <template x-for="church in churches" :key="church.id">
                                <option :value="church.id" x-text="church.name"></option>
                            </template>
                        </select>
                        @error('church_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Visit <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="date_of_visit" value="{{ old('date_of_visit', date('Y-m-d')) }}" required
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('date_of_visit') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Church Event</label>
                        <input type="text" name="church_event" value="{{ old('church_event') }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="e.g., Sunday Service">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Retaining Officer</label>
                        <select name="retaining_officer_id" x-model="selectedOfficer" :disabled="!selectedChurch"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-400">
                            <option value="">Auto-assign from church</option>
                            @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" {{ old('retaining_officer_id') == $officer->id ? 'selected' : '' }}>{{ $officer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <h3 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b">Who Brought Them / Credentials</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bringer Name</label>
                        <input type="text" name="bringer_name" value="{{ old('bringer_name') }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bringer Contact</label>
                        <input type="text" name="bringer_contact" value="{{ old('bringer_contact') }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bringer Fellowship</label>
                        <input type="text" name="bringer_fellowship" value="{{ old('bringer_fellowship') }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prayer Requests</label>
                    <textarea name="prayer_requests" rows="3"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('prayer_requests') }}</textarea>
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