@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50 py-8">

    {{-- PAGE HEADER --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">

            <div class="flex items-center gap-4">

                {{-- Back --}}
                <a href="{{ route('tables.index') }}"
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
                        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">
                            Dashboard
                        </a>
                        <span>/</span>
                        <a href="{{ route('tables.index') }}" class="hover:text-indigo-600">
                            Tables
                        </a>
                        <span>/</span>
                        <span class="text-slate-700">Add New</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">
                        Add New Table
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Create a restaurant table and configure its availability.
                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- VALIDATION / SUCCESS MESSAGES --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-xl
                        border border-emerald-200 bg-emerald-50
                        px-4 py-4 text-emerald-800">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif


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


        {{-- FORM --}}
        <form action="{{ route('tables.store') }}"
              method="POST"
              x-data="{
                    preview: {
                        number: @js(old('table_number', '')),
                        capacity: @js(old('capacity', 4)),
                        status: @js(old('status', 'available'))
                    }
              }">

            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT SIDE --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- TABLE INFORMATION --}}
                    <div class="bg-white rounded-2xl border border-slate-200
                                shadow-sm overflow-hidden">

                        <div class="px-6 py-5 border-b border-slate-200">
                            <div class="flex items-center gap-3">

                                <div class="flex items-center justify-center
                                            w-11 h-11 rounded-xl
                                            bg-indigo-100 text-indigo-600">

                                    <svg class="w-6 h-6" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M4 10h16M5 10v7a2 2 0 002 2h10a2 2 0 002-2v-7M8 10V7a4 4 0 018 0v3"/>
                                    </svg>

                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">
                                        Table Information
                                    </h2>
                                    <p class="text-sm text-slate-500 mt-0.5">
                                        Enter the basic details of the table.
                                    </p>
                                </div>

                            </div>
                        </div>


                        <div class="p-6 space-y-6">

                            {{-- HOTEL --}}
                            <div>
                                <label for="hotel_id"
                                       class="block text-sm font-semibold text-slate-700 mb-2">
                                    Hotel <span class="text-red-500">*</span>
                                </label>

                                <select id="hotel_id"
                                        name="hotel_id"
                                        required
                                        class="w-full rounded-xl border
                                               border-slate-300 bg-white
                                               px-4 py-3 text-sm text-slate-800
                                               shadow-sm
                                               focus:border-indigo-500
                                               focus:ring-2 focus:ring-indigo-200
                                               outline-none transition
                                               @error('hotel_id') border-red-400 @enderror">

                                    <option value="">Select a hotel</option>

                                    @foreach($hotels ?? [] as $hotel)
                                        <option value="{{ $hotel->id }}"
                                            {{ old('hotel_id') == $hotel->id ? 'selected' : '' }}>
                                            {{ $hotel->name }}
                                        </option>
                                    @endforeach

                                </select>

                                @error('hotel_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>


                            {{-- TABLE NUMBER + CAPACITY --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                                {{-- TABLE NUMBER --}}
                                <div>
                                    <label for="table_number"
                                           class="block text-sm font-semibold text-slate-700 mb-2">
                                        Table Number <span class="text-red-500">*</span>
                                    </label>

                                    <input type="text"
                                           id="table_number"
                                           name="table_number"
                                           value="{{ old('table_number') }}"
                                           placeholder="e.g. T-01"
                                           required
                                           x-model="preview.number"
                                           class="w-full rounded-xl border
                                                  border-slate-300 bg-white
                                                  px-4 py-3 text-sm text-slate-800
                                                  placeholder-slate-400
                                                  shadow-sm
                                                  focus:border-indigo-500
                                                  focus:ring-2 focus:ring-indigo-200
                                                  outline-none transition
                                                  @error('table_number') border-red-400 @enderror">

                                    @error('table_number')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>


                                {{-- CAPACITY --}}
                                <div>
                                    <label for="capacity"
                                           class="block text-sm font-semibold text-slate-700 mb-2">
                                        Capacity <span class="text-red-500">*</span>
                                    </label>

                                    <div class="relative">

                                        <input type="number"
                                               id="capacity"
                                               name="capacity"
                                               value="{{ old('capacity', 4) }}"
                                               min="1"
                                               max="50"
                                               required
                                               x-model.number="preview.capacity"
                                               class="w-full rounded-xl border
                                                      border-slate-300 bg-white
                                                      px-4 py-3 pr-16 text-sm
                                                      text-slate-800
                                                      shadow-sm
                                                      focus:border-indigo-500
                                                      focus:ring-2 focus:ring-indigo-200
                                                      outline-none transition
                                                      @error('capacity') border-red-400 @enderror">

                                        <span class="absolute right-4 top-1/2
                                                     -translate-y-1/2
                                                     text-xs text-slate-400">
                                            seats
                                        </span>

                                    </div>

                                    @error('capacity')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- STATUS --}}
                    <div class="bg-white rounded-2xl border border-slate-200
                                shadow-sm overflow-hidden">

                        <div class="px-6 py-5 border-b border-slate-200">
                            <div class="flex items-center gap-3">

                                <div class="flex items-center justify-center
                                            w-11 h-11 rounded-xl
                                            bg-amber-100 text-amber-600">

                                    <svg class="w-6 h-6" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>

                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">
                                        Initial Status
                                    </h2>
                                    <p class="text-sm text-slate-500 mt-0.5">
                                        Select the current status of this table.
                                    </p>
                                </div>

                            </div>
                        </div>


                        <div class="p-6">

                            {{-- 3 STATUS OPTIONS (matches DB enum) --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                                {{-- AVAILABLE --}}
                                <label class="cursor-pointer">
                                    <input type="radio"
                                           name="status"
                                           value="available"
                                           x-model="preview.status"
                                           {{ old('status', 'available') === 'available' ? 'checked' : '' }}
                                           class="peer sr-only">

                                    <div class="rounded-xl border-2 border-slate-200
                                                bg-white p-4
                                                hover:border-emerald-400
                                                peer-checked:border-emerald-500
                                                peer-checked:bg-emerald-50
                                                transition">

                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center justify-center
                                                        w-10 h-10 rounded-lg
                                                        bg-emerald-100 text-emerald-600
                                                        font-bold">
                                                ✓
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-900">
                                                    Available
                                                </p>
                                                <p class="text-xs text-slate-500">
                                                    Ready for reservation
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </label>


                                {{-- RESERVED --}}
                                <label class="cursor-pointer">
                                    <input type="radio"
                                           name="status"
                                           value="reserved"
                                           x-model="preview.status"
                                           {{ old('status') === 'reserved' ? 'checked' : '' }}
                                           class="peer sr-only">

                                    <div class="rounded-xl border-2 border-slate-200
                                                bg-white p-4
                                                hover:border-amber-400
                                                peer-checked:border-amber-500
                                                peer-checked:bg-amber-50
                                                transition">

                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center justify-center
                                                        w-10 h-10 rounded-lg
                                                        bg-amber-100 text-amber-600
                                                        font-bold">
                                                ⏳
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-900">
                                                    Reserved
                                                </p>
                                                <p class="text-xs text-slate-500">
                                                    Already booked
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </label>


                                {{-- MAINTENANCE --}}
                                <label class="cursor-pointer">
                                    <input type="radio"
                                           name="status"
                                           value="maintenance"
                                           x-model="preview.status"
                                           {{ old('status') === 'maintenance' ? 'checked' : '' }}
                                           class="peer sr-only">

                                    <div class="rounded-xl border-2 border-slate-200
                                                bg-white p-4
                                                hover:border-slate-500
                                                peer-checked:border-slate-600
                                                peer-checked:bg-slate-100
                                                transition">

                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center justify-center
                                                        w-10 h-10 rounded-lg
                                                        bg-slate-200 text-slate-600
                                                        font-bold">
                                                ⚙
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-900">
                                                    Maintenance
                                                </p>
                                                <p class="text-xs text-slate-500">
                                                    Not currently available
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </label>

                            </div>

                            @error('status')
                                <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                        </div>

                    </div>

                </div>


                {{-- RIGHT SIDE --}}
                <div class="space-y-6">

                    {{-- LIVE PREVIEW --}}
                    <div class="bg-white rounded-2xl border border-slate-200
                                shadow-sm overflow-hidden">

                        <div class="px-6 py-5 border-b border-slate-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="font-semibold text-slate-900">
                                        Table Preview
                                    </h2>
                                    <p class="text-xs text-slate-500 mt-1">
                                        Live preview
                                    </p>
                                </div>

                                <span class="flex items-center gap-2
                                             text-xs font-medium text-emerald-600">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Live
                                </span>
                            </div>
                        </div>


                        <div class="p-6">

                            {{-- PREVIEW CARD --}}
                            <div class="rounded-2xl bg-slate-900 p-6 text-white shadow-xl">

                                <div class="flex items-center justify-between mb-7">

                                    <div class="w-12 h-12 rounded-xl bg-indigo-500
                                                flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M4 10h16M5 10v7a2 2 0 002 2h10a2 2 0 002-2v-7M8 10V7a4 4 0 018 0v3"/>
                                        </svg>
                                    </div>

                                    <span class="px-3 py-1.5 rounded-full
                                                 bg-white/10 border border-white/10
                                                 text-xs font-medium capitalize"
                                          x-text="preview.status">
                                    </span>

                                </div>


                                <p class="text-xs text-slate-400 mb-1">TABLE</p>

                                <h3 class="text-3xl font-bold"
                                    x-text="preview.number || 'T-01'">
                                </h3>


                                <div class="mt-6 grid grid-cols-1 gap-3">

                                    <div class="rounded-xl bg-white/10 p-3">
                                        <p class="text-xs text-slate-400">Capacity</p>
                                        <p class="font-semibold mt-1"
                                           x-text="preview.capacity + ' seats'"></p>
                                    </div>

                                </div>

                            </div>


                            {{-- INFO --}}
                            <div class="mt-5 rounded-xl bg-indigo-50
                                        border border-indigo-100 p-4">

                                <div class="flex gap-3">

                                    <div class="flex-shrink-0 text-indigo-600">💡</div>

                                    <div>
                                        <p class="text-sm font-semibold text-indigo-900">
                                            Tip
                                        </p>
                                        <p class="text-xs text-indigo-700 mt-1 leading-relaxed">
                                            Use simple numbers such as T-01, T-02,
                                            T-03 so staff can identify tables quickly.
                                        </p>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- ACTIONS --}}
                    <div class="bg-white rounded-2xl border border-slate-200
                                shadow-sm p-5">

                        <button type="submit"
                                class="w-full flex items-center justify-center
                                       gap-2 px-5 py-3.5 rounded-xl
                                       bg-indigo-600 text-white
                                       font-semibold text-sm
                                       shadow-md shadow-indigo-200
                                       hover:bg-indigo-700
                                       hover:shadow-lg
                                       focus:outline-none
                                       focus:ring-4 focus:ring-indigo-200
                                       transition">

                            <svg class="w-5 h-5" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>

                            Create Table

                        </button>


                        <a href="{{ route('tables.index') }}"
                           class="mt-3 w-full flex items-center justify-center
                                  gap-2 px-5 py-3.5 rounded-xl
                                  bg-white text-slate-700
                                  border-2 border-slate-300
                                  font-semibold text-sm
                                  hover:bg-slate-50
                                  hover:border-slate-400
                                  transition">

                            <svg class="w-5 h-5" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>

                            Cancel

                        </a>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>

@endsection
