@extends('admin.layouts.app')

@section('title', 'Payments')
@section('heading', 'Payments')
@section('subheading', 'Track all transactions')

@section('content')

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase">Total Collected</p>
            <p class="text-2xl font-bold text-green-600">₦{{ number_format($stats['total_collected'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase">Pending</p>
            <p class="text-2xl font-bold text-yellow-600">₦{{ number_format($stats['pending'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase">Successful</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['successful_count'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase">Failed</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['failed_count'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Order number or transaction ref..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="successful" {{ request('status') === 'successful' ? 'selected' : '' }}>Successful</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>

            <select name="method" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">All Methods</option>
                <option value="flutterwave" {{ request('method') === 'flutterwave' ? 'selected' : '' }}>Flutterwave</option>
                <option value="cash_on_delivery" {{ request('method') === 'cash_on_delivery' ? 'selected' : '' }}>Cash on Delivery</option>
            </select>

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'method']))
                    <a href="{{ route('admin.payments.index') }}" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg">✕</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($payments->isEmpty())
            <div class="p-16 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-700 mb-1">No payments yet</h3>
                <p class="text-sm text-gray-500">Payments will appear here as orders are placed.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left">Order #</th>
                            <th class="px-6 py-3 text-left">Customer</th>
                            <th class="px-6 py-3 text-left">Method</th>
                            <th class="px-6 py-3 text-left">Amount</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Date</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">
                                    {{ $payment->order->order_number ?? '—' }}
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-700">
                                    {{ $payment->order->customer->full_name ?? 'Unknown' }}
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-700">
                                    {{ $payment->method_label ?? ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                </td>
                                <td class="px-6 py-3 text-sm font-semibold text-gray-900">
                                    ₦{{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-6 py-3">
                                    @php
                                        $statusColors = [
                                            'successful' => 'bg-green-100 text-green-700',
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'failed' => 'bg-red-100 text-red-700',
                                        ];
                                        $color = $statusColors[$payment->status] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="inline-block px-2 py-1 text-xs font-medium rounded {{ $color }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500">
                                    {{ $payment->created_at->format('M j, Y') }}<br>
                                    <span class="text-xs">{{ $payment->created_at->format('g:i A') }}</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.payments.show', $payment) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

@endsection