@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Welcome back, ' . auth('admin')->user()->name)

@section('content')

    {{-- Today's Performance --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-indigo-100 text-sm mb-1">Today's Performance</p>
                <h2 class="text-2xl font-bold">{{ now()->format('l, F j, Y') }}</h2>
            </div>
            <div class="flex space-x-6">
                <div class="text-right">
                    <p class="text-3xl font-bold">{{ $todayStats['orders_today'] }}</p>
                    <p class="text-indigo-100 text-xs uppercase">Orders Today</p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold">₦{{ number_format($todayStats['revenue_today'], 2) }}</p>
                    <p class="text-indigo-100 text-xs uppercase">Revenue Today</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

        {{-- Total Orders --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-xs text-gray-500">All time</span>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['total_orders']) }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Orders</p>
        </div>

        {{-- Total Revenue --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs text-gray-500">Paid only</span>
            </div>
            <p class="text-3xl font-bold text-gray-800">₦{{ number_format($stats['total_revenue'], 2) }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Revenue</p>
        </div>

        {{-- Total Customers --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="text-xs text-gray-500">Registered</span>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['total_customers']) }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Customers</p>
        </div>

        {{-- Total Products --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <span class="text-xs text-gray-500">Active</span>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['total_products']) }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Products</p>
        </div>

    </div>

    {{-- Recent Orders & Low Stock Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Recent Orders --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Recent Orders</h3>
                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800">View all →</a>
            </div>

            @if($recentOrders->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-sm">No orders yet</p>
                    <p class="text-xs text-gray-400 mt-1">Orders will appear here once customers start buying</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                            <tr>
                                <th class="px-6 py-3 text-left">Order</th>
                                <th class="px-6 py-3 text-left">Customer</th>
                                <th class="px-6 py-3 text-left">Amount</th>
                                <th class="px-6 py-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentOrders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm font-medium text-gray-900">
                                        #{{ $order->order_number }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-700">
                                        {{ $order->customer->full_name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-3 text-sm font-semibold text-gray-900">
                                        ₦{{ number_format($order->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="inline-block px-2 py-1 text-xs rounded bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Low Stock Alert --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Low Stock Alert</h3>
                @if($lowStockProducts->count() > 0)
                    <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded">{{ $lowStockProducts->count() }}</span>
                @endif
            </div>

            @if($lowStockProducts->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-10 h-10 mx-auto mb-2 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm">All stock levels OK</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($lowStockProducts as $product)
                        <div class="px-6 py-3 flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500">SKU: {{ $product->sku ?? 'N/A' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-red-600">{{ $product->stock_quantity }}</p>
                                <p class="text-xs text-gray-500">left</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

@endsection