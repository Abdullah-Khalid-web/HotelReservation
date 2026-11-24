<x-guest-layout>
    @section('title', 'Register — ' . config('app.name'))

    {{-- Heading --}}
    <div class="mb-8">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Create your account ✨</h2>
        <p class="text-sm text-gray-500 mt-2">
            Join in seconds — book tables and pre-order food with ease.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full name')"
                           class="text-sm font-semibold text-gray-700" />
            <x-text-input id="name"
                          class="block mt-1.5 w-full rounded-xl border-gray-300
                                 focus:border-indigo-500 focus:ring-indigo-500
                                 px-4 py-2.5 text-sm"
                          type="text"
                          name="name"
                          :value="old('name')"
                          required autofocus autocomplete="name"
                          placeholder="John Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

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
                          required autocomplete="username"
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
                          required autocomplete="new-password"
                          placeholder="At least 8 characters" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm password')"
                           class="text-sm font-semibold text-gray-700" />
            <x-text-input id="password_confirmation"
                          class="block mt-1.5 w-full rounded-xl border-gray-300
                                 focus:border-indigo-500 focus:ring-indigo-500
                                 px-4 py-2.5 text-sm"
                          type="password"
                          name="password_confirmation"
                          required autocomplete="new-password"
                          placeholder="Re-enter password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit -->
        <button type="submit"
                class="w-full mt-2 py-3 rounded-xl
                       bg-gradient-to-r from-indigo-600 to-violet-600
                       text-white font-semibold text-sm
                       shadow-lg shadow-indigo-500/30
                       hover:shadow-xl hover:shadow-indigo-500/40
                       active:scale-[0.98]
                       transition-all duration-150">
            {{ __('Create account') }} →
        </button>

        <p class="text-xs text-gray-400 text-center pt-1">
            By signing up you agree to our
            <a href="#" class="text-indigo-600 hover:underline">Terms</a>
            and
            <a href="#" class="text-indigo-600 hover:underline">Privacy Policy</a>.
        </p>
    </form>

    {{-- Divider --}}
    <div class="mt-8 relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-xs">
            <span class="px-3 bg-white text-gray-400 uppercase tracking-wider">
                Already have an account?
            </span>
        </div>
    </div>

    {{-- Login link --}}
    <div class="mt-6 text-center">
        <a href="{{ route('login') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
            Log in instead
            <span class="text-xs">→</span>
        </a>
    </div>
</x-guest-layout>
