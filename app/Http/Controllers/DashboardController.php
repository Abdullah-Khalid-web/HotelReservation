<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Hotel;
use App\Models\HotelTable;
use App\Models\PreOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $isAdmin = $user->hasAnyRole(['superadmin', 'admin']);

        // ═══════════════════════════════════════════════
        //  ROLE-AWARE BASE SCOPING
        // ═══════════════════════════════════════════════

        $reservationQuery = Reservation::query();
        if (! $isAdmin) {
            $reservationQuery->where('user_id', $user->id);
        }

        $preOrderQuery = PreOrder::query();
        if (! $isAdmin) {
            $preOrderQuery->where('user_id', $user->id);
        }

        // ═══════════════════════════════════════════════
        //  HEADLINE STATS
        // ═══════════════════════════════════════════════

        $stats = [
            'total_reservations'     => (clone $reservationQuery)->count(),
            'pending_reservations'   => (clone $reservationQuery)->where('status', 'pending')->count(),
            'confirmed_reservations' => (clone $reservationQuery)->where('status', 'confirmed')->count(),
            'cancelled_reservations' => (clone $reservationQuery)->where('status', 'cancelled')->count(),
            'completed_reservations' => (clone $reservationQuery)->where('status', 'completed')->count(),

            'total_pre_orders'       => (clone $preOrderQuery)->count(),
            'pending_pre_orders'     => (clone $preOrderQuery)->where('status', 'pending')->count(),
            'served_pre_orders'      => (clone $preOrderQuery)->where('status', 'served')->count(),

            'total_hotels'           => Hotel::count(),
            'available_tables'       => HotelTable::where('status', 'available')->count(),
        ];

        // ═══════════════════════════════════════════════
        //  CHART 1: Reservations — Last 14 days line chart
        // ═══════════════════════════════════════════════

        $days = collect();
        for ($i = 13; $i >= 0; $i--) {
            $days->push(today()->subDays($i));
        }

        $reservationTrendRaw = (clone $reservationQuery)
            ->whereDate('reservation_date', '>=', today()->subDays(13))
            ->selectRaw('DATE(reservation_date) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $reservationTrend = [
            'labels' => $days->map(fn ($d) => $d->format('M d'))->values(),
            'values' => $days->map(fn ($d) => (int) ($reservationTrendRaw[$d->toDateString()] ?? 0))->values(),
        ];

        // ═══════════════════════════════════════════════
        //  CHART 2: Reservation status breakdown (doughnut)
        // ═══════════════════════════════════════════════

        $statusBreakdown = (clone $reservationQuery)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $reservationStatusChart = [
            'labels' => ['Pending', 'Confirmed', 'Cancelled', 'Completed'],
            'values' => [
                (int) ($statusBreakdown['pending']   ?? 0),
                (int) ($statusBreakdown['confirmed'] ?? 0),
                (int) ($statusBreakdown['cancelled'] ?? 0),
                (int) ($statusBreakdown['completed'] ?? 0),
            ],
        ];

        // ═══════════════════════════════════════════════
        //  CHART 3: Reservations by hotel (bar) — admins only
        // ═══════════════════════════════════════════════

        $byHotelChart = null;
        if ($isAdmin) {
            $byHotel = (clone $reservationQuery)
                ->select('hotel_id', DB::raw('COUNT(*) as count'))
                ->with('hotel:id,name')
                ->groupBy('hotel_id')
                ->orderByDesc('count')
                ->limit(8)
                ->get();

            $byHotelChart = [
                'labels' => $byHotel->map(fn ($r) => $r->hotel?->name ?? 'Unknown')->values(),
                'values' => $byHotel->pluck('count')->map(fn ($v) => (int) $v)->values(),
            ];
        }

        // ═══════════════════════════════════════════════
        //  CHART 4: Peak hours heatmap-style (bar)
        // ═══════════════════════════════════════════════

        $hourly = (clone $reservationQuery)
            ->selectRaw("HOUR(reservation_time) as hour, COUNT(*) as count")
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour');

        $hourlyChart = [
            'labels' => collect(range(0, 23))
                ->map(fn ($h) => sprintf('%02d:00', $h))
                ->values(),
            'values' => collect(range(0, 23))
                ->map(fn ($h) => (int) ($hourly[$h] ?? 0))
                ->values(),
        ];

        // ═══════════════════════════════════════════════
        //  CHART 5: Pre-order trend (last 14 days)
        // ═══════════════════════════════════════════════

        $preOrderTrendRaw = (clone $preOrderQuery)
            ->whereDate('created_at', '>=', today()->subDays(13))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $preOrderTrendChart = [
            'labels' => $days->map(fn ($d) => $d->format('M d'))->values(),
            'values' => $days->map(fn ($d) => (int) ($preOrderTrendRaw[$d->toDateString()] ?? 0))->values(),
        ];

        // ═══════════════════════════════════════════════
        //  CHART 6: Top products by order quantity
        // ═══════════════════════════════════════════════

        $topProducts = DB::table('pre_order_items')
            ->join('pre_orders', 'pre_order_items.pre_order_id', '=', 'pre_orders.id')
            ->when(! $isAdmin, fn ($q) => $q->where('pre_orders.user_id', $user->id))
            ->select('pre_order_items.product_name', DB::raw('SUM(pre_order_items.quantity) as total_qty'))
            ->groupBy('pre_order_items.product_name')
            ->orderByDesc('total_qty')
            ->limit(6)
            ->get();

        $topProductsChart = [
            'labels' => $topProducts->pluck('product_name')->values(),
            'values' => $topProducts->pluck('total_qty')->map(fn ($v) => (int) $v)->values(),
        ];

        // ═══════════════════════════════════════════════
        //  RECENT ACTIVITY
        // ═══════════════════════════════════════════════

        $recentReservations = (clone $reservationQuery)
            ->with(['hotel:id,name', 'user:id,name'])
            ->latest()
            ->limit(5)
            ->get();

        $recentPreOrders = (clone $preOrderQuery)
            ->with(['reservation.hotel:id,name', 'user:id,name'])
            ->latest()
            ->limit(5)
            ->get();

        // ═══════════════════════════════════════════════
        //  UPCOMING RESERVATIONS (next 7 days)
        // ═══════════════════════════════════════════════

        $upcomingReservations = (clone $reservationQuery)
            ->with(['hotel:id,name'])
            ->where('status', 'confirmed')
            ->whereDate('reservation_date', '>=', today())
            ->whereDate('reservation_date', '<=', today()->addDays(7))
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'isAdmin',
            'stats',
            'reservationTrend',
            'reservationStatusChart',
            'byHotelChart',
            'hourlyChart',
            'preOrderTrendChart',
            'topProductsChart',
            'recentReservations',
            'recentPreOrders',
            'upcomingReservations'
        ));
    }
}
