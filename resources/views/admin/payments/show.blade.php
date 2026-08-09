@extends('admin.layouts.app')

@section('title', 'Payment Details')
@section('heading', 'Payment Details')
@section('subheading', $payment->order->order_number ?? 'Payment')

@section('actions')
    <a href="{{ route('admin.payments.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Back to Payments</a>
@endsection

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Payment Info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Transaction Information</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Amount</p>
                        <p class="text-lg font-bold text-gray-900">₦{{ number_format($payment->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Status</p>
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
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Method</p>
                        <p class="text-gray-900">{{ $payment->method_label ?? ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Currency</p>
                        <p class="text-gray-900">{{ $payment->currency }}</p>
                    </div>

                    @if($payment->flutterwave_ref)
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Flutterwave Reference</p>
                            <p class="text-gray-900 break-all">{{ $payment->flutterwave_ref }}</p>
                        </div>
                    @endif
                    @if($payment->flutterwave_tx_id)
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Transaction ID</p>
                            <p class="text-gray-900 break-all">{{ $payment->flutterwave_tx_id }}</p>
                        </div>
                    @endif
                    @if($payment->flutterwave_payment_type)
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Payment Type</p>
                            <p class="text-gray-900">{{ ucfirst($payment->flutterwave_payment_type) }}</p>
                        </div>
                    @endif

                    @if($payment->paid_at)
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Paid At</p>
                            <p class="text-gray-900">{{ $payment->paid_at->format('M j, Y g:i A') }}</p>
                        </div>
                    @endif
                    @if($payment->failed_at)
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Failed At</p>
                            <p class="text-gray-900">{{ $payment->failed_at->format('M j, Y g:i A') }}</p>
                        </div>
                    @endif
                </div>

                @if($payment->failure_reason)
                    <div class="mt-4 bg-red-50 border border-red-200 rounded p-3">
                        <p class="text-xs text-red-600 uppercase font-semibold">Failure Reason</p>
                        <p class="text-sm text-red-800 mt-1">{{ $payment->failure_reason }}</p>
                    </div>
                @endif
            </div>

            {{-- Order Items --}}
            @if($payment->order)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900">Order Items</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($payment->order->items as $item)
                            <div class="px-6 py-3 flex items-center justify-between text-sm">
                                <span class="text-gray-700">{{ $item->product_name }} × {{ $item->quantity }}</span>
                                <span class="font-semibold text-gray-900">₦{{ number_format($item->subtotal, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Linked Order --}}
            @if($payment->order)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Linked Order</h3>
                    <p class="text-sm font-medium text-gray-900">{{ $payment->order->order_number }}</p>
                    <p class="text-xs text-gray-500 mb-3">{{ $payment->order->created_at->format('M j, Y') }}</p>
                    <a href="{{ route('admin.orders.show', $payment->order) }}"
                       class="inline-block w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                        View Full Order
                    </a>
                </div>

                {{-- Customer --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Customer</h3>
                    @if($payment->order->customer)
                        <p class="text-sm font-medium text-gray-900">{{ $payment->order->customer->full_name }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->order->customer->email }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->order->customer->phone }}</p>
                    @else
                        <p class="text-sm text-gray-500">Customer record unavailable</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

@endsection