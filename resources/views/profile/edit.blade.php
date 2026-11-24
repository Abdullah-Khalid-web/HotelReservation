@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto">

    {{-- ═══════════ HEADER ═══════════ --}}
    <div class="mb-6 flex items-start gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Profile</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">My Profile</h1>
            <p class="text-sm text-slate-500 mt-1">
                Manage your account details and security.
            </p>
        </div>
    </div>


    {{-- ═══════════ FLASH ═══════════ --}}
    @if(session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3
                    text-sm text-emerald-800 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                 stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('status') === 'profile-updated')
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3
                    text-sm text-emerald-800">
            Profile updated.
        </div>
    @endif


    <div class="space-y-5">

        {{-- ═══════════ PROFILE HEADER CARD ═══════════ --}}
        <div class="bg-gradient-to-br from-indigo-50 to-violet-50
                    rounded-2xl border border-indigo-100 shadow-sm p-6">
            <div class="flex items-center gap-4">
                <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-violet-600
                            rounded-2xl flex items-center justify-center
                            text-white font-bold text-3xl shadow-lg
                            flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="text-xl font-bold text-slate-900 truncate">
                        {{ $user->name }}
                    </h2>
                    <p class="text-sm text-slate-600 truncate">{{ $user->email }}</p>
                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                        @foreach($user->getRoleNames() as $role)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md
                                         bg-indigo-100 text-indigo-700
                                         text-xs font-bold uppercase tracking-wider">
                                {{ $role }}
                            </span>
                        @endforeach
                        <span class="text-xs text-slate-500">
                            Member since {{ $user->created_at->format('M Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>


        {{-- ═══════════ SECTION 1: BASIC INFO ═══════════ --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-600
                            flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                         stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-slate-900">
                        Profile Information
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Update your name and email address.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="p-6 space-y-5">
                @csrf
                @method('PATCH')

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', $user->name) }}"
                           required
                           autocomplete="name"
                           class="w-full rounded-xl border border-slate-300 bg-white
                                  px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                  focus:border-indigo-500 focus:ring-2
                                  focus:ring-indigo-200 outline-none transition">
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email', $user->email) }}"
                           required
                           autocomplete="username"
                           class="w-full rounded-xl border border-slate-300 bg-white
                                  px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                  focus:border-indigo-500 focus:ring-2
                                  focus:ring-indigo-200 outline-none transition">
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                    @enderror

                    @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <p class="mt-2 text-xs text-amber-600 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                 stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Your email is not verified.
                        </p>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                                   bg-gradient-to-r from-indigo-500 to-violet-600
                                   text-sm font-semibold text-white
                                   hover:shadow-lg hover:shadow-indigo-500/30
                                   active:scale-[0.98] transition shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Changes
                    </button>
                </div>
            </form>

        </div>


        {{-- ═══════════ SECTION 2: PASSWORD ═══════════ --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-600
                            flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                         stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-slate-900">
                        Update Password
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Use a strong, unique password.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.password') }}" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                {{-- Current password --}}
                <div>
                    <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Current Password <span class="text-rose-500">*</span>
                    </label>
                    <input type="password"
                           id="current_password"
                           name="current_password"
                           required
                           autocomplete="current-password"
                           class="w-full rounded-xl border border-slate-300 bg-white
                                  px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                  focus:border-indigo-500 focus:ring-2
                                  focus:ring-indigo-200 outline-none transition">
                    @error('current_password', 'updatePassword')
                        <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New password --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        New Password <span class="text-rose-500">*</span>
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           autocomplete="new-password"
                           class="w-full rounded-xl border border-slate-300 bg-white
                                  px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                  focus:border-indigo-500 focus:ring-2
                                  focus:ring-indigo-200 outline-none transition">
                    @error('password', 'updatePassword')
                        <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-slate-500">
                        Minimum 8 characters.
                    </p>
                </div>

                {{-- Confirm --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Confirm New Password <span class="text-rose-500">*</span>
                    </label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           required
                           autocomplete="new-password"
                           class="w-full rounded-xl border border-slate-300 bg-white
                                  px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                  focus:border-indigo-500 focus:ring-2
                                  focus:ring-indigo-200 outline-none transition">
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                                   bg-slate-900 text-white text-sm font-semibold
                                   hover:bg-slate-800 active:scale-[0.98] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Update Password
                    </button>
                </div>
            </form>

        </div>


        {{-- ═══════════ SECTION 3: DELETE ACCOUNT ═══════════ --}}
        <div class="bg-white rounded-2xl border border-rose-200 shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b border-rose-100 flex items-center gap-3
                        bg-rose-50/50">
                <div class="w-9 h-9 rounded-lg bg-rose-100 text-rose-600
                            flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                         stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v2m0 4h.01M10.29 3.86l-7.82 14A2 2 0 004.21 21h15.58a2 2 0 001.74-3.14l-7.82-14a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-rose-900">
                        Delete Account
                    </h2>
                    <p class="text-xs text-rose-700 mt-0.5">
                        Permanently delete your account and all data. This cannot be undone.
                    </p>
                </div>
            </div>

            <div class="p-6">

                <button type="button"
                        onclick="document.getElementById('delete-account-modal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                               bg-rose-600 text-white text-sm font-semibold
                               hover:bg-rose-700 active:scale-[0.98] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                         stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete My Account
                </button>

            </div>

        </div>

    </div>

</div>


{{-- ═══════════ DELETE MODAL ═══════════ --}}
<div id="delete-account-modal"
     class="hidden fixed inset-0 z-[90] flex items-center justify-center p-4
            bg-black/50 backdrop-blur-sm">

    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden
                animate-in">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-slate-200 flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600
                        flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                     stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v2m0 4h.01M10.29 3.86l-7.82 14A2 2 0 004.21 21h15.58a2 2 0 001.74-3.14l-7.82-14a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-slate-900">
                    Delete account?
                </h3>
                <p class="text-sm text-slate-600 mt-0.5">
                    This action is permanent and cannot be undone. All your data will be removed.
                </p>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('profile.destroy') }}" class="p-6 space-y-4">
            @csrf
            @method('DELETE')

            <div>
                <label for="delete_password"
                       class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Confirm your password <span class="text-rose-500">*</span>
                </label>
                <input type="password"
                       id="delete_password"
                       name="password"
                       required
                       placeholder="Enter your password"
                       class="w-full rounded-xl border border-slate-300 bg-white
                              px-4 py-2.5 text-sm text-slate-800 shadow-sm
                              placeholder-slate-400
                              focus:border-rose-500 focus:ring-2
                              focus:ring-rose-200 outline-none transition">
                @error('password', 'userDeletion')
                    <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('delete-account-modal').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl
                               border border-slate-300 text-slate-700
                               text-sm font-semibold
                               hover:bg-slate-50 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                               bg-rose-600 text-white text-sm font-semibold
                               hover:bg-rose-700 active:scale-[0.98] transition">
                    Yes, Delete
                </button>
            </div>
        </form>

    </div>

</div>

@endsection
