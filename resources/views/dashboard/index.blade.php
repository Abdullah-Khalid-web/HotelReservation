@extends('layouts.app')

@section('content')

{{-- ═══════════════════════════════════════════════════════
     Chart.js CDN
═══════════════════════════════════════════════════════ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="max-w-7xl mx-auto">

    {{-- ═══════════════ HEADER ═══════════════ --}}
    <div class="mb-6 flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Dashboard</h1>
            <p class="text-sm text-slate-500 mt-1">
                Welcome back, {{ auth()->user()->name }}.
                @if($isAdmin)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md
                                 bg-indigo-100 text-indigo-700 text-xs font-semibold ml-2">
                        Admin View
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md
                                 bg-slate-100 text-slate-700 text-xs font-semibold ml-2">
                        My Activity
                    </span>
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reservations.create') }}"
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
                New Reservation
            </a>
        </div>
    </div>


    {{-- ═══════════════ HEADLINE STATS ═══════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">

        {{-- Reservations --}}
        <a href="{{ route('reservations.index') }}"
           class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5
                  hover:shadow-md hover:border-indigo-300 transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">
                        {{ $isAdmin ? 'Total' : 'My' }} Reservations
                    </p>
                    <h2 class="text-3xl font-bold text-slate-900 mt-2">
                        {{ $stats['total_reservations'] }}
                    </h2>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600
                            flex items-center justify-center text-xl">
                    📅
                </div>
            </div>
            @if($isAdmin && $stats['total_reservations'] > 0)
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center gap-3 text-xs">
                    <span class="text-amber-600 font-medium">
                        ⏳ {{ $stats['pending_reservations'] }} pending
                    </span>
                    <span class="text-emerald-600 font-medium">
                        ✅ {{ $stats['confirmed_reservations'] }} confirmed
                    </span>
                </div>
            @endif
        </a>

        {{-- Confirmed --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">
                        Confirmed
                    </p>
                    <h2 class="text-3xl font-bold text-slate-900 mt-2">
                        {{ $stats['confirmed_reservations'] }}
                    </h2>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600
                            flex items-center justify-center text-xl">
                    ✅
                </div>
            </div>
        </div>

        {{-- Pre-Orders --}}
        @can('view pre-orders')
            <a href="{{ route('pre-orders.index') }}"
               class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5
                      hover:shadow-md hover:border-violet-300 transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">
                            {{ $isAdmin ? 'Total' : 'My' }} Pre-Orders
                        </p>
                        <h2 class="text-3xl font-bold text-slate-900 mt-2">
                            {{ $stats['total_pre_orders'] }}
                        </h2>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-violet-100 text-violet-600
                                flex items-center justify-center text-xl">
                        🛒
                    </div>
                </div>
                @if($isAdmin && $stats['total_pre_orders'] > 0)
                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center gap-3 text-xs">
                        <span class="text-amber-600 font-medium">
                            ⏳ {{ $stats['pending_pre_orders'] }} pending
                        </span>
                        <span class="text-emerald-600 font-medium">
                            🍽️ {{ $stats['served_pre_orders'] }} served
                        </span>
                    </div>
                @endif
            </a>
        @endcan

        {{-- Tables / Hotels --}}
        @if($isAdmin)
            <a href="{{ route('tables.index') }}"
               class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5
                      hover:shadow-md hover:border-blue-300 transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">
                            Available Tables
                        </p>
                        <h2 class="text-3xl font-bold text-slate-900 mt-2">
                            {{ $stats['available_tables'] }}
                        </h2>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600
                                flex items-center justify-center text-xl">
                        🪑
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 text-xs">
                    <span class="text-slate-500">
                        across {{ $stats['total_hotels'] }} hotel(s)
                    </span>
                </div>
            </a>
        @else
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">
                            Cancelled
                        </p>
                        <h2 class="text-3xl font-bold text-slate-900 mt-2">
                            {{ $stats['cancelled_reservations'] }}
                        </h2>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600
                                flex items-center justify-center text-xl">
                        ❌
                    </div>
                </div>
            </div>
        @endif

    </div>


    {{-- ═══════════════ CHARTS ROW 1 ═══════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

        {{-- Reservation trend (2 cols) --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">
                        {{ $isAdmin ? 'All Reservations' : 'My Reservations' }} — Last 14 Days
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Daily count trend</p>
                </div>
                <span class="text-xs text-slate-400">Daily</span>
            </div>
            <div style="height: 280px; position: relative;">
                <canvas id="reservationTrendChart"></canvas>
            </div>
        </div>

        {{-- Status breakdown (1 col) --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="mb-4">
                <h2 class="text-base font-semibold text-slate-900">Status Breakdown</h2>
                <p class="text-xs text-slate-500 mt-0.5">Reservations by status</p>
            </div>
            <div style="height: 280px; position: relative;">
                <canvas id="statusBreakdownChart"></canvas>
            </div>
        </div>

    </div>


    {{-- ═══════════════ CHARTS ROW 2 ═══════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">

        {{-- Peak hours --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Peak Reservation Hours</h2>
                    <p class="text-xs text-slate-500 mt-0.5">What time of day bookings happen</p>
                </div>
            </div>
            <div style="height: 260px; position: relative;">
                <canvas id="hourlyChart"></canvas>
            </div>
        </div>

        {{-- By Hotel (admin only) or Top products --}}
        @if($isAdmin && $byHotelChart)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Top Hotels</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Reservations by hotel</p>
                    </div>
                </div>
                <div style="height: 260px; position: relative;">
                    <canvas id="byHotelChart"></canvas>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">My Top Items</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Most ordered products</p>
                    </div>
                </div>
                <div style="height: 260px; position: relative;">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        @endif

    </div>


    {{-- ═══════════════ CHARTS ROW 3 ═══════════════ --}}
    <div class="grid grid-cols-1 {{ $isAdmin ? 'lg:grid-cols-2' : '' }} gap-5 mb-6">

        {{-- Pre-order trend --}}
        @can('view pre-orders')
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Pre-Orders — Last 14 Days
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Daily order trend</p>
                    </div>
                </div>
                <div style="height: 240px; position: relative;">
                    <canvas id="preOrderTrendChart"></canvas>
                </div>
            </div>
        @endcan

        {{-- Top products (admin) --}}
        @if($isAdmin)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Top Selling Items</h2>
                        <p class="text-xs text-slate-500 mt-0.5">By total quantity ordered</p>
                    </div>
                </div>
                <div style="height: 240px; position: relative;">
                    <canvas id="topProductsAdminChart"></canvas>
                </div>
            </div>
        @endif

    </div>


    {{-- ═══════════════ ACTIVITY FEED ROW ═══════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Recent Reservations --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600
                                flex items-center justify-center">
                        📅
                    </div>
                    <h2 class="text-sm font-semibold text-slate-900">Recent Reservations</h2>
                </div>
                <a href="{{ route('reservations.index') }}"
                   class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                    View all →
                </a>
            </div>

            @if($recentReservations->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm text-slate-500">No reservations yet.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($recentReservations as $reservation)
                        <a href="{{ route('reservations.show', $reservation) }}"
                           class="flex items-center gap-3 p-4 hover:bg-slate-50 transition">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br
                                        from-indigo-100 to-violet-100
                                        flex items-center justify-center text-lg flex-shrink-0">
                                🏨
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 truncate">
                                    {{ $reservation->hotel?->name ?? '—' }}
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $reservation->reservation_date->format('M d, Y') }}
                                    · {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('h:i A') }}
                                    · {{ $reservation->number_of_guests }} guests
                                    @if($isAdmin && $reservation->user)
                                        · by {{ $reservation->user->name }}
                                    @endif
                                </p>
                            </div>
                            <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-medium flex-shrink-0
                                         bg-{{ match($reservation->status) {
                                            'pending'   => 'amber',
                                            'confirmed' => 'emerald',
                                            'cancelled' => 'rose',
                                            'completed' => 'blue',
                                            default     => 'slate',
                                         } }}-100
                                         text-{{ match($reservation->status) {
                                            'pending'   => 'amber',
                                            'confirmed' => 'emerald',
                                            'cancelled' => 'rose',
                                            'completed' => 'blue',
                                            default     => 'slate',
                                         } }}-700">
                                {{ ucfirst($reservation->status) }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>


        {{-- Recent Pre-Orders --}}
        @can('view pre-orders')
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-violet-100 text-violet-600
                                    flex items-center justify-center">
                            🛒
                        </div>
                        <h2 class="text-sm font-semibold text-slate-900">Recent Pre-Orders</h2>
                    </div>
                    <a href="{{ route('pre-orders.index') }}"
                       class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                        View all →
                    </a>
                </div>

                @if($recentPreOrders->isEmpty())
                    <div class="p-8 text-center">
                        <p class="text-sm text-slate-500">No pre-orders yet.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($recentPreOrders as $preOrder)
                            <a href="{{ route('pre-orders.show', $preOrder) }}"
                               class="flex items-center gap-3 p-4 hover:bg-slate-50 transition">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br
                                            from-violet-100 to-pink-100
                                            flex items-center justify-center text-lg flex-shrink-0">
                                    🍽️
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900 truncate">
                                        Order #{{ $preOrder->id }} —
                                        {{ $preOrder->reservation?->hotel?->name ?? '—' }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ $preOrder->items->sum('quantity') }} items ·
                                        Rs {{ number_format($preOrder->total, 0) }}
                                        @if($isAdmin && $preOrder->user)
                                            · by {{ $preOrder->user->name }}
                                        @endif
                                    </p>
                                </div>
                                <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-medium flex-shrink-0
                                             bg-{{ $preOrder->statusColor() }}-100
                                             text-{{ $preOrder->statusColor() }}-700">
                                    {{ ucfirst($preOrder->status) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endcan

    </div>


    {{-- ═══════════════ UPCOMING RESERVATIONS ═══════════════ --}}
    @if($upcomingReservations->isNotEmpty())
        <div class="mt-6 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600
                                flex items-center justify-center">
                        ⏰
                    </div>
                    <h2 class="text-sm font-semibold text-slate-900">
                        Upcoming — Next 7 Days
                    </h2>
                </div>
                <a href="{{ route('reservations.index') }}"
                   class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                    View all →
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($upcomingReservations as $reservation)
                    <a href="{{ route('reservations.show', $reservation) }}"
                       class="flex items-center gap-3 p-4 hover:bg-slate-50 transition">
                        <div class="w-14 text-center flex-shrink-0">
                            <p class="text-xs text-slate-500 uppercase">
                                {{ $reservation->reservation_date->format('M') }}
                            </p>
                            <p class="text-xl font-bold text-slate-900">
                                {{ $reservation->reservation_date->format('d') }}
                            </p>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-900 truncate">
                                {{ $reservation->hotel?->name ?? '—' }}
                            </p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('h:i A') }}
                                · {{ $reservation->number_of_guests }} guests
                            </p>
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
    @endif

</div>


{{-- ═══════════════════════════════════════════════════════
     CHART INITIALIZATION
═══════════════════════════════════════════════════════ --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js not loaded');
            return;
        }

        Chart.defaults.font.family =
            'ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif';
        Chart.defaults.color = '#64748b';

        const palette = {
            indigo:  '#6366f1',
            violet:  '#8b5cf6',
            emerald: '#10b981',
            amber:   '#f59e0b',
            rose:    '#f43f5e',
            blue:    '#3b82f6',
            slate:   '#94a3b8',
        };

        // ════════ 1. Reservation trend ════════
        const reservationTrendData = @json($reservationTrend);

        new Chart(document.getElementById('reservationTrendChart'), {
            type: 'line',
            data: {
                labels: reservationTrendData.labels,
                datasets: [{
                    label: 'Reservations',
                    data: reservationTrendData.values,
                    borderColor: palette.indigo,
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: palette.indigo,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 5,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, maxRotation: 0 },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 11 } },
                        grid: { color: 'rgba(148, 163, 184, 0.15)' },
                    },
                },
            },
        });

        // ════════ 2. Status breakdown ════════
        const statusData = @json($reservationStatusChart);

        new Chart(document.getElementById('statusBreakdownChart'), {
            type: 'doughnut',
            data: {
                labels: statusData.labels,
                datasets: [{
                    data: statusData.values,
                    backgroundColor: [
                        palette.amber,
                        palette.emerald,
                        palette.rose,
                        palette.blue,
                    ],
                    borderWidth: 0,
                    hoverOffset: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            font: { size: 11 },
                            usePointStyle: true,
                            pointStyle: 'circle',
                        },
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8,
                    },
                },
            },
        });

        // ════════ 3. Peak hours ════════
        const hourlyData = @json($hourlyChart);

        new Chart(document.getElementById('hourlyChart'), {
            type: 'bar',
            data: {
                labels: hourlyData.labels,
                datasets: [{
                    label: 'Reservations',
                    data: hourlyData.values,
                    backgroundColor: palette.violet,
                    borderRadius: 4,
                    barThickness: 'flex',
                    maxBarThickness: 20,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8,
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 10 },
                            callback: function(val, index) {
                                // Show only every 3rd label
                                return index % 3 === 0 ? this.getLabelForValue(val) : '';
                            },
                        },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 11 } },
                        grid: { color: 'rgba(148, 163, 184, 0.15)' },
                    },
                },
            },
        });

        // ════════ 4. By Hotel (admin) ════════
        @if($isAdmin && $byHotelChart)
            const byHotelData = @json($byHotelChart);

            new Chart(document.getElementById('byHotelChart'), {
                type: 'bar',
                data: {
                    labels: byHotelData.labels,
                    datasets: [{
                        label: 'Reservations',
                        data: byHotelData.values,
                        backgroundColor: palette.indigo,
                        borderRadius: 6,
                        maxBarThickness: 28,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            padding: 10,
                            cornerRadius: 8,
                        },
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { precision: 0, font: { size: 11 } },
                            grid: { color: 'rgba(148, 163, 184, 0.15)' },
                        },
                        y: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } },
                        },
                    },
                },
            });
        @else
            // Non-admin: top products chart
            const topProductsData = @json($topProductsChart);

            new Chart(document.getElementById('topProductsChart'), {
                type: 'bar',
                data: {
                    labels: topProductsData.labels.length ? topProductsData.labels : ['No data'],
                    datasets: [{
                        label: 'Quantity',
                        data: topProductsData.values.length ? topProductsData.values : [0],
                        backgroundColor: palette.violet,
                        borderRadius: 6,
                        maxBarThickness: 28,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            padding: 10,
                            cornerRadius: 8,
                        },
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { precision: 0, font: { size: 11 } },
                            grid: { color: 'rgba(148, 163, 184, 0.15)' },
                        },
                        y: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } },
                        },
                    },
                },
            });
        @endif

        // ════════ 5. Pre-order trend ════════
        @can('view pre-orders')
            const preOrderTrendData = @json($preOrderTrendChart);

            new Chart(document.getElementById('preOrderTrendChart'), {
                type: 'line',
                data: {
                    labels: preOrderTrendData.labels,
                    datasets: [{
                        label: 'Pre-Orders',
                        data: preOrderTrendData.values,
                        borderColor: palette.violet,
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: palette.violet,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 5,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            padding: 10,
                            cornerRadius: 8,
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, maxRotation: 0 },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, font: { size: 11 } },
                            grid: { color: 'rgba(148, 163, 184, 0.15)' },
                        },
                    },
                },
            });
        @endcan

        // ════════ 6. Top products (admin) ════════
        @if($isAdmin)
            const topProductsAdminData = @json($topProductsChart);

            new Chart(document.getElementById('topProductsAdminChart'), {
                type: 'bar',
                data: {
                    labels: topProductsAdminData.labels.length ? topProductsAdminData.labels : ['No data'],
                    datasets: [{
                        label: 'Quantity',
                        data: topProductsAdminData.values.length ? topProductsAdminData.values : [0],
                        backgroundColor: palette.emerald,
                        borderRadius: 6,
                        maxBarThickness: 28,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            padding: 10,
                            cornerRadius: 8,
                        },
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { precision: 0, font: { size: 11 } },
                            grid: { color: 'rgba(148, 163, 184, 0.15)' },
                        },
                        y: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } },
                        },
                    },
                },
            });
        @endif
    });
</script>

@endsection
