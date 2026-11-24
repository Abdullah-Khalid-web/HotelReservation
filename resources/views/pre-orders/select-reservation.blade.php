@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- ═══════════ HEADER ═══════════ --}}
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('pre-orders.index') }}"
           class="flex items-center justify-center w-11 h-11 rounded-xl
                  bg-white border border-slate-200 text-slate-600
                  hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50
                  shadow-sm transition flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>

        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('pre-orders.index') }}" class="hover:text-indigo-600">Pre-Orders</a>
                <span>/</span>
                <span class="text-slate-700">New</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">
                Add Pre-Order
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Choose a confirmed reservation to add a food pre-order.
            </p>
        </div>
    </div>


    {{-- ═══════════ NO RESERVATIONS AT ALL ═══════════ --}}
    @if($available->isEmpty() && $taken->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl
                        bg-gradient-to-br from-amber-100 to-orange-100 text-4xl mb-4">
                📅
            </div>
            <p class="text-base font-semibold text-slate-800">
                No confirmed reservations available
            </p>
            <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                You need a confirmed reservation (from today onwards) to place a pre-order.
                Create a reservation first, then come back.
            </p>

            <div class="flex items-center justify-center gap-3 mt-5 flex-wrap">
                @can('create reservations')
                    <a href="{{ route('reservations.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                              bg-gradient-to-r from-indigo-500 to-violet-600
                              text-sm font-semibold text-white
                              hover:shadow-lg hover:shadow-indigo-500/30
                              active:scale-[0.98] transition shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 4v16m8-8H4"/>
                        </svg>
                        New Reservation
                    </a>
                @endcan

                <a href="{{ route('reservations.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                          bg-white border border-slate-300 text-slate-700
                          text-sm font-semibold
                          hover:bg-slate-50 transition">
                    View All Reservations
                </a>
            </div>
        </div>


    {{-- ═══════════ HAS RESERVATIONS ═══════════ --}}
    @else

        {{-- Search --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-5">
            <form method="GET" class="flex gap-3">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by reservation ID or hotel name…"
                       class="flex-1 rounded-xl border border-slate-300 bg-white
                              px-4 py-2.5 text-sm
                              focus:border-indigo-500 focus:ring-2
                              focus:ring-indigo-200 outline-none transition">
                <button type="submit"
                        class="rounded-xl bg-slate-900 text-white
                               px-5 py-2.5 text-sm font-semibold
                               hover:bg-slate-800 transition">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('pre-orders.select-reservation') }}"
                       class="rounded-xl border border-slate-300 text-slate-700
                              px-5 py-2.5 text-sm font-semibold
                              hover:bg-slate-50 transition">
                        Clear
                    </a>
                @endif
            </form>
        </div>


        {{-- ═══════════ AVAILABLE RESERVATIONS (can add pre-order) ═══════════ --}}
        @if($available->isNotEmpty())
            <div class="mb-6">
                <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider mb-3 px-1">
                    Available for Pre-Order ({{ $available->count() }})
                </h2>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <form action="" method="GET" id="reservation-select-form">
                        <div class="divide-y divide-slate-100">

                            @foreach($available as $reservation)
                                <label class="flex items-center gap-4 p-4
                                              hover:bg-indigo-50/50 transition cursor-pointer
                                              has-[:checked]:bg-indigo-50
                                              has-[:checked]:border-l-4
                                              has-[:checked]:border-indigo-500">

                                    <input type="radio"
                                           name="reservation_id"
                                           value="{{ $reservation->id }}"
                                           class="w-4 h-4 text-indigo-600
                                                  border-slate-300
                                                  focus:ring-indigo-500">

                                    {{-- Hotel icon --}}
                                    <div class="w-12 h-12 rounded-xl
                                                bg-gradient-to-br from-indigo-100 to-violet-100
                                                flex items-center justify-center text-xl flex-shrink-0">
                                        🏨
                                    </div>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-slate-900 truncate">
                                            {{ $reservation->hotel?->name ?? 'Unknown Hotel' }}
                                        </p>
                                        <div class="flex items-center gap-3 mt-1 text-xs text-slate-500 flex-wrap">
                                            <span>
                                                📅 {{ $reservation->reservation_date->format('D, M d, Y') }}
                                            </span>
                                            <span>
                                                🕐 {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('h:i A') }}
                                                @if($reservation->reservation_end_time)
                                                    – {{ \Carbon\Carbon::parse($reservation->reservation_end_time)->format('h:i A') }}
                                                @endif
                                            </span>
                                            <span>
                                                👥 {{ $reservation->number_of_guests }} guests
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Reservation # --}}
                                    <div class="flex-shrink-0 text-right">
                                        <p class="text-xs text-slate-400">Reservation</p>
                                        <p class="text-sm font-bold text-slate-700">
                                            #{{ $reservation->id }}
                                        </p>
                                    </div>

                                </label>
                            @endforeach

                        </div>

                        {{-- Sticky footer with Continue --}}
                        <div class="px-4 py-3 bg-slate-50 border-t border-slate-200
                                    flex items-center justify-end gap-3">
                            <p class="text-xs text-slate-500 mr-auto" id="selection-hint">
                                Select a reservation to continue.
                            </p>
                            <button type="submit"
                                    id="continue-btn"
                                    disabled
                                    class="inline-flex items-center gap-2
                                           px-5 py-2.5 rounded-xl
                                           bg-gradient-to-r from-indigo-500 to-violet-600
                                           text-sm font-semibold text-white
                                           hover:shadow-lg hover:shadow-indigo-500/30
                                           active:scale-[0.98] transition shadow-md
                                           disabled:opacity-50 disabled:cursor-not-allowed">
                                Continue to Menu
                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                     stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif


        {{-- ═══════════ RESERVATIONS ALREADY WITH PRE-ORDER (info only) ═══════════ --}}
        @if($taken->isNotEmpty())
            <div>
                <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider mb-3 px-1">
                    Already Have Pre-Order ({{ $taken->count() }})
                </h2>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="divide-y divide-slate-100">

                        @foreach($taken as $reservation)
                            <a href="{{ route('pre-orders.show', $reservation->preOrder) }}"
                               class="flex items-center gap-4 p-4
                                      hover:bg-slate-50 transition">

                                <div class="w-12 h-12 rounded-xl
                                            bg-slate-100 text-slate-500
                                            flex items-center justify-center text-xl flex-shrink-0">
                                    ✅
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 truncate">
                                        {{ $reservation->hotel?->name ?? 'Unknown Hotel' }}
                                    </p>
                                    <div class="flex items-center gap-3 mt-1 text-xs text-slate-500 flex-wrap">
                                        <span>
                                            📅 {{ $reservation->reservation_date->format('M d, Y') }}
                                        </span>
                                        <span>
                                            🕐 {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('h:i A') }}
                                        </span>
                                        <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-medium
                                                     bg-{{ $reservation->preOrder->statusColor() }}-100
                                                     text-{{ $reservation->preOrder->statusColor() }}-700">
                                            Pre-order: {{ ucfirst($reservation->preOrder->status) }}
                                        </span>
                                    </div>
                                </div>

                                <svg class="w-4 h-4 text-slate-400 flex-shrink-0"
                                     fill="none" stroke="currentColor"
                                     stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endforeach

                    </div>
                </div>
            </div>
        @endif

    @endif

</div>


{{-- ═══════════ JS: enable Continue button when a reservation is selected ═══════════ --}}
@if($available->isNotEmpty())
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form       = document.getElementById('reservation-select-form');
        const btn        = document.getElementById('continue-btn');
        const hint       = document.getElementById('selection-hint');
        const radios     = form.querySelectorAll('input[name="reservation_id"]');

        function refresh() {
            const selected = form.querySelector('input[name="reservation_id"]:checked');
            if (selected) {
                btn.disabled   = false;
                hint.textContent = `Reservation #${selected.value} selected. Click Continue.`;
                hint.className = 'text-xs text-emerald-600 mr-auto font-medium';
            } else {
                btn.disabled   = true;
                hint.textContent = 'Select a reservation to continue.';
                hint.className = 'text-xs text-slate-500 mr-auto';
            }
        }

        radios.forEach(r => r.addEventListener('change', refresh));
        refresh();

        // Update form action dynamically when radio changes
        form.addEventListener('submit', (e) => {
            const selected = form.querySelector('input[name="reservation_id"]:checked');
            if (!selected) {
                e.preventDefault();
                return;
            }
            // Redirect to pre-orders.create with selected reservation
            e.preventDefault();
            window.location.href = `/reservations/${selected.value}/pre-order`;
        });
    });
</script>
@endif

@endsection
