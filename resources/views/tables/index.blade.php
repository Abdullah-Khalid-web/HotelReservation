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
                <span class="text-slate-600 font-medium">Tables</span>
            </div>

            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">
                Tables
            </h1>

            <p class="text-slate-500 mt-1.5 text-sm">
                Manage all tables across your hotels and their availability.
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

            @can('create tables')
                <a href="{{ route('tables.create') }}"
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

                    Add Table

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

            <p class="text-sm font-medium">
                {{ session('success') }}
            </p>

        </div>
    @endif


    {{-- ============ STATS ROW ============ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">

        {{-- Total Tables --}}
        <div class="group bg-white rounded-2xl border border-slate-200/70 p-5
                    hover:shadow-lg hover:shadow-slate-200/60
                    hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Total Tables
                    </p>
                    <h2 class="text-3xl font-bold text-slate-900 mt-2">
                        {{ $stats['total'] ?? 0 }}
                    </h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-indigo-50
                            flex items-center justify-center text-lg
                            group-hover:scale-110 transition">
                    🪑
                </div>
            </div>
        </div>

        {{-- Available --}}
        <div class="group bg-white rounded-2xl border border-slate-200/70 p-5
                    hover:shadow-lg hover:shadow-slate-200/60
                    hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Available
                    </p>
                    <h2 class="text-3xl font-bold text-emerald-600 mt-2">
                        {{ $stats['available'] ?? 0 }}
                    </h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50
                            flex items-center justify-center text-lg
                            group-hover:scale-110 transition">
                    ✅
                </div>
            </div>
        </div>

        {{-- Reserved --}}
        <div class="group bg-white rounded-2xl border border-slate-200/70 p-5
                    hover:shadow-lg hover:shadow-slate-200/60
                    hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Reserved
                    </p>
                    <h2 class="text-3xl font-bold text-amber-600 mt-2">
                        {{ $stats['reserved'] ?? 0 }}
                    </h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-50
                            flex items-center justify-center text-lg
                            group-hover:scale-110 transition">
                    ⏳
                </div>
            </div>
        </div>

        {{-- Maintenance --}}
        <div class="group bg-white rounded-2xl border border-slate-200/70 p-5
                    hover:shadow-lg hover:shadow-slate-200/60
                    hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Maintenance
                    </p>
                    <h2 class="text-3xl font-bold text-slate-600 mt-2">
                        {{ $stats['maintenance'] ?? 0 }}
                    </h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-slate-100
                            flex items-center justify-center text-lg
                            group-hover:scale-110 transition">
                    ⚙️
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

            {{-- Search --}}
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
                       placeholder="Search tables, hotels..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl
                              bg-slate-50 border border-slate-200
                              text-sm text-slate-700 placeholder-slate-400
                              focus:bg-white focus:border-indigo-400
                              focus:ring-2 focus:ring-indigo-500/20
                              outline-none transition">

            </div>

            {{-- Filters --}}
            <div class="flex flex-wrap items-center gap-2">

                <select class="px-3.5 py-2.5 rounded-xl
                               bg-slate-50 border border-slate-200
                               text-sm text-slate-700
                               focus:bg-white focus:border-indigo-400
                               focus:ring-2 focus:ring-indigo-500/20
                               outline-none transition cursor-pointer">
                    <option>All Hotels</option>
                    @foreach($hotels ?? [] as $hotel)
                        <option>{{ $hotel->name }}</option>
                    @endforeach
                </select>

                <select class="px-3.5 py-2.5 rounded-xl
                               bg-slate-50 border border-slate-200
                               text-sm text-slate-700
                               focus:bg-white focus:border-indigo-400
                               focus:ring-2 focus:ring-indigo-500/20
                               outline-none transition cursor-pointer">
                    <option>All Status</option>
                    <option>Available</option>
                    <option>Reserved</option>
                    <option>Maintenance</option>
                </select>

            </div>

        </div>


        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50/70 border-b border-slate-100">

                    <tr>
                        <th class="px-6 py-3.5 text-left">
                            <input type="checkbox"
                                   class="rounded border-slate-300 text-indigo-600
                                          focus:ring-indigo-500">
                        </th>

                        <th class="px-6 py-3.5 text-left text-xs font-semibold
                                   uppercase tracking-wider text-slate-500">
                            Table
                        </th>

                        <th class="px-6 py-3.5 text-left text-xs font-semibold
                                   uppercase tracking-wider text-slate-500">
                            Hotel
                        </th>

                        <th class="px-6 py-3.5 text-left text-xs font-semibold
                                   uppercase tracking-wider text-slate-500">
                            Capacity
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

                    @forelse($tables ?? [] as $table)

                        <tr class="hover:bg-slate-50/60 transition-colors">

                            {{-- Checkbox --}}
                            <td class="px-6 py-4">
                                <input type="checkbox"
                                       class="rounded border-slate-300 text-indigo-600
                                              focus:ring-indigo-500">
                            </td>

                            {{-- Table --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">

                                    <div class="w-10 h-10 rounded-xl
                                                bg-gradient-to-br
                                                from-indigo-50 to-violet-50
                                                border border-indigo-100
                                                flex items-center justify-center
                                                text-base">
                                        🪑
                                    </div>

                                    <div>
                                        <p class="font-semibold text-slate-800">
                                            Table {{ $table->table_number ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            ID: #{{ str_pad($table->id ?? 0, 4, '0', STR_PAD_LEFT) }}
                                        </p>
                                    </div>

                                </div>
                            </td>

                            {{-- Hotel --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">🏨</span>
                                    <span class="text-slate-700 font-medium">
                                        {{ $table->hotel->name ?? 'Unassigned' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Capacity --}}
                            <td class="px-6 py-4">
                                <div class="inline-flex items-center gap-1.5
                                            px-2.5 py-1 rounded-lg
                                            bg-slate-100 text-slate-700
                                            text-xs font-medium">
                                    <span>👥</span>
                                    {{ $table->capacity ?? 0 }} seats
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">

                                @php
                                    $status = $table->status ?? 'available';
                                    $statusConfig = [
                                        'available'   => ['bg-emerald-50 text-emerald-700 border-emerald-200', '✓ Available',   'bg-emerald-500'],
                                        'reserved'    => ['bg-amber-50 text-amber-700 border-amber-200',       '⏳ Reserved',   'bg-amber-500'],
                                        'maintenance' => ['bg-slate-100 text-slate-600 border-slate-200',      '⚙ Maintenance', 'bg-slate-400'],
                                    ];
                                    [$classes, $label, $dot] = $statusConfig[$status] ?? $statusConfig['available'];
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

                                    <button class="p-2 rounded-lg
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
                                    </button>

                                    @can('edit tables')
                                        <button class="p-2 rounded-lg
                                                       text-slate-400 hover:text-amber-600
                                                       hover:bg-amber-50 transition"
                                                title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                 stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                    @endcan

                                    @can('delete tables')
                                        <button class="p-2 rounded-lg
                                                       text-slate-400 hover:text-rose-600
                                                       hover:bg-rose-50 transition"
                                                title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                 stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                            </svg>
                                        </button>
                                    @endcan

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">

                                <div class="inline-flex items-center justify-center
                                            w-16 h-16 rounded-2xl bg-slate-100
                                            text-3xl mb-4">
                                    🪑
                                </div>

                                <h3 class="text-lg font-semibold text-slate-800">
                                    No tables found
                                </h3>

                                <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                                    Get started by adding your first table or adjust your filters.
                                </p>

                                @can('create tables')
                                    <a href="{{ route('tables.create') }}"
                                       class="mt-6 inline-flex items-center gap-2
                                              px-5 py-2.5 rounded-xl
                                              bg-gradient-to-r from-indigo-500 to-violet-600
                                              text-sm font-medium text-white
                                              hover:shadow-lg hover:shadow-indigo-500/30
                                              active:scale-[0.98]
                                              transition shadow-md">

                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M12 4v16m8-8H4"/>
                                        </svg>

                                        Add Your First Table

                                    </a>
                                @endcan

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ============ PAGINATION ============ --}}
        @if(isset($tables) && method_exists($tables, 'links') && $tables->count())

            <div class="px-6 py-4 border-t border-slate-100
                        flex flex-col sm:flex-row
                        items-center justify-between gap-3">

                <p class="text-xs text-slate-500">
                    Showing
                    <span class="font-medium text-slate-700">{{ $tables->firstItem() }}</span>
                    to
                    <span class="font-medium text-slate-700">{{ $tables->lastItem() }}</span>
                    of
                    <span class="font-medium text-slate-700">{{ $tables->total() }}</span>
                    results
                </p>

                <div>
                    {{ $tables->links() }}
                </div>

            </div>

        @endif

    </div>

@endsection
