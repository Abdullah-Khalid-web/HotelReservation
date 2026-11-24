@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- ============ PAGE HEADER ============ --}}
    <div class="mb-6 flex items-center justify-between gap-4">

        <div class="flex items-center gap-4">

            {{-- Back --}}
            <a href="{{ route('reservations.index') }}"
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
                    <a href="{{ route('reservations.index') }}" class="hover:text-indigo-600 transition">
                        Reservations
                    </a>
                    <span>/</span>
                    <span class="text-slate-700">
                        #{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">
                    Reservation Details
                </h1>

            </div>

        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2">

            @can('edit reservations')
                <a href="{{ route('reservations.edit', $reservation) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                          bg-gradient-to-r from-indigo-500 to-violet-600
                          text-sm font-medium text-white
                          hover:shadow-lg hover:shadow-indigo-500/30
                          transition shadow-md">

                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                         stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>

                    Edit

                </a>
            @endcan

        </div>

    </div>


    {{-- ============ FLASH ============ --}}
    @if(session('success'))
        <div class="mb-5 flex items-start gap-3 rounded-xl
                    border border-emerald-200 bg-emerald-50
                    px-4 py-3 text-emerald-800">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ============ LEFT (2/3) ============ --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Reservation Info Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                {{-- Gradient header --}}
                <div class="relative h-32 bg-gradient-to-r from-indigo-500 via-indigo-600 to-violet-600">

                    <div class="absolute -bottom-8 left-6">

                        <div class="w-16 h-16 rounded-2xl
                                    bg-white shadow-lg
                                    flex items-center justify-center
                                    text-3xl">

                            📅

                        </div>

                    </div>

                    {{-- Status badge --}}
                    <div class="absolute top-4 right-4">

                        @php
                            $status = $reservation->status ?? 'pending';
                            $statusConfig = [
                                'pending'   => ['bg-white/20 text-white border-white/30', '⏳ Pending'],
                                'confirmed' => ['bg-white/20 text-white border-white/30', '✓ Confirmed'],
                                'cancelled' => ['bg-white/20 text-white border-white/30', '✕ Cancelled'],
                                'completed' => ['bg-white/20 text-white border-white/30', '✓ Completed'],
                            ];
                            [$badgeClasses, $badgeLabel] = $statusConfig[$status] ?? $statusConfig['pending'];
                        @endphp

                        <span class="inline-flex items-center gap-1.5
                                     px-3 py-1.5 rounded-full
                                     backdrop-blur-sm border
                                     text-xs font-semibold
                                     {{ $badgeClasses }}">

                            {{ $badgeLabel }}

                        </span>

                    </div>

                </div>


                <div class="pt-12 px-6 pb-6">

                    <div class="flex items-center justify-between mb-5">

                        <div>

                            <h2 class="text-xl font-bold text-slate-900">
                                Reservation #{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }}
                            </h2>

                            <p class="text-xs text-slate-500 mt-0.5">
                                Created {{ $reservation->created_at->diffForHumans() }}
                            </p>

                        </div>

                    </div>


                    {{-- Info Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- Date --}}
                        <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">

                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-base">📆</span>
                                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                                    Date
                                </p>
                            </div>

                            <p class="text-sm font-semibold text-slate-800 mt-2">
                                {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('l, M d, Y') }}
                            </p>

                        </div>

                        {{-- Time --}}
                        <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">

                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-base">🕐</span>
                                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                                    Time
                                </p>
                            </div>

                            <p class="text-sm font-semibold text-slate-800 mt-2">
                                {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('h:i A') }}
                            </p>

                        </div>

                        {{-- Guests --}}
                        <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">

                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-base">👥</span>
                                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                                    Guests
                                </p>
                            </div>

                            <p class="text-sm font-semibold text-slate-800 mt-2">
                                {{ $reservation->number_of_guests }} {{ Str::plural('person', $reservation->number_of_guests) }}
                            </p>

                        </div>

                        {{-- Hotel --}}
                        <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">

                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-base">🏨</span>
                                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                                    Hotel
                                </p>
                            </div>

                            <p class="text-sm font-semibold text-slate-800 mt-2 truncate">
                                {{ $reservation->hotel->name ?? '—' }}
                            </p>

                        </div>

                    </div>


                    {{-- Notes --}}
                    @if($reservation->notes)
                        <div class="mt-5 rounded-xl bg-amber-50 border border-amber-100 p-4">

                            <div class="flex items-start gap-3">

                                <span class="text-base flex-shrink-0">📝</span>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-amber-800 mb-1">
                                        Notes
                                    </p>
                                    <p class="text-sm text-amber-900/80 leading-relaxed">
                                        {{ $reservation->notes }}
                                    </p>
                                </div>

                            </div>

                        </div>
                    @endif

                </div>

            </div>


            {{-- Tables Card --}}
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
                            Reserved Tables
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $reservation->tables->count() }} {{ Str::plural('table', $reservation->tables->count()) }} assigned
                        </p>
                    </div>

                </div>


                <div class="p-5">

                    @if($reservation->tables->count())

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                            @foreach($reservation->tables as $table)

                                <div class="rounded-xl border border-slate-200 bg-white p-4
                                            hover:shadow-md hover:border-indigo-200
                                            transition">

                                    <div class="flex items-center gap-3">

                                        <div class="w-10 h-10 rounded-lg
                                                    bg-gradient-to-br from-indigo-50 to-violet-50
                                                    border border-indigo-100
                                                    flex items-center justify-center text-base">

                                            🪑

                                        </div>

                                        <div class="min-w-0 flex-1">

                                            <p class="text-sm font-semibold text-slate-800 truncate">
                                                Table {{ $table->table_number }}
                                            </p>

                                            <p class="text-xs text-slate-500 truncate mt-0.5">
                                                {{ $table->hotel->name ?? '—' }} · {{ $table->capacity }} seats
                                            </p>

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div class="text-center py-8">

                            <div class="inline-flex items-center justify-center
                                        w-12 h-12 rounded-xl bg-slate-100
                                        text-xl mb-3">

                                🪑

                            </div>

                            <p class="text-sm text-slate-500">
                                No tables assigned to this reservation.
                            </p>

                        </div>

                    @endif

                </div>

            </div>

        </div>


        {{-- ============ RIGHT (1/3) ============ --}}
        <div class="space-y-5">

            {{-- Guest Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                <div class="px-5 py-4 border-b border-slate-100">

                    <h2 class="text-sm font-semibold text-slate-900">
                        Guest
                    </h2>

                </div>


                <div class="p-5">

                    <div class="flex flex-col items-center text-center">

                        <div class="w-16 h-16 rounded-2xl
                                    bg-gradient-to-br from-indigo-500 to-violet-600
                                    flex items-center justify-center
                                    text-white font-bold text-xl
                                    shadow-lg shadow-indigo-500/30
                                    ring-4 ring-white mb-3">

                            {{ strtoupper(substr($reservation->user->name ?? 'G', 0, 1)) }}

                        </div>

                        <p class="text-sm font-semibold text-slate-900">
                            {{ $reservation->user->name ?? 'Guest' }}
                        </p>

                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $reservation->user->email ?? '—' }}
                        </p>

                        @if($reservation->user)
                            <a href="{{ route('users.show', $reservation->user) }}"
                               class="mt-3 text-xs font-medium text-indigo-600
                                      hover:text-indigo-700 transition">
                                View Profile →
                            </a>
                        @endif

                    </div>

                </div>

            </div>


            {{-- Timeline Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                <div class="px-5 py-4 border-b border-slate-100">

                    <h2 class="text-sm font-semibold text-slate-900">
                        Timeline
                    </h2>

                </div>


                <div class="p-5 space-y-4">

                    <div class="flex gap-3">

                        <div class="flex flex-col items-center">

                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></div>

                        </div>

                        <div class="flex-1 -mt-0.5">

                            <p class="text-xs font-semibold text-slate-800">
                                Created
                            </p>

                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $reservation->created_at->format('M d, Y · h:i A') }}
                            </p>

                        </div>

                    </div>


                    <div class="flex gap-3">

                        <div class="flex flex-col items-center">

                            <div class="w-2.5 h-2.5 rounded-full bg-indigo-500 ring-4 ring-indigo-100"></div>

                        </div>

                        <div class="flex-1 -mt-0.5">

                            <p class="text-xs font-semibold text-slate-800">
                                Last Updated
                            </p>

                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $reservation->updated_at->format('M d, Y · h:i A') }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Danger Zone --}}
            @can('delete reservations')
                <div class="bg-white rounded-2xl border border-rose-200 shadow-sm p-5">

                    <h3 class="text-sm font-semibold text-rose-800 mb-1">
                        Danger Zone
                    </h3>

                    <p class="text-xs text-rose-700/70 mb-3 leading-relaxed">
                        Permanently delete this reservation. This cannot be undone.
                    </p>

                    <form action="{{ route('reservations.destroy', $reservation) }}"
                          method="POST"
                          onsubmit="return confirm('Delete this reservation? This cannot be undone.');">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2
                                       px-4 py-2.5 rounded-xl
                                       bg-rose-50 text-rose-700 border border-rose-200
                                       text-sm font-medium
                                       hover:bg-rose-100 hover:border-rose-300
                                       active:scale-[0.98] transition">

                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                 stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                            </svg>

                            Delete Reservation

                        </button>

                    </form>

                </div>
            @endcan

        </div>

    </div>

</div>

@endsection
