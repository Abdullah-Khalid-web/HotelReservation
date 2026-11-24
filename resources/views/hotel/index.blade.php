@extends('layouts.app')

@section('content')

    <div class="max-w-5xl mx-auto">

        {{-- ============ PAGE HEADER ============ --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center
                    sm:justify-between gap-4">

            <div>

                <div class="flex items-center gap-2 text-xs text-slate-400 mb-2">
                    <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition">
                        Dashboard
                    </a>
                    <span>/</span>
                    <span class="text-slate-600 font-medium">Hotel</span>
                </div>

                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">
                    Hotel
                </h1>

                <p class="text-slate-500 mt-1.5 text-sm">
                    Manage your hotel information.
                </p>

            </div>

            @if($hotel)
                <div class="flex items-center gap-3">

                    <span class="inline-flex items-center gap-1.5
                                 px-3 py-1.5 rounded-full
                                 {{ $hotel->status === 'active'
                                        ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                        : 'bg-slate-100 text-slate-600 border border-slate-200' }}
                                 text-xs font-medium">

                        <span class="w-1.5 h-1.5 rounded-full
                                     {{ $hotel->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}">
                        </span>

                        {{ ucfirst($hotel->status) }}

                    </span>

                </div>
            @endif

        </div>


        {{-- ============ SUCCESS MESSAGE ============ --}}
        @if(session('success'))

            <div class="mb-6 flex items-start gap-3 rounded-xl
                        border border-emerald-200 bg-emerald-50
                        px-4 py-4 text-emerald-800">

                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>

                <p class="text-sm font-medium">
                    {{ session('success') }}
                </p>

            </div>

        @endif


        {{-- ============ VALIDATION ERRORS ============ --}}
        @if($errors->any())

            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">

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


        @if($hotel)

                    {{-- =========================================== --}}
            {{-- HOTEL SUMMARY --}}
            {{-- =========================================== --}}
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-5">

                {{-- Total Tables --}}
                <div class="group bg-white rounded-2xl
                            border border-slate-200/70 p-5
                            hover:shadow-lg hover:shadow-slate-200/60
                            hover:-translate-y-0.5
                            transition-all duration-300">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-xs font-medium uppercase
                                      tracking-wider text-slate-500">

                                Total Tables

                            </p>

                            <p class="text-3xl font-bold text-slate-900 mt-2">
                                {{ $hotel->tables()->count() }}
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-xl bg-indigo-50
                                    flex items-center justify-center text-lg
                                    group-hover:scale-110 transition">

                            🪑

                        </div>

                    </div>

                </div>


                {{-- Reservations --}}
                <div class="group bg-white rounded-2xl
                            border border-slate-200/70 p-5
                            hover:shadow-lg hover:shadow-slate-200/60
                            hover:-translate-y-0.5
                            transition-all duration-300">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-xs font-medium uppercase
                                      tracking-wider text-slate-500">

                                Reservations

                            </p>

                            <p class="text-3xl font-bold text-slate-900 mt-2">
                                {{ $hotel->reservations()->count() }}
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-xl bg-amber-50
                                    flex items-center justify-center text-lg
                                    group-hover:scale-110 transition">

                            📅

                        </div>

                    </div>

                </div>


                {{-- Status --}}
                <div class="group bg-white rounded-2xl
                            border border-slate-200/70 p-5
                            hover:shadow-lg hover:shadow-slate-200/60
                            hover:-translate-y-0.5
                            transition-all duration-300">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-xs font-medium uppercase
                                      tracking-wider text-slate-500">

                                Status

                            </p>

                            <p class="text-3xl font-bold mt-2
                                      {{ $hotel->status === 'active'
                                            ? 'text-emerald-600'
                                            : 'text-slate-500' }}">

                                {{ ucfirst($hotel->status) }}

                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-xl
                                    {{ $hotel->status === 'active'
                                        ? 'bg-emerald-50'
                                        : 'bg-slate-100' }}
                                    flex items-center justify-center text-lg
                                    group-hover:scale-110 transition">

                            {{ $hotel->status === 'active' ? '✅' : '⏸' }}

                        </div>

                    </div>

                </div>

            </div>
            <br>

            {{-- =========================================== --}}
            {{-- EDIT HOTEL --}}
            {{-- =========================================== --}}

            <div class="bg-white rounded-2xl border border-slate-200/70
                        shadow-sm overflow-hidden">

                {{-- Card Header --}}
                <div class="px-6 py-5 border-b border-slate-100">

                    <div class="flex items-center gap-3">

                        <div class="flex items-center justify-center
                                    w-11 h-11 rounded-xl
                                    bg-indigo-100 text-indigo-600">

                            <svg class="w-6 h-6" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/>
                            </svg>

                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Hotel Information
                            </h2>
                            <p class="text-sm text-slate-500 mt-0.5">
                                Update your hotel details.
                            </p>
                        </div>

                    </div>

                </div>


                {{-- Form --}}
                <form action="{{ route('hotel.update') }}"
                      method="POST"
                      class="p-6">

                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Name --}}
                        <div>

                            <label for="name"
                                   class="block text-sm font-semibold
                                          text-slate-700 mb-2">

                                Hotel Name
                                <span class="text-rose-500">*</span>

                            </label>

                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $hotel->name) }}"
                                   required
                                   class="w-full rounded-xl border
                                          border-slate-300 bg-white
                                          px-4 py-3 text-sm text-slate-800
                                          placeholder-slate-400
                                          shadow-sm
                                          focus:border-indigo-500
                                          focus:ring-2 focus:ring-indigo-200
                                          outline-none transition
                                          @error('name') border-red-400 @enderror">

                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                        </div>


                        {{-- Phone --}}
                        <div>

                            <label for="phone"
                                   class="block text-sm font-semibold
                                          text-slate-700 mb-2">

                                Phone

                            </label>

                            <input type="text"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone', $hotel->phone) }}"
                                   class="w-full rounded-xl border
                                          border-slate-300 bg-white
                                          px-4 py-3 text-sm text-slate-800
                                          placeholder-slate-400
                                          shadow-sm
                                          focus:border-indigo-500
                                          focus:ring-2 focus:ring-indigo-200
                                          outline-none transition
                                          @error('phone') border-red-400 @enderror">

                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                        </div>


                        {{-- Email --}}
                        <div>

                            <label for="email"
                                   class="block text-sm font-semibold
                                          text-slate-700 mb-2">

                                Email

                            </label>

                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $hotel->email) }}"
                                   class="w-full rounded-xl border
                                          border-slate-300 bg-white
                                          px-4 py-3 text-sm text-slate-800
                                          placeholder-slate-400
                                          shadow-sm
                                          focus:border-indigo-500
                                          focus:ring-2 focus:ring-indigo-200
                                          outline-none transition
                                          @error('email') border-red-400 @enderror">

                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                        </div>


                        {{-- Status --}}
                        <div>

                            <label for="status"
                                   class="block text-sm font-semibold
                                          text-slate-700 mb-2">

                                Status

                            </label>

                            <select id="status"
                                    name="status"
                                    class="w-full rounded-xl border
                                           border-slate-300 bg-white
                                           px-4 py-3 text-sm text-slate-800
                                           shadow-sm
                                           focus:border-indigo-500
                                           focus:ring-2 focus:ring-indigo-200
                                           outline-none transition
                                           @error('status') border-red-400 @enderror">

                                <option value="active"
                                    @selected(old('status', $hotel->status) === 'active')>
                                    Active
                                </option>

                                <option value="inactive"
                                    @selected(old('status', $hotel->status) === 'inactive')>
                                    Inactive
                                </option>

                            </select>

                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                        </div>


                        {{-- Address --}}
                        <div class="md:col-span-2">

                            <label for="address"
                                   class="block text-sm font-semibold
                                          text-slate-700 mb-2">

                                Address

                            </label>

                            <textarea id="address"
                                      name="address"
                                      rows="4"
                                      class="w-full rounded-xl border
                                             border-slate-300 bg-white
                                             px-4 py-3 text-sm text-slate-800
                                             placeholder-slate-400
                                             shadow-sm
                                             focus:border-indigo-500
                                             focus:ring-2 focus:ring-indigo-200
                                             outline-none transition resize-none
                                             @error('address') border-red-400 @enderror">{{ old('address', $hotel->address) }}</textarea>

                            @error('address')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                        </div>

                    </div>


                    {{-- Submit --}}
                    <div class="mt-8 flex flex-col sm:flex-row
                                sm:justify-end gap-3 pt-6 border-t border-slate-100">

                        <button type="submit"
                                class="inline-flex items-center justify-center
                                       gap-2 px-5 py-3 rounded-xl
                                       bg-gradient-to-r from-indigo-500 to-violet-600
                                       text-sm font-semibold text-white
                                       hover:shadow-lg hover:shadow-indigo-500/30
                                       active:scale-[0.98]
                                       transition shadow-md">

                            <svg class="w-4 h-4" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>

                            Update Hotel

                        </button>

                    </div>

                </form>

            </div>




        @else

            {{-- =========================================== --}}
            {{-- CREATE HOTEL --}}
            {{-- =========================================== --}}

            <div class="bg-white rounded-2xl border border-slate-200/70
                        shadow-sm overflow-hidden">

                {{-- Card Header --}}
                <div class="px-6 py-5 border-b border-slate-100">

                    <div class="flex items-center gap-3">

                        <div class="flex items-center justify-center
                                    w-11 h-11 rounded-xl
                                    bg-indigo-100 text-indigo-600">

                            <svg class="w-6 h-6" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 4v16m8-8H4"/>
                            </svg>

                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Create Hotel
                            </h2>
                            <p class="text-sm text-slate-500 mt-0.5">
                                Set up your hotel information.
                            </p>
                        </div>

                    </div>

                </div>


                {{-- Form --}}
                <form action="{{ route('hotel.store') }}"
                      method="POST"
                      class="p-6">

                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Name --}}
                        <div>

                            <label for="name"
                                   class="block text-sm font-semibold
                                          text-slate-700 mb-2">

                                Hotel Name
                                <span class="text-rose-500">*</span>

                            </label>

                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   required
                                   class="w-full rounded-xl border
                                          border-slate-300 bg-white
                                          px-4 py-3 text-sm text-slate-800
                                          placeholder-slate-400
                                          shadow-sm
                                          focus:border-indigo-500
                                          focus:ring-2 focus:ring-indigo-200
                                          outline-none transition
                                          @error('name') border-red-400 @enderror">

                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                        </div>


                        {{-- Phone --}}
                        <div>

                            <label for="phone"
                                   class="block text-sm font-semibold
                                          text-slate-700 mb-2">

                                Phone

                            </label>

                            <input type="text"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone') }}"
                                   class="w-full rounded-xl border
                                          border-slate-300 bg-white
                                          px-4 py-3 text-sm text-slate-800
                                          placeholder-slate-400
                                          shadow-sm
                                          focus:border-indigo-500
                                          focus:ring-2 focus:ring-indigo-200
                                          outline-none transition
                                          @error('phone') border-red-400 @enderror">

                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                        </div>


                        {{-- Email --}}
                        <div>

                            <label for="email"
                                   class="block text-sm font-semibold
                                          text-slate-700 mb-2">

                                Email

                            </label>

                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   class="w-full rounded-xl border
                                          border-slate-300 bg-white
                                          px-4 py-3 text-sm text-slate-800
                                          placeholder-slate-400
                                          shadow-sm
                                          focus:border-indigo-500
                                          focus:ring-2 focus:ring-indigo-200
                                          outline-none transition
                                          @error('email') border-red-400 @enderror">

                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                        </div>


                        {{-- Status --}}
                        <div>

                            <label for="status"
                                   class="block text-sm font-semibold
                                          text-slate-700 mb-2">

                                Status

                            </label>

                            <select id="status"
                                    name="status"
                                    class="w-full rounded-xl border
                                           border-slate-300 bg-white
                                           px-4 py-3 text-sm text-slate-800
                                           shadow-sm
                                           focus:border-indigo-500
                                           focus:ring-2 focus:ring-indigo-200
                                           outline-none transition">

                                <option value="active"
                                    @selected(old('status') === 'active')>
                                    Active
                                </option>

                                <option value="inactive"
                                    @selected(old('status') === 'inactive')>
                                    Inactive
                                </option>

                            </select>

                        </div>


                        {{-- Address --}}
                        <div class="md:col-span-2">

                            <label for="address"
                                   class="block text-sm font-semibold
                                          text-slate-700 mb-2">

                                Address

                            </label>

                            <textarea id="address"
                                      name="address"
                                      rows="4"
                                      class="w-full rounded-xl border
                                             border-slate-300 bg-white
                                             px-4 py-3 text-sm text-slate-800
                                             placeholder-slate-400
                                             shadow-sm
                                             focus:border-indigo-500
                                             focus:ring-2 focus:ring-indigo-200
                                             outline-none transition resize-none
                                             @error('address') border-red-400 @enderror">{{ old('address') }}</textarea>

                            @error('address')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                        </div>

                    </div>


                    {{-- Submit --}}
                    <div class="mt-8 flex flex-col sm:flex-row
                                sm:justify-end gap-3 pt-6 border-t border-slate-100">

                        <button type="submit"
                                class="inline-flex items-center justify-center
                                       gap-2 px-5 py-3 rounded-xl
                                       bg-gradient-to-r from-indigo-500 to-violet-600
                                       text-sm font-semibold text-white
                                       hover:shadow-lg hover:shadow-indigo-500/30
                                       active:scale-[0.98]
                                       transition shadow-md">

                            <svg class="w-4 h-4" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>

                            Create Hotel

                        </button>

                    </div>

                </form>

            </div>

        @endif

    </div>

@endsection
