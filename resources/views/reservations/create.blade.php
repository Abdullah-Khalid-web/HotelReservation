@extends('layouts.app')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

{{-- ============================================================
     SCRIPT — must be defined BEFORE x-data initializes.
     Guard prevents Alpine from double-defining it on retries.
============================================================ --}}
<script>
    if (typeof window.reservationForm === 'undefined') {
        window.reservationForm = function () {
            return {
                loading: false,
                searched: false,
                searchTimer: null,
                searchAbort: null,
                searchId: 0,
                mounted: false,

                form: {
                    hotel_id: '',
                    reservation_date: '{{ date('Y-m-d') }}',
                    reservation_time: '19:00',
                    reservation_end_time: '',
                    number_of_guests: 2,
                },

                results: {
                    start_time: '',
                    end_time: '',
                    tables: [],
                },

                init() {
                    // Guard against double-mount on same DOM node
                    if (this.mounted) {
                        console.warn('[reservationForm] Already mounted — skipping re-init');
                        return;
                    }
                    this.mounted = true;
                    console.log('[reservationForm] Mounted at', new Date().toISOString());
                },

                autoSearch() {
                    clearTimeout(this.searchTimer);
                    this.searchTimer = setTimeout(() => this.runSearch(), 300);
                },

                async runSearch() {
                    if (!this.form.hotel_id
                        || !this.form.reservation_date
                        || !this.form.reservation_time
                        || !this.form.number_of_guests) {
                        this.searched = false;
                        this.results = { start_time: '', end_time: '', tables: [] };
                        return;
                    }

                    if (this.searchAbort) this.searchAbort.abort();
                    this.searchAbort = new AbortController();

                    this.searchId++;
                    const currentSearchId = this.searchId;

                    // Reset BEFORE fetch
                    this.searched = false;
                    this.results = { start_time: '', end_time: '', tables: [] };
                    this.loading = true;

                    try {
                        const response = await fetch('{{ route('reservations.search') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(this.form),
                            signal: this.searchAbort.signal,
                        });

                        const data = await response.json();
                        if (currentSearchId !== this.searchId) {
                            console.log('[search] Stale response ignored (id ' + currentSearchId + ')');
                            return;
                        }
                        if (!response.ok) { this.searched = false; return; }

                        console.log('[search] RAW response tables:', data.tables);

                        // Strict dedupe using Map
                        const map = new Map();
                        (data.tables || []).forEach(t => {
                            if (t && t.id != null) map.set(t.id, t);
                        });
                        const uniqueTables = Array.from(map.values());

                        console.log('[search] DEDUPED tables:', uniqueTables);

                        // Single assignment
                        this.results = {
                            start_time: data.start_time,
                            end_time:   data.end_time,
                            tables:     uniqueTables,
                        };
                        this.searched = true;

                        // Post-render DOM audit
                        this.$nextTick(() => {
                            const cards = document.querySelectorAll('input[name="tables[]"]');
                            console.log('[search] DOM checkbox count after render:', cards.length);
                            console.log('[search] State table count:', this.results.tables.length);
                            if (cards.length !== this.results.tables.length) {
                                console.error('[search] MISMATCH! DOM has ' + cards.length + ' but state has ' + this.results.tables.length);
                            }
                        });
                    } catch (e) {
                        if (e.name === 'AbortError') return;
                        if (currentSearchId !== this.searchId) return;
                        console.error('Search error:', e);
                        this.searched = false;
                    } finally {
                        if (currentSearchId === this.searchId) {
                            this.loading = false;
                        }
                        this.searchAbort = null;
                    }
                },

                computedDuration() {
                    const start = this.form.reservation_time;
                    const end   = this.form.reservation_end_time;
                    if (!start) return '—';
                    if (!end) return '2 hours (default)';

                    const [sh, sm] = start.split(':').map(Number);
                    const [eh, em] = end.split(':').map(Number);
                    let startMin = sh * 60 + sm;
                    let endMin   = eh * 60 + em;

                    if (endMin === startMin) return '2 hours (default)';
                    if (endMin < startMin) endMin += 24 * 60;

                    const diff = endMin - startMin;
                    if (diff > 12 * 60) return '2 hours (default)';

                    const hours   = Math.floor(diff / 60);
                    const minutes = diff % 60;
                    if (minutes === 0) return `${hours} ${hours === 1 ? 'hour' : 'hours'}`;
                    return `${hours}h ${minutes}m`;
                },

                formatTime(t) {
                    if (!t) return '';
                    const [h, m] = t.split(':').map(Number);
                    const ampm = h >= 12 ? 'PM' : 'AM';
                    const hh = h % 12 || 12;
                    return `${String(hh).padStart(2, '0')}:${String(m).padStart(2, '0')} ${ampm}`;
                },
            };
        };
    }
</script>


<div class="max-w-5xl mx-auto" x-data="reservationForm()" x-cloak>

    {{-- ============ PAGE HEADER ============ --}}
    <div class="mb-6 flex items-center gap-4">

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
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <a href="{{ route('reservations.index') }}" class="hover:text-indigo-600">Reservations</a>
                <span>/</span>
                <span class="text-slate-700">New</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">New Reservation</h1>
            <p class="text-sm text-slate-500 mt-1">
                Pick a hotel, time range, and party size — we'll find the tables.
            </p>
        </div>

    </div>


    @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
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


    {{-- ============ SEARCH PANEL ============ --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-5">

        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-3">

            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl
                            bg-indigo-100 text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-slate-900">
                        Find a table
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Results update automatically as you change the fields.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 text-xs">

                <template x-if="loading">
                    <span class="inline-flex items-center gap-1.5 text-indigo-600 font-medium">
                        <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="3" opacity="0.25"/>
                            <path fill="currentColor" opacity="0.75"
                                  d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                        </svg>
                        Searching…
                    </span>
                </template>

                <template x-if="!loading && searched">
                    <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span x-text="`${results.tables.length} table(s) found`"></span>
                    </span>
                </template>

                <template x-if="!loading && !searched">
                    <span class="inline-flex items-center gap-1.5 text-slate-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                        Waiting for input
                    </span>
                </template>

            </div>

        </div>


        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

            {{-- Hotel --}}
            <div class="lg:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Hotel <span class="text-rose-500">*</span>
                </label>
                <select x-model="form.hotel_id"
                        @change="autoSearch()"
                        class="w-full rounded-xl border border-slate-300 bg-white
                               px-4 py-2.5 text-sm text-slate-800 shadow-sm
                               focus:border-indigo-500 focus:ring-2
                               focus:ring-indigo-200 outline-none transition">
                    <option value="">Select a hotel</option>
                    @foreach($hotels ?? [] as $hotel)
                        <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Date --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Date <span class="text-rose-500">*</span>
                </label>
                <input type="date"
                       x-model="form.reservation_date"
                       @change="autoSearch()"
                       min="{{ date('Y-m-d') }}"
                       value="{{ date('Y-m-d') }}"
                       class="w-full rounded-xl border border-slate-300 bg-white
                              px-4 py-2.5 text-sm text-slate-800 shadow-sm
                              focus:border-indigo-500 focus:ring-2
                              focus:ring-indigo-200 outline-none transition">
            </div>

            {{-- Start Time --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Start time <span class="text-rose-500">*</span>
                </label>
                <input type="time"
                       x-model="form.reservation_time"
                       @change="autoSearch()"
                       value="19:00"
                       class="w-full rounded-xl border border-slate-300 bg-white
                              px-4 py-2.5 text-sm text-slate-800 shadow-sm
                              focus:border-indigo-500 focus:ring-2
                              focus:ring-indigo-200 outline-none transition">
            </div>

            {{-- End Time --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    End time
                </label>
                <input type="time"
                       x-model="form.reservation_end_time"
                       @change="autoSearch()"
                       class="w-full rounded-xl border border-slate-300 bg-white
                              px-4 py-2.5 text-sm text-slate-800 shadow-sm
                              focus:border-indigo-500 focus:ring-2
                              focus:ring-indigo-200 outline-none transition">
                <p class="mt-1.5 text-xs text-slate-400">
                    Empty → <strong>2 hours</strong> default
                </p>
            </div>

            {{-- Guests --}}
            <div class="lg:col-span-3">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Guests <span class="text-rose-500">*</span>
                </label>
                <input type="number"
                       x-model.number="form.number_of_guests"
                       @input.debounce.400ms="autoSearch()"
                       min="1" max="50" value="2"
                       class="w-full rounded-xl border border-slate-300 bg-white
                              px-4 py-2.5 text-sm text-slate-800 shadow-sm
                              focus:border-indigo-500 focus:ring-2
                              focus:ring-indigo-200 outline-none transition">
            </div>

            {{-- Duration preview --}}
            <div class="lg:col-span-2 flex items-end">
                <div class="w-full rounded-xl bg-indigo-50 border border-indigo-100
                            px-4 py-3 flex items-center gap-3">

                    <div class="flex items-center justify-center w-9 h-9 rounded-lg
                                bg-indigo-100 text-indigo-600 flex-shrink-0">
                        ⏱️
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-medium uppercase tracking-wider text-indigo-700">
                            Duration
                        </p>
                        <p class="text-sm font-semibold text-indigo-900 mt-0.5"
                           x-text="computedDuration()">
                        </p>
                    </div>

                </div>
            </div>

        </div>

    </div>


    {{-- ============ RESULTS ============ --}}
    <form action="{{ route('reservations.store') }}" method="POST">
        @csrf

        <input type="hidden" name="hotel_id"             :value="form.hotel_id">
        <input type="hidden" name="reservation_date"     :value="form.reservation_date">
        <input type="hidden" name="reservation_time"     :value="form.reservation_time">
        <input type="hidden" name="reservation_end_time" :value="form.reservation_end_time">
        <input type="hidden" name="number_of_guests"     :value="form.number_of_guests">


        {{-- Placeholder --}}
        <template x-if="!searched">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-10 text-center">
                <div class="inline-flex items-center justify-center
                            w-14 h-14 rounded-2xl bg-slate-100 text-2xl mb-3">
                    🔍
                </div>
                <p class="text-sm font-medium text-slate-700">
                    Fill in the fields above
                </p>
                <p class="text-xs text-slate-500 mt-1">
                    Available tables will appear here automatically.
                </p>
            </div>
        </template>


        {{-- Results --}}
        <template x-if="searched">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl
                                bg-emerald-100 text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M4 10h16M5 10v7a2 2 0 002 2h10a2 2 0 002-2v-7M8 10V7a4 4 0 018 0v3"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Available Tables
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            <span x-text="formatTime(results.start_time)"></span>
                            <span class="mx-1">→</span>
                            <span x-text="formatTime(results.end_time)"></span>
                            <span class="mx-1">·</span>
                            <span x-text="computedDuration()"></span>
                        </p>
                    </div>
                </div>


                <div class="p-5">

                    {{-- Empty --}}
                    <template x-if="results.tables.length === 0">
                        <div class="text-center py-10">
                            <div class="inline-flex items-center justify-center
                                        w-14 h-14 rounded-2xl bg-slate-100 text-2xl mb-3">
                                🪑
                            </div>
                            <p class="text-sm font-medium text-slate-700">
                                No tables available for this slot.
                            </p>
                            <p class="text-xs text-slate-500 mt-1">
                                Try a different time or party size.
                            </p>
                        </div>
                    </template>


                    {{-- Tables grid --}}
                    <template x-if="results.tables.length > 0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                            <template x-for="table in results.tables" :key="table.id">
                                <label class="cursor-pointer">
                                    <input type="checkbox"
                                           name="tables[]"
                                           :value="table.id"
                                           class="peer sr-only">

                                    <div class="rounded-xl border-2 border-slate-200 bg-white p-4
                                                hover:border-indigo-300
                                                peer-checked:border-indigo-500
                                                peer-checked:bg-indigo-50
                                                peer-checked:shadow-md transition">

                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600
                                                        flex items-center justify-center text-base">🪑</div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold text-slate-800 truncate">
                                                    Table <span x-text="table.table_number"></span>
                                                </p>
                                                <p class="text-xs text-slate-500 mt-0.5">
                                                    <span x-text="table.capacity"></span> seats
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </label>
                            </template>

                        </div>
                    </template>


                    {{-- Notes + Submit --}}
                    <div class="mt-6 pt-5 border-t border-slate-100 space-y-4">

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Notes (optional)
                            </label>
                            <textarea name="notes" rows="2"
                                      placeholder="Special requests, allergies, etc."
                                      class="w-full rounded-xl border border-slate-300 bg-white
                                             px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                             placeholder-slate-400 resize-none
                                             focus:border-indigo-500 focus:ring-2
                                             focus:ring-indigo-200 outline-none transition"></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    :disabled="results.tables.length === 0"
                                    class="inline-flex items-center gap-2
                                           px-6 py-2.5 rounded-xl
                                           bg-gradient-to-r from-indigo-500 to-violet-600
                                           text-sm font-semibold text-white
                                           hover:shadow-lg hover:shadow-indigo-500/30
                                           active:scale-[0.98]
                                           transition shadow-md
                                           disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                     stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M5 13l4 4L19 7"/>
                                </svg>
                                Confirm Reservation
                            </button>
                        </div>

                    </div>

                </div>

            </div>
        </template>

    </form>

</div>

@endsection
