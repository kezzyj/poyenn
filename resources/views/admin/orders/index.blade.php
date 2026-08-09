@extends('admin.layouts.app')

@section('title', 'Orders')
@section('heading', 'Orders')
@section('subheading', 'Manage customer orders')

@section('content')

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="bg-white rounded-lg shadow p-4 hover:shadow-md transition">
            <p class="text-xs text-gray-500 uppercase">Pending</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" class="bg-white rounded-lg shadow p-4 hover:shadow-md transition">
            <p class="text-xs text-gray-500 uppercase">Confirmed</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['confirmed'] }}</p>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'out_for_delivery']) }}" class="bg-white rounded-lg shadow p-4 hover:shadow-md transition">
            <p class="text-xs text-gray-500 uppercase">Out for Delivery</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['out_for_delivery'] }}</p>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="bg-white rounded-lg shadow p-4 hover:shadow-md transition">
            <p class="text-xs text-gray-500 uppercase">Delivered</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['delivered'] }}</p>
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Order number, customer name, email..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="packed" {{ request('status') === 'packed' ? 'selected' : '' }}>Packed</option>
                <option value="out_for_delivery" {{ request('status') === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <select name="payment_status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">All Payments</option>
                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Payment Pending</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>

            <select name="payment_method" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">All Methods</option>
                <option value="flutterwave" {{ request('payment_method') === 'flutterwave' ? 'selected' : '' }}>Flutterwave</option>
                <option value="cash_on_delivery" {{ request('payment_method') === 'cash_on_delivery' ? 'selected' : '' }}>Cash on Delivery</option>
            </select>

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'payment_status', 'payment_method']))
                    <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg">✕</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($orders->isEmpty())
            <div class="p-16 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-700 mb-1">No orders found</h3>
                <p class="text-sm text-gray-500">Orders will appear here as customers place them.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left">Order #</th>
                            <th class="px-6 py-3 text-left">Customer</th>
                            <th class="px-6 py-3 text-left">Items</th>
                            <th class="px-6 py-3 text-left">Total</th>
                            <th class="px-6 py-3 text-left">Payment</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Date</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $order->order_number }}</td>
                                <td class="px-6 py-3 text-sm">
                                    <p class="text-gray-900">{{ $order->customer->full_name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->customer->email ?? '' }}</p>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-700">{{ $order->items->count() }} item(s)</td>
                                <td class="px-6 py-3 text-sm font-semibold text-gray-900">₦{{ number_format($order->total_amount, 2) }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex flex-col space-y-1">
                                        <span class="text-xs text-gray-600">{{ $order->latestPayment->method_label ?? ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
                                        <span class="inline-block px-2 py-0.5 text-xs rounded {{ $order->is_paid ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="inline-block px-2 py-1 text-xs font-medium rounded bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500">
                                    {{ $order->created_at->format('M j, Y') }}<br>
                                    <span class="text-xs">{{ $order->created_at->format('g:i A') }}</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

@endsection