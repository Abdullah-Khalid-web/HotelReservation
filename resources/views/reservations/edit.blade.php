@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50 py-6">

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- ============ PAGE HEADER ============ --}}
        <div class="mb-6 flex items-center gap-4">

            <a href="{{ route('reservations.show', $reservation) }}"
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
                    <a href="{{ route('reservations.index') }}" class="hover:text-indigo-600">
                        Reservations
                    </a>
                    <span>/</span>
                    <a href="{{ route('reservations.show', $reservation) }}" class="hover:text-indigo-600">
                        #{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }}
                    </a>
                    <span>/</span>
                    <span class="text-slate-700">Edit</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">
                    Edit Reservation
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    Update the reservation details.
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


        @php
            // Pre-checked tables: old input OR current reservation's tables
            $selectedTables = old('tables', $reservation->tables->pluck('id')->toArray());
        @endphp


        <form action="{{ route('reservations.update', $reservation) }}"
              method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                {{-- =========================================== --}}
                {{-- LEFT (2/3) --}}
                {{-- =========================================== --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Reservation Info --}}
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">

                            <div class="flex items-center justify-center w-10 h-10 rounded-xl
                                        bg-indigo-100 text-indigo-600">

                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>

                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Reservation Details
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Update date, time, and guest count.
                                </p>
                            </div>

                        </div>


                        <div class="p-5 space-y-4">

                            {{-- Hotel --}}
                            <div>
                                <label for="hotel_id"
                                       class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Hotel <span class="text-rose-500">*</span>
                                </label>

                                <select id="hotel_id" name="hotel_id" required
                                        class="w-full rounded-xl border border-slate-300 bg-white
                                               px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                               focus:border-indigo-500 focus:ring-2
                                               focus:ring-indigo-200 outline-none transition
                                               @error('hotel_id') border-red-400 @enderror">

                                    <option value="">Select a hotel</option>

                                    @foreach($hotels ?? [] as $hotel)
                                        <option value="{{ $hotel->id }}"
                                            {{ old('hotel_id', $reservation->hotel_id) == $hotel->id ? 'selected' : '' }}>
                                            {{ $hotel->name }}
                                        </option>
                                    @endforeach

                                </select>

                                @error('hotel_id')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>


                            {{-- Date + Time --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div>
                                    <label for="reservation_date"
                                           class="block text-sm font-semibold text-slate-700 mb-1.5">
                                        Date <span class="text-rose-500">*</span>
                                    </label>

                                    <input type="date"
                                           id="reservation_date"
                                           name="reservation_date"
                                           value="{{ old('reservation_date', \Carbon\Carbon::parse($reservation->reservation_date)->format('Y-m-d')) }}"
                                           required
                                           class="w-full rounded-xl border border-slate-300 bg-white
                                                  px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                                  focus:border-indigo-500 focus:ring-2
                                                  focus:ring-indigo-200 outline-none transition
                                                  @error('reservation_date') border-red-400 @enderror">

                                    @error('reservation_date')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="reservation_time"
                                           class="block text-sm font-semibold text-slate-700 mb-1.5">
                                        Time <span class="text-rose-500">*</span>
                                    </label>

                                    <input type="time"
                                           id="reservation_time"
                                           name="reservation_time"
                                           value="{{ old('reservation_time', \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i')) }}"
                                           required
                                           class="w-full rounded-xl border border-slate-300 bg-white
                                                  px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                                  focus:border-indigo-500 focus:ring-2
                                                  focus:ring-indigo-200 outline-none transition
                                                  @error('reservation_time') border-red-400 @enderror">

                                    @error('reservation_time')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>


                            {{-- Guests --}}
                            <div>
                                <label for="number_of_guests"
                                       class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Number of Guests <span class="text-rose-500">*</span>
                                </label>

                                <input type="number"
                                       id="number_of_guests"
                                       name="number_of_guests"
                                       value="{{ old('number_of_guests', $reservation->number_of_guests) }}"
                                       min="1" max="50"
                                       required
                                       class="w-full rounded-xl border border-slate-300 bg-white
                                              px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                              focus:border-indigo-500 focus:ring-2
                                              focus:ring-indigo-200 outline-none transition
                                              @error('number_of_guests') border-red-400 @enderror">

                                @error('number_of_guests')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>


                            {{-- Notes --}}
                            <div>
                                <label for="notes"
                                       class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Notes
                                </label>

                                <textarea id="notes"
                                          name="notes"
                                          rows="3"
                                          placeholder="Special requests, allergies, etc."
                                          class="w-full rounded-xl border border-slate-300 bg-white
                                                 px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                                 placeholder-slate-400 resize-none
                                                 focus:border-indigo-500 focus:ring-2
                                                 focus:ring-indigo-200 outline-none transition
                                                 @error('notes') border-red-400 @enderror">{{ old('notes', $reservation->notes) }}</textarea>

                                @error('notes')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                    </div>


                    {{-- Status --}}
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">

                            <div class="flex items-center justify-center w-10 h-10 rounded-xl
                                        bg-amber-100 text-amber-600">

                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>

                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Status
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Update the reservation status.
                                </p>
                            </div>

                        </div>


                        <div class="p-5">

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">

                                @php
                                    $statuses = [
                                        ['value' => 'pending',   'label' => 'Pending',   'icon' => '⏳', 'hover' => 'hover:border-amber-400',   'checked' => 'peer-checked:border-amber-500 peer-checked:bg-amber-50',   'bg' => 'bg-amber-100 text-amber-600'],
                                        ['value' => 'confirmed', 'label' => 'Confirmed', 'icon' => '✓', 'hover' => 'hover:border-emerald-400', 'checked' => 'peer-checked:border-emerald-500 peer-checked:bg-emerald-50', 'bg' => 'bg-emerald-100 text-emerald-600'],
                                        ['value' => 'cancelled', 'label' => 'Cancelled', 'icon' => '✕', 'hover' => 'hover:border-rose-400',    'checked' => 'peer-checked:border-rose-500 peer-checked:bg-rose-50',    'bg' => 'bg-rose-100 text-rose-600'],
                                        ['value' => 'completed', 'label' => 'Completed', 'icon' => '✓', 'hover' => 'hover:border-sky-400',     'checked' => 'peer-checked:border-sky-500 peer-checked:bg-sky-50',      'bg' => 'bg-sky-100 text-sky-600'],
                                    ];
                                @endphp

                                @foreach($statuses as $status)

                                    <label class="cursor-pointer">

                                        <input type="radio"
                                               name="status"
                                               value="{{ $status['value'] }}"
                                               {{ old('status', $reservation->status) === $status['value'] ? 'checked' : '' }}
                                               class="peer sr-only">

                                        <div class="rounded-xl border-2 border-slate-200 bg-white p-3 text-center
                                                    {{ $status['hover'] }} {{ $status['checked'] }} transition">

                                            <div class="w-9 h-9 mx-auto rounded-lg {{ $status['bg'] }}
                                                        flex items-center justify-center font-bold text-sm mb-1.5">
                                                {{ $status['icon'] }}
                                            </div>

                                            <p class="text-xs font-medium text-slate-700">
                                                {{ $status['label'] }}
                                            </p>

                                        </div>

                                    </label>

                                @endforeach

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =========================================== --}}
                {{-- RIGHT (1/3) --}}
                {{-- =========================================== --}}
                <div class="space-y-5">

                    {{-- Table Selection --}}
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">

                            <div class="flex items-center justify-center w-10 h-10 rounded-xl
                                        bg-violet-100 text-violet-600">

                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M4 10h16M5 10v7a2 2 0 002 2h10a2 2 0 002-2v-7M8 10V7a4 4 0 018 0v3"/>
                                </svg>

                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Tables
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Assign tables to this reservation.
                                </p>
                            </div>

                        </div>


                        <div class="p-5 max-h-96 overflow-y-auto">

                            @forelse($tables ?? [] as $table)

                                <label class="flex items-start gap-3 p-3 rounded-xl
                                              hover:bg-slate-50 cursor-pointer transition mb-1">

                                    <input type="checkbox"
                                           name="tables[]"
                                           value="{{ $table->id }}"
                                           {{ in_array($table->id, $selectedTables) ? 'checked' : '' }}
                                           class="mt-0.5 rounded border-slate-300 text-indigo-600
                                                  focus:ring-indigo-500">

                                    <div class="flex-1 min-w-0">

                                        <p class="text-sm font-medium text-slate-800">
                                            Table {{ $table->table_number }}
                                        </p>

                                        <p class="text-xs text-slate-500 truncate">
                                            {{ $table->hotel->name ?? '—' }} · {{ $table->capacity }} seats
                                        </p>

                                    </div>

                                </label>

                            @empty

                                <p class="text-sm text-slate-400 text-center py-6">
                                    No tables available
                                </p>

                            @endforelse

                        </div>

                    </div>


                    {{-- Actions --}}
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2
                                       px-5 py-3 rounded-xl
                                       bg-gradient-to-r from-indigo-500 to-violet-600
                                       text-sm font-semibold text-white
                                       hover:shadow-lg hover:shadow-indigo-500/30
                                       active:scale-[0.98] transition shadow-md">

                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                 stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M5 13l4 4L19 7"/>
                            </svg>

                            Save Changes

                        </button>

                        <a href="{{ route('reservations.show', $reservation) }}"
                           class="mt-3 w-full inline-flex items-center justify-center gap-2
                                  px-5 py-3 rounded-xl
                                  bg-white text-slate-700
                                  border-2 border-slate-300
                                  text-sm font-semibold
                                  hover:bg-slate-50 hover:border-slate-400 transition">

                            Cancel

                        </a>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>

@endsection
