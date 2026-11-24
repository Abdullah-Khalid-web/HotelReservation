@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto">

    {{-- ============ PAGE HEADER ============ --}}
    <div class="mb-6 flex items-center gap-4">

        {{-- Back --}}
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
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition">
                    Dashboard
                </a>
                <span>/</span>
                <a href="{{ route('users.index') }}" class="hover:text-indigo-600 transition">
                    Users
                </a>
                <span>/</span>
                <span class="text-slate-700">Add New</span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                Add New User
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Create an account and assign a role.
            </p>

        </div>

    </div>


    {{-- ============ VALIDATION ERRORS ============ --}}
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
                    <p class="text-sm font-semibold text-red-800">
                        Please fix the following errors:
                    </p>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>

            </div>

        </div>

    @endif


    {{-- ============ FORM ============ --}}
    <form action="{{ route('users.store') }}"
          method="POST"
          x-data="{
                name: @js(old('name', '')),
                email: @js(old('email', '')),
                role: @js(old('role', 'guest')),
                password: ''
          }">

        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- ============ LEFT ============ --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Account Details --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">

                        <div class="flex items-center justify-center w-10 h-10 rounded-xl
                                    bg-indigo-100 text-indigo-600">

                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>

                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Account Details
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Basic information about the user.
                            </p>
                        </div>

                    </div>


                    <div class="p-5 space-y-4">

                        {{-- Name --}}
                        <div>
                            <label for="name"
                                   class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Full Name <span class="text-rose-500">*</span>
                            </label>

                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   x-model="name"
                                   placeholder="e.g. John Doe"
                                   required
                                   autofocus
                                   class="w-full rounded-xl border border-slate-300 bg-white
                                          px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                          placeholder-slate-400
                                          focus:border-indigo-500 focus:ring-2
                                          focus:ring-indigo-200 outline-none transition
                                          @error('name') border-red-400 @enderror">

                            @error('name')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>


                        {{-- Email --}}
                        <div>
                            <label for="email"
                                   class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Email Address <span class="text-rose-500">*</span>
                            </label>

                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   x-model="email"
                                   placeholder="name@example.com"
                                   required
                                   class="w-full rounded-xl border border-slate-300 bg-white
                                          px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                          placeholder-slate-400
                                          focus:border-indigo-500 focus:ring-2
                                          focus:ring-indigo-200 outline-none transition
                                          @error('email') border-red-400 @enderror">

                            @error('email')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>


                        {{-- Role --}}
                        <div>
                            <label for="role"
                                   class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Role <span class="text-rose-500">*</span>
                            </label>

                            <select id="role"
                                    name="role"
                                    x-model="role"
                                    required
                                    class="w-full rounded-xl border border-slate-300 bg-white
                                           px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                           focus:border-indigo-500 focus:ring-2
                                           focus:ring-indigo-200 outline-none transition
                                           @error('role') border-red-400 @enderror">

                                <option value="">Select a role</option>

                                @foreach($roles ?? [] as $role)
                                    <option value="{{ $role->name }}"
                                        {{ old('role') === $role->name ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach

                            </select>

                            @error('role')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror

                            <p class="mt-1.5 text-xs text-slate-400">
                                The role controls what the user can access.
                            </p>
                        </div>

                    </div>

                </div>


                {{-- Password --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">

                        <div class="flex items-center justify-center w-10 h-10 rounded-xl
                                    bg-amber-100 text-amber-600">

                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>

                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Password
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Minimum 8 characters.
                            </p>
                        </div>

                    </div>


                    <div class="p-5 space-y-4">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            {{-- Password --}}
                            <div>
                                <label for="password"
                                       class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Password <span class="text-rose-500">*</span>
                                </label>

                                <input type="password"
                                       id="password"
                                       name="password"
                                       required
                                       class="w-full rounded-xl border border-slate-300 bg-white
                                              px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                              focus:border-indigo-500 focus:ring-2
                                              focus:ring-indigo-200 outline-none transition
                                              @error('password') border-red-400 @enderror">

                                @error('password')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>


                            {{-- Confirm --}}
                            <div>
                                <label for="password_confirmation"
                                       class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Confirm Password <span class="text-rose-500">*</span>
                                </label>

                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       required
                                       class="w-full rounded-xl border border-slate-300 bg-white
                                              px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                              focus:border-indigo-500 focus:ring-2
                                              focus:ring-indigo-200 outline-none transition">
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ============ RIGHT ============ --}}
            <div class="space-y-5">

                {{-- Live Preview --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                    <div class="px-6 py-4 border-b border-slate-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="font-semibold text-slate-900 text-sm">
                                    Live Preview
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    How the user will appear
                                </p>
                            </div>
                            <span class="flex items-center gap-1.5
                                         text-xs font-medium text-emerald-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Live
                            </span>
                        </div>
                    </div>


                    <div class="p-6">

                        <div class="flex flex-col items-center text-center">

                            {{-- Avatar --}}
                            <div class="w-20 h-20 rounded-2xl
                                        bg-gradient-to-br from-indigo-500 to-violet-600
                                        flex items-center justify-center
                                        text-white font-bold text-2xl
                                        shadow-lg shadow-indigo-500/30
                                        ring-4 ring-white mb-4">

                                <span x-text="name ? name.charAt(0).toUpperCase() : '?'">?</span>

                            </div>

                            {{-- Name --}}
                            <h3 class="text-base font-semibold text-slate-900 truncate max-w-full"
                                x-text="name || 'New User'">
                            </h3>

                            {{-- Email --}}
                            <p class="text-xs text-slate-500 mt-1 truncate max-w-full"
                               x-text="email || 'user@example.com'">
                            </p>

                            {{-- Role badge --}}
                            <div class="mt-4">

                                <template x-if="role === 'admin'">
                                    <span class="inline-flex items-center gap-1.5
                                                 px-3 py-1 rounded-lg
                                                 border text-xs font-medium capitalize
                                                 bg-violet-50 text-violet-700 border-violet-200">
                                        🛡️ <span x-text="role"></span>
                                    </span>
                                </template>

                                <template x-if="role === 'staff'">
                                    <span class="inline-flex items-center gap-1.5
                                                 px-3 py-1 rounded-lg
                                                 border text-xs font-medium capitalize
                                                 bg-emerald-50 text-emerald-700 border-emerald-200">
                                        🧑‍💼 <span x-text="role"></span>
                                    </span>
                                </template>

                                <template x-if="role === 'guest'">
                                    <span class="inline-flex items-center gap-1.5
                                                 px-3 py-1 rounded-lg
                                                 border text-xs font-medium capitalize
                                                 bg-amber-50 text-amber-700 border-amber-200">
                                        🙋 <span x-text="role"></span>
                                    </span>
                                </template>

                                <template x-if="!role">
                                    <span class="inline-flex items-center gap-1.5
                                                 px-3 py-1 rounded-lg
                                                 border text-xs font-medium
                                                 bg-slate-100 text-slate-600 border-slate-200">
                                        No role selected
                                    </span>
                                </template>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Tips --}}
                <div class="bg-indigo-50/70 border border-indigo-100
                            rounded-2xl p-5">

                    <div class="flex items-start gap-3">

                        <div class="w-8 h-8 rounded-lg bg-indigo-100
                                    flex items-center justify-center
                                    text-base flex-shrink-0">
                            💡
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-indigo-900 mb-1">
                                Quick Tips
                            </p>
                            <ul class="text-xs text-indigo-700/80 space-y-1.5 leading-relaxed">
                                <li>• Use a real email — it's used for login</li>
                                <li>• Passwords must be 8+ characters</li>
                                <li>• Assign the correct role before saving</li>
                            </ul>
                        </div>

                    </div>

                </div>


                {{-- Actions --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3">

                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2
                                   px-5 py-3 rounded-xl
                                   bg-gradient-to-r from-indigo-500 to-violet-600
                                   text-sm font-semibold text-white
                                   hover:shadow-lg hover:shadow-indigo-500/30
                                   active:scale-[0.98]
                                   transition shadow-md">

                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M5 13l4 4L19 7"/>
                        </svg>

                        Create User

                    </button>

                    <a href="{{ route('users.index') }}"
                       class="w-full inline-flex items-center justify-center gap-2
                              px-5 py-3 rounded-xl
                              bg-white border border-slate-200
                              text-sm font-medium text-slate-700
                              hover:bg-slate-50 hover:border-slate-300
                              active:scale-[0.98]
                              transition">

                        Cancel

                    </a>

                </div>

            </div>

        </div>

    </form>

</div>

@endsection
