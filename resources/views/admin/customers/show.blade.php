@extends('admin.layouts.app')

@section('title', 'Customer Details')
@section('heading', $customer->full_name)
@section('subheading', 'Customer since ' . $customer->created_at->format('F Y'))

@section('actions')
    <a href="{{ route('admin.customers.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Back to Customers</a>
@endsection

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">
            {{-- Order Stats --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-xs text-gray-500 uppercase">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $orderStats['total_orders'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-xs text-gray-500 uppercase">Total Spent</p>
                    <p class="text-2xl font-bold text-green-600">₦{{ number_format($orderStats['total_spent'], 2) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-xs text-gray-500 uppercase">Active Orders</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $orderStats['pending_orders'] }}</p>
                </div>
            </div>

            {{-- Order History --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Order History</h3>
                </div>
                @if($customer->orders->isEmpty())
                    <div class="p-8 text-center text-sm text-gray-500">This customer hasn't placed any orders yet.</div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($customer->orders as $order)
                            <a href="{{ route('admin.orders.show', $order) }}" class="block px-6 py-3 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $order->order_number }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->created_at->format('M j, Y') }} • {{ $order->items->count() }} item(s)</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-gray-900">₦{{ number_format($order->total_amount, 2) }}</p>
                                        <span class="inline-block px-2 py-0.5 text-xs rounded bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700">{{ $order->status_label }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Contact Information</h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Email</p>
                        <p class="text-gray-900 break-all">{{ $customer->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Phone</p>
                        <p class="text-gray-900">{{ $customer->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Email Verified</p>
                        @if($customer->email_verified_at)
                            <p class="text-green-700">Yes — {{ $customer->email_verified_at->format('M j, Y') }}</p>
                        @else
                            <p class="text-gray-600">Not verified</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Joined</p>
                        <p class="text-gray-900">{{ $customer->created_at->format('M j, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection