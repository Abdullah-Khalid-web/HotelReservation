@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50 py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="mb-6 flex items-center gap-4">
            <a href="{{ route('users.index') }}"
               class="flex items-center justify-center w-11 h-11 rounded-xl
                      bg-white border border-slate-200
                      text-slate-600 hover:text-indigo-600
                      hover:border-indigo-300 hover:bg-indigo-50
                      shadow-sm transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('users.index') }}" class="hover:text-indigo-600">Users</a>
                    <span>/</span>
                    <span class="text-slate-700">Edit</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Edit User</h1>
                <p class="text-sm text-slate-500 mt-1">Update {{ $user->name }}'s details.</p>
            </div>
        </div>


        {{-- ERRORS --}}
        @if($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8v4m0 4h.01M10.29 3.86l-7.82 14A2 2 0 004.21 21h15.58a2 2 0 001.74-3.14l-7.82-14a2 2 0 00-3.42 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-red-800">Please fix the following errors:</p>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif


        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                {{-- Header with avatar --}}
                <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full
                                bg-gradient-to-br from-indigo-500 to-violet-600
                                flex items-center justify-center
                                text-white font-bold text-lg
                                ring-2 ring-white shadow-md">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">{{ $user->name }}</h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Member since {{ $user->created_at->format('M d, Y') }}
                        </p>
                    </div>
                </div>

                <div class="p-5 space-y-4">

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Full Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                               value="{{ old('name', $user->name) }}"
                               required
                               class="w-full rounded-xl border border-slate-300 bg-white
                                      px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                      focus:border-indigo-500 focus:ring-2
                                      focus:ring-indigo-200 outline-none transition
                                      @error('name') border-red-400 @enderror">
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Email Address <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email', $user->email) }}"
                               required
                               class="w-full rounded-xl border border-slate-300 bg-white
                                      px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                      focus:border-indigo-500 focus:ring-2
                                      focus:ring-indigo-200 outline-none transition
                                      @error('email') border-red-400 @enderror">
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="rounded-xl bg-amber-50 border border-amber-100 p-4">
                        <p class="text-xs text-amber-800 mb-3 flex items-center gap-1.5">
                            <span>💡</span>
                            Leave password fields empty to keep the current password.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    New Password
                                </label>
                                <input type="password" id="password" name="password"
                                       class="w-full rounded-xl border border-slate-300 bg-white
                                              px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                              focus:border-indigo-500 focus:ring-2
                                              focus:ring-indigo-200 outline-none transition
                                              @error('password') border-red-400 @enderror">
                                @error('password')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation"
                                       class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Confirm New Password
                                </label>
                                <input type="password" id="password_confirmation"
                                       name="password_confirmation"
                                       class="w-full rounded-xl border border-slate-300 bg-white
                                              px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                              focus:border-indigo-500 focus:ring-2
                                              focus:ring-indigo-200 outline-none transition">
                            </div>
                        </div>
                    </div>

                    {{-- Role --}}
                    <div>
                        <label for="role" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Role <span class="text-rose-500">*</span>
                        </label>
                        @php $currentRole = $user->getRoleNames()->first() ?? ''; @endphp
                        <select id="role" name="role" required
                                class="w-full rounded-xl border border-slate-300 bg-white
                                       px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                       focus:border-indigo-500 focus:ring-2
                                       focus:ring-indigo-200 outline-none transition
                                       @error('role') border-red-400 @enderror">
                            @foreach($roles ?? [] as $role)
                                <option value="{{ $role->name }}"
                                    {{ old('role', $currentRole) === $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>


                {{-- Actions --}}
                <div class="px-5 py-4 bg-slate-50/70 border-t border-slate-100
                            flex flex-col sm:flex-row sm:justify-end gap-3">

                    <a href="{{ route('users.index') }}"
                       class="inline-flex items-center justify-center gap-2
                              px-5 py-2.5 rounded-xl bg-white
                              border border-slate-300 text-slate-700
                              text-sm font-medium
                              hover:bg-slate-50 hover:border-slate-400 transition">
                        Cancel
                    </a>

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2
                                   px-5 py-2.5 rounded-xl
                                   bg-gradient-to-r from-indigo-500 to-violet-600
                                   text-sm font-semibold text-white
                                   hover:shadow-lg hover:shadow-indigo-500/30
                                   active:scale-[0.98] transition shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update User
                    </button>

                </div>

            </div>

        </form>

    </div>
</div>

@endsection
