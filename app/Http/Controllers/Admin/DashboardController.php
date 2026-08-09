<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;

        // Core stats
        $stats = [
            'total_orders' => Order::where('platform_id', $platformId)->count(),
            'total_revenue' => Order::where('platform_id', $platformId)
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'total_customers' => Customer::where('platform_id', $platformId)->count(),
            'total_products' => Product::where('platform_id', $platformId)->count(),
        ];

        // Today's stats
        $todayStats = [
            'orders_today' => Order::where('platform_id', $platformId)
                ->whereDate('created_at', today())
                ->count(),
            'revenue_today' => Order::where('platform_id', $platformId)
                ->where('payment_status', 'paid')
                ->whereDate('created_at', today())
                ->sum('total_amount'),
        ];

        // Recent orders
        $recentOrders = Order::where('platform_id', $platformId)
            ->with('customer')
            ->latest()
            ->limit(5)
            ->get();

        // Low stock products
        $lowStockProducts = Product::where('platform_id', $platformId)
            ->where('manage_stock', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('is_active', true)
            ->limit(5)
            ->get();

        // Order status breakdown
        $ordersByStatus = Order::where('platform_id', $platformId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('admin.dashboard', compact(
            'stats',
            'todayStats',
            'recentOrders',
            'lowStockProducts',
            'ordersByStatus'
        ));
    }
}