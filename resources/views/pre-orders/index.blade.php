@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- ═══════════════════════ HEADER ═══════════════════════ --}}
    <div class="mb-6 flex items-start justify-between gap-4 flex-wrap">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Pre-Orders</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Pre-Orders</h1>
            <p class="text-sm text-slate-500 mt-1">
                Manage food orders placed with reservations.
            </p>
        </div>

        @can('create pre-orders')
            <a href="{{ route('pre-orders.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                      bg-gradient-to-r from-indigo-500 to-violet-600
                      text-sm font-semibold text-white
                      hover:shadow-lg hover:shadow-indigo-500/30
                      active:scale-[0.98] transition shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                     stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 4v16m8-8H4"/>
                </svg>
                Add Pre-Order
            </a>
        @endcan
    </div>


    {{-- ═══════════════════════ FLASH ═══════════════════════ --}}
    @if(session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3
                    text-sm text-emerald-800 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                 stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3
                    text-sm text-rose-800 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                 stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 8v4m0 4h.01"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="mb-5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3
                    text-sm text-blue-800 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                 stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M13 16h-1v-4h-1m1-4h.01"/>
            </svg>
            {{ session('info') }}
        </div>
    @endif


    {{-- ═══════════════════════ STATS ═══════════════════════ --}}
    @php
        $stats = $stats ?? [
            'pending'   => 0,
            'confirmed' => 0,
            'preparing' => 0,
        ];
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">

        {{-- Total --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600
                            flex items-center justify-center text-sm">
                    📊
                </div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">
                    Total
                </p>
            </div>
            <p class="text-2xl font-bold text-slate-900 mt-2">
                {{ $preOrders->total() }}
            </p>
        </div>

        {{-- Pending --}}
        <a href="{{ route('pre-orders.index', ['status' => 'pending']) }}"
           class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4
                  hover:border-amber-300 hover:shadow-md transition
                  {{ request('status') === 'pending' ? 'ring-2 ring-amber-300' : '' }}">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600
                            flex items-center justify-center text-sm">
                    ⏳
                </div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">
                    Pending
                </p>
            </div>
            <p class="text-2xl font-bold text-slate-900 mt-2">
                {{ $stats['pending'] }}
            </p>
        </a>

        {{-- Confirmed --}}
        <a href="{{ route('pre-orders.index', ['status' => 'confirmed']) }}"
           class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4
                  hover:border-blue-300 hover:shadow-md transition
                  {{ request('status') === 'confirmed' ? 'ring-2 ring-blue-300' : '' }}">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600
                            flex items-center justify-center text-sm">
                    ✅
                </div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">
                    Confirmed
                </p>
            </div>
            <p class="text-2xl font-bold text-slate-900 mt-2">
                {{ $stats['confirmed'] }}
            </p>
        </a>

        {{-- Preparing --}}
        <a href="{{ route('pre-orders.index', ['status' => 'preparing']) }}"
           class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4
                  hover:border-indigo-300 hover:shadow-md transition
                  {{ request('status') === 'preparing' ? 'ring-2 ring-indigo-300' : '' }}">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600
                            flex items-center justify-center text-sm">
                    🍳
                </div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">
                    Preparing
                </p>
            </div>
            <p class="text-2xl font-bold text-slate-900 mt-2">
                {{ $stats['preparing'] }}
            </p>
        </a>

    </div>


    {{-- ═══════════════════════ FILTERS ═══════════════════════ --}}
    @if(!$preOrders->isEmpty() || request()->hasAny(['status', 'search']))
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-5">
            <form method="GET" class="flex flex-wrap gap-3 items-end">

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-slate-600 mb-1">
                        Status
                    </label>
                    <select name="status"
                            class="w-full rounded-xl border border-slate-300 bg-white
                                   px-4 py-2.5 text-sm
                                   focus:border-indigo-500 focus:ring-2
                                   focus:ring-indigo-200 outline-none transition">
                        <option value="">All statuses</option>
                        @foreach(['draft', 'pending', 'confirmed', 'preparing', 'ready', 'served', 'cancelled'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="rounded-xl bg-slate-900 text-white
                               px-5 py-2.5 text-sm font-semibold
                               hover:bg-slate-800 transition">
                    Filter
                </button>

                @if(request()->hasAny(['status']))
                    <a href="{{ route('pre-orders.index') }}"
                       class="rounded-xl border border-slate-300 text-slate-700
                              px-5 py-2.5 text-sm font-semibold
                              hover:bg-slate-50 transition">
                        Clear
                    </a>
                @endif

            </form>
        </div>
    @endif


    {{-- ═══════════════════════ EMPTY STATE ═══════════════════════ --}}
    @if($preOrders->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl
                        bg-gradient-to-br from-indigo-100 to-violet-100 text-4xl mb-4">
                🛒
            </div>

            @if(request()->hasAny(['status']))
                <p class="text-base font-semibold text-slate-800">
                    No pre-orders match your filter
                </p>
                <p class="text-sm text-slate-500 mt-1">
                    Try clearing the filter to see all pre-orders.
                </p>
                <a href="{{ route('pre-orders.index') }}"
                   class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl
                          bg-slate-900 text-white text-sm font-semibold
                          hover:bg-slate-800 transition">
                    Clear Filter
                </a>
            @else
                <p class="text-base font-semibold text-slate-800">
                    No pre-orders yet
                </p>
                <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                    Pre-orders are placed alongside reservations. Create a confirmed
                    reservation, then add food to it.
                </p>

                <div class="flex items-center justify-center gap-3 mt-5 flex-wrap">
                    @can('create pre-orders')
                        <a href="{{ route('pre-orders.create') }}"
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
                            Add Pre-Order
                        </a>
                    @endcan

                    @can('view reservations')
                        <a href="{{ route('reservations.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                                  bg-white border border-slate-300 text-slate-700
                                  text-sm font-semibold
                                  hover:bg-slate-50 transition">
                            View Reservations
                        </a>
                    @endcan
                </div>
            @endif
        </div>


    {{-- ═══════════════════════ RESULTS ═══════════════════════ --}}
    @else

        {{-- ─── Mobile cards (< md) ─── --}}
        <div class="md:hidden space-y-3">
            @foreach($preOrders as $preOrder)
                <a href="{{ route('pre-orders.show', $preOrder) }}"
                   class="block bg-white rounded-2xl border border-slate-200 shadow-sm
                          p-4 hover:shadow-md transition">

                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900">
                                #{{ $preOrder->id }}
                            </p>
                            <p class="text-xs text-slate-500 mt-0.5 truncate">
                                {{ $preOrder->reservation?->hotel?->name ?? '—' }}
                            </p>
                        </div>
                        <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-medium
                                     bg-{{ $preOrder->statusColor() }}-100
                                     text-{{ $preOrder->statusColor() }}-700 flex-shrink-0">
                            {{ ucfirst($preOrder->status) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-4 text-xs text-slate-600 pt-2
                                border-t border-slate-100">
                        <span>
                            📅 {{ $preOrder->reservation?->reservation_date?->format('M d') ?? '—' }}
                        </span>
                        <span>
                            🛒 {{ $preOrder->items->sum('quantity') }} item(s)
                        </span>
                        <span class="ml-auto font-semibold text-slate-900">
                            Rs {{ number_format($preOrder->total, 0) }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>


        {{-- ─── Desktop table (>= md) ─── --}}
        <div class="hidden md:block bg-white rounded-2xl border border-slate-200
                    shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            Order
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            Reservation
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            Items
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($preOrders as $preOrder)
                        <tr class="hover:bg-slate-50 transition">

                            <td class="px-4 py-3">
                                <p class="text-sm font-semibold text-slate-900">
                                    #{{ $preOrder->id }}
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $preOrder->created_at->diffForHumans() }}
                                </p>
                            </td>

                            <td class="px-4 py-3">
                                @if($preOrder->reservation)
                                    <p class="text-sm font-medium text-slate-900">
                                        {{ $preOrder->reservation->hotel?->name ?? '—' }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ $preOrder->reservation->reservation_date?->format('M d, Y') }}
                                        · {{ \Carbon\Carbon::parse($preOrder->reservation->reservation_time)->format('h:i A') }}
                                    </p>
                                @else
                                    <span class="text-sm text-slate-400">—</span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <p class="text-sm text-slate-700">
                                    {{ $preOrder->items->sum('quantity') }} item(s)
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $preOrder->items->count() }} unique
                                </p>
                            </td>

                            <td class="px-4 py-3">
                                <p class="text-sm font-semibold text-slate-900">
                                    Rs {{ number_format($preOrder->total, 0) }}
                                </p>
                            </td>

                            <td class="px-4 py-3">
                                <span class="inline-flex px-2.5 py-1 rounded-md text-xs font-medium
                                             bg-{{ $preOrder->statusColor() }}-100
                                             text-{{ $preOrder->statusColor() }}-700">
                                    {{ ucfirst($preOrder->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1">

                                    {{-- View --}}
                                    <a href="{{ route('pre-orders.show', $preOrder) }}"
                                       class="p-2 rounded-lg text-slate-500
                                              hover:bg-slate-100 hover:text-slate-900 transition"
                                       title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    {{-- Edit — only if editable --}}
                                    @if($preOrder->isEditable())
                                        @can('edit pre-orders')
                                            <a href="{{ route('pre-orders.edit', $preOrder) }}"
                                               class="p-2 rounded-lg text-indigo-500
                                                      hover:bg-indigo-50 hover:text-indigo-700 transition"
                                               title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                     stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                        @endcan
                                    @endif

                                    {{-- Cancel — only if cancellable --}}
                                    @if($preOrder->isCancellable())
                                        @can('delete pre-orders')
                                            <form action="{{ route('pre-orders.destroy', $preOrder) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Cancel this pre-order?')"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="p-2 rounded-lg text-rose-500
                                                               hover:bg-rose-50 hover:text-rose-700 transition"
                                                        title="Cancel">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                         stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $preOrders->links() }}
        </div>

    @endif

</div>
@endsection
