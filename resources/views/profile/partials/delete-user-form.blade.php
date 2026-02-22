<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 border-b pb-2">
            {{ __('Delete Account') }}
        </h2>

        @if(auth()->user()->isDeletionPending())
            <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-amber-800">Deletion Request Pending</p>
                        <p class="text-xs text-amber-700">Requested on
                            {{ auth()->user()->deletion_requested_at->format('M d, Y') }}. Awaiting Super Admin approval.
                        </p>
                    </div>
                </div>
                <form action="{{ route('profile.cancel-deletion') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
                        Cancel Request
                    </button>
                </form>
            </div>
        @else
            <p class="mt-1 text-sm text-gray-600">
                {{ __('Account deletion requires Super Admin approval. Once requested, your account will be marked for deletion and permanently removed after approval.') }}
            </p>

            <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="mt-4">{{ __('Request Account Deletion') }}</x-danger-button>
        @endif
    </header>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Are you sure you want to request account deletion?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Once approved by a Super Admin, your account and all associated data will be permanently deleted. Please enter your password to confirm.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input id="password" name="password" type="password" class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}" />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Confirm Request') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>