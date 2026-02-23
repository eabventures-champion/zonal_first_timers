<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-10 text-center">
        <h2 class="text-3xl font-bold text-white mb-2 tracking-tight">Welcome Back</h2>
        <p class="text-slate-400 text-sm">Access your ministerial dashboard</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Login (Email or Phone) -->
        <div>
            <label for="login" class="block text-sm font-semibold text-slate-300 mb-2 ms-1">Email or Phone
                Number</label>
            <div class="relative group">
                <div
                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-indigo-400 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <input id="login"
                    class="block w-full pl-11 pr-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all"
                    type="text" name="login" :value="old('login')" required autofocus
                    placeholder="Enter your credentials" />
            </div>
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-2 ms-1">
                <label for="password" class="block text-sm font-semibold text-slate-300">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition-colors"
                        href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="relative group">
                <div
                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-indigo-400 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input id="password"
                    class="block w-full pl-11 pr-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all"
                    type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <div class="relative">
                    <input id="remember_me" type="checkbox" class="sr-only" name="remember">
                    <div
                        class="w-10 h-5 bg-white/10 rounded-full shadow-inner border border-white/5 group-hover:bg-white/20 transition-colors">
                    </div>
                    <div
                        class="dot absolute left-1 top-1 w-3 h-3 bg-slate-400 rounded-full transition-all group-has-[:checked]:left-6 group-has-[:checked]:bg-indigo-500">
                    </div>
                </div>
                <span class="ms-3 text-sm font-medium text-slate-400 group-hover:text-slate-300 transition-colors">Stay
                    signed in</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-xl shadow-indigo-500/30 transition-all hover:scale-[1.02] focus:ring-4 focus:ring-indigo-500/50">
                Log in
            </button>
        </div>
    </form>

    <style>
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #0f172a inset !important;
            -webkit-text-fill-color: white !important;
        }
    </style>
</x-guest-layout>