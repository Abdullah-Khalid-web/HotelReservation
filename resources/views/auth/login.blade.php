<x-guest-layout>
    @section('title', 'Login — ' . config('app.name'))

    {{-- Heading --}}
    <div class="mb-8">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Welcome back 👋</h2>
        <p class="text-sm text-gray-500 mt-2">
            Log in to manage your reservations and pre-orders.
        </p>
    </div>

    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')"
                           class="text-sm font-semibold text-gray-700" />
            <x-text-input id="email"
                          class="block mt-1.5 w-full rounded-xl border-gray-300
                                 focus:border-indigo-500 focus:ring-indigo-500
                                 px-4 py-2.5 text-sm"
                          type="email"
                          name="email"
                          :value="old('email')"
                          required autofocus autocomplete="username"
                          placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')"
                           class="text-sm font-semibold text-gray-700" />
            <x-text-input id="password"
                          class="block mt-1.5 w-full rounded-xl border-gray-300
                                 focus:border-indigo-500 focus:ring-indigo-500
                                 px-4 py-2.5 text-sm"
                          type="password"
                          name="password"
                          required autocomplete="current-password"
                          placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember + Forgot -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me"
                       type="checkbox"
                       name="remember"
                       class="rounded border-gray-300 text-indigo-600
                              shadow-sm focus:ring-indigo-500">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Submit -->
        <button type="submit"
                class="w-full py-3 rounded-xl
                       bg-gradient-to-r from-indigo-600 to-violet-600
                       text-white font-semibold text-sm
                       shadow-lg shadow-indigo-500/30
                       hover:shadow-xl hover:shadow-indigo-500/40
                       active:scale-[0.98]
                       transition-all duration-150">
            {{ __('Log in') }} →
        </button>
    </form>

    {{-- Divider --}}
    <div class="mt-8 relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-xs">
            <span class="px-3 bg-white text-gray-400 uppercase tracking-wider">
                New here?
            </span>
        </div>
    </div>

    {{-- Register link --}}
    <div class="mt-6 text-center">
        <a href="{{ route('register') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
            Create an account
            <span class="text-xs">→</span>
        </a>
    </div>
</x-guest-layout>
