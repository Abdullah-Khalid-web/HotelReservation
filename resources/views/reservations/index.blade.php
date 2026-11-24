@extends('layouts.app')

@section('content')

    {{-- ============ PAGE HEADER ============ --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center
                sm:justify-between gap-4">

        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition">
                    Dashboard
                </a>
                <span>/</span>
                <span class="text-slate-600 font-medium">Reservations</span>
            </div>

            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">
                Reservations
            </h1>

            <p class="text-slate-500 mt-1.5 text-sm">
                Manage all hotel reservations and their status.
            </p>
        </div>

        <div class="flex items-center gap-2">

            <button class="px-4 py-2 rounded-xl bg-white border border-slate-200
                           text-sm font-medium text-slate-700
                           hover:bg-slate-50 hover:border-slate-300
                           transition shadow-sm inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                     stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                Export
            </button>

            @can('create reservations')
                <a href="{{ route('reservations.create') }}"
                   class="px-4 py-2 rounded-xl
                          bg-gradient-to-r from-indigo-500 to-violet-600
                          text-sm font-medium text-white
                          hover:shadow-lg hover:shadow-indigo-500/30
                          active:scale-[0.98]
                          transition shadow-md inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                         stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                    New Reservation
                </a>
            @endcan

        </div>

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
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif


    {{-- ============ STATS ROW ============ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">

        {{-- Total --}}
        <div class="group bg-white rounded-2xl border border-slate-200/70 p-5
                    hover:shadow-lg hover:shadow-slate-200/60
                    hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Total Reservations
                    </p>
                    <h2 class="text-3xl font-bold text-slate-900 mt-2">
                        {{ $stats['total'] ?? 0 }}
                    </h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-indigo-50
                            flex items-center justify-center text-lg
                            group-hover:scale-110 transition">
                    📅
                </div>
            </div>
        </div>

        {{-- Pending --}}
        <div class="group bg-white rounded-2xl border border-slate-200/70 p-5
                    hover:shadow-lg hover:shadow-slate-200/60
                    hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Pending
                    </p>
                    <h2 class="text-3xl font-bold text-amber-600 mt-2">
                        {{ $stats['pending'] ?? 0 }}
                    </h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-50
                            flex items-center justify-center text-lg
                            group-hover:scale-110 transition">
                    ⏳
                </div>
            </div>
        </div>

        {{-- Confirmed --}}
        <div class="group bg-white rounded-2xl border border-slate-200/70 p-5
                    hover:shadow-lg hover:shadow-slate-200/60
                    hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Confirmed
                    </p>
                    <h2 class="text-3xl font-bold text-emerald-600 mt-2">
                        {{ $stats['confirmed'] ?? 0 }}
                    </h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50
                            flex items-center justify-center text-lg
                            group-hover:scale-110 transition">
                    ✅
                </div>
            </div>
        </div>

        {{-- Cancelled --}}
        <div class="group bg-white rounded-2xl border border-slate-200/70 p-5
                    hover:shadow-lg hover:shadow-slate-200/60
                    hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Cancelled
                    </p>
                    <h2 class="text-3xl font-bold text-rose-600 mt-2">
                        {{ $stats['cancelled'] ?? 0 }}
                    </h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-rose-50
                            flex items-center justify-center text-lg
                            group-hover:scale-110 transition">
                    ✕
                </div>
            </div>
        </div>

    </div>


    {{-- ============ FILTERS + TABLE ============ --}}
    <div class="bg-white rounded-2xl border border-slate-200/70
                shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="p-5 border-b border-slate-100
                    flex flex-col lg:flex-row lg:items-center
                    gap-3 lg:justify-between">

            <div class="relative max-w-sm w-full">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center
                            pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none"
                         stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                    </svg>
                </div>
                <input type="text"
                       placeholder="Search reservations..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl
                              bg-slate-50 border border-slate-200
                              text-sm text-slate-700 placeholder-slate-400
                              focus:bg-white focus:border-indigo-400
                              focus:ring-2 focus:ring-indigo-500/20
                              outline-none transition">
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <select class="px-3.5 py-2.5 rounded-xl
                               bg-slate-50 border border-slate-200
                               text-sm text-slate-700
                               focus:bg-white focus:border-indigo-400
                               focus:ring-2 focus:ring-indigo-500/20
                               outline-none transition cursor-pointer">
                    <option>All Status</option>
                    <option>Pending</option>
                    <option>Confirmed</option>
                    <option>Cancelled</option>
                    <option>Completed</option>
                </select>
            </div>

        </div>


        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50/70 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold
                                   uppercase tracking-wider text-slate-500">
                            Guest
                        </th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold
                                   uppercase tracking-wider text-slate-500">
                            Hotel
                        </th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold
                                   uppercase tracking-wider text-slate-500">
                            Date & Time
                        </th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold
                                   uppercase tracking-wider text-slate-500">
                            Guests
                        </th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold
                                   uppercase tracking-wider text-slate-500">
                            Tables
                        </th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold
                                   uppercase tracking-wider text-slate-500">
                            Status
                        </th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold
                                   uppercase tracking-wider text-slate-500">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">

                    @forelse($reservations ?? [] as $reservation)

                        <tr class="hover:bg-slate-50/60 transition-colors">

                            {{-- Guest --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full
                                                bg-gradient-to-br from-indigo-100 to-violet-100
                                                flex items-center justify-center
                                                text-indigo-700 font-semibold text-sm
                                                flex-shrink-0">
                                        {{ strtoupper(substr($reservation->user->name ?? 'G', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">
                                            {{ $reservation->user->name ?? 'Guest' }}
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            #{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Hotel --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">🏨</span>
                                    <span class="text-slate-700 font-medium">
                                        {{ $reservation->hotel->name ?? '—' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Date & Time --}}
                            <td class="px-6 py-4">
                                <p class="text-slate-800 font-medium">
                                    {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('M d, Y') }}
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('h:i A') }}
                                </p>
                            </td>

                            {{-- Guests --}}
                            <td class="px-6 py-4">
                                <div class="inline-flex items-center gap-1.5
                                            px-2.5 py-1 rounded-lg
                                            bg-slate-100 text-slate-700
                                            text-xs font-medium">
                                    <span>👥</span>
                                    {{ $reservation->number_of_guests }}
                                </div>
                            </td>

                            {{-- Tables --}}
                            <td class="px-6 py-4">
                                @if($reservation->tables->count())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($reservation->tables->take(3) as $table)
                                            <span class="inline-flex items-center
                                                         px-2 py-0.5 rounded-md
                                                         bg-indigo-50 text-indigo-700
                                                         text-xs font-medium">
                                                {{ $table->table_number }}
                                            </span>
                                        @endforeach
                                        @if($reservation->tables->count() > 3)
                                            <span class="text-xs text-slate-400">
                                                +{{ $reservation->tables->count() - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                @php
                                    $status = $reservation->status ?? 'pending';
                                    $config = [
                                        'pending'   => ['bg-amber-50 text-amber-700 border-amber-200',    '⏳ Pending',   'bg-amber-500'],
                                        'confirmed' => ['bg-emerald-50 text-emerald-700 border-emerald-200','✓ Confirmed','bg-emerald-500'],
                                        'cancelled' => ['bg-rose-50 text-rose-700 border-rose-200',       '✕ Cancelled', 'bg-rose-500'],
                                        'completed' => ['bg-sky-50 text-sky-700 border-sky-200',          '✓ Completed', 'bg-sky-500'],
                                    ];
                                    [$classes, $label, $dot] = $config[$status] ?? $config['pending'];
                                @endphp
                                <span class="inline-flex items-center gap-1.5
                                             px-2.5 py-1 rounded-lg
                                             border text-xs font-medium
                                             {{ $classes }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                                    {{ $label }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('reservations.show', $reservation->id) }}"
                                       class="p-2 rounded-lg
                                              text-slate-400 hover:text-indigo-600
                                              hover:bg-indigo-50 transition"
                                       title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="inline-flex items-center justify-center
                                            w-16 h-16 rounded-2xl bg-slate-100
                                            text-3xl mb-4">
                                    📅
                                </div>
                                <h3 class="text-lg font-semibold text-slate-800">
                                    No reservations found
                                </h3>
                                <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                                    Get started by creating your first reservation.
                                </p>
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>


        {{-- Pagination --}}
        @if(isset($reservations) && method_exists($reservations, 'links') && $reservations->count())
            <div class="px-6 py-4 border-t border-slate-100
                        flex flex-col sm:flex-row
                        items-center justify-between gap-3">
                <p class="text-xs text-slate-500">
                    Showing
                    <span class="font-medium text-slate-700">{{ $reservations->firstItem() }}</span>
                    to
                    <span class="font-medium text-slate-700">{{ $reservations->lastItem() }}</span>
                    of
                    <span class="font-medium text-slate-700">{{ $reservations->total() }}</span>
                    results
                </p>
                <div>{{ $reservations->links() }}</div>
            </div>
        @endif

    </div>

@endsection
