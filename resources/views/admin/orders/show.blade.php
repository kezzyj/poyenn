@extends('admin.layouts.app')

@section('title', 'Order ' . $order->order_number)
@section('heading', 'Order ' . $order->order_number)
@section('subheading', 'Placed ' . $order->created_at->format('F j, Y \a\t g:i A'))

@section('actions')
    <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Back to Orders</a>
@endsection

@section('content')

    {{-- Status Bar --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center space-x-3">
            <span class="text-sm text-gray-500">Order Status:</span>
            <span class="px-3 py-1 text-xs font-semibold rounded bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700">
                {{ $order->status_label }}
            </span>
            <span class="text-sm text-gray-500">Payment:</span>
            <span class="px-3 py-1 text-xs font-semibold rounded {{ $order->is_paid ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                {{ ucfirst($order->payment_status) }}
            </span>
        </div>

        {{-- Quick status update buttons --}}
        <div class="flex flex-wrap items-center gap-2">
            @if($order->status === 'pending')
                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="confirmed">
                    <button class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded">
                        ✓ Confirm
                    </button>
                </form>
            @endif

            @if(in_array($order->status, ['confirmed', 'pending']))
                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="packed">
                    <button class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded">
                        📦 Packed
                    </button>
                </form>
            @endif

            @if(in_array($order->status, ['packed', 'confirmed']))
                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="out_for_delivery">
                    <button class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-medium rounded">
                        🚚 Out for Delivery
                    </button>
                </form>
            @endif

            @if($order->status === 'out_for_delivery')
                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="delivered">
                    <button class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded">
                        ✓ Delivered
                    </button>
                </form>
            @endif

            @if(!in_array($order->status, ['delivered', 'cancelled']))
                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}"
                      onsubmit="return confirm('Are you sure you want to cancel this order?');"
                      class="inline">
                    @csrf
                    <input type="hidden" name="status" value="cancelled">
                    <button class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded">
                        ✕ Cancel
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Items --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Order Items</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="px-6 py-4 flex items-center space-x-4">
                            @if($item->product_image)
                                <img src="{{ asset('storage/' . $item->product_image) }}" class="w-16 h-16 rounded-lg object-cover">
                            @else
                                <div class="w-16 h-16 bg-gray-100 rounded-lg"></div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                                @if($item->product_sku)
                                    <p class="text-xs text-gray-500">SKU: {{ $item->product_sku }}</p>
                                @endif
                                <p class="text-sm text-gray-600">{{ $item->quantity }} × ₦{{ number_format($item->unit_price, 2) }}</p>
                            </div>
                            <p class="text-sm font-bold text-gray-900">₦{{ number_format($item->subtotal, 2) }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Order Totals --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">₦{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Delivery ({{ $order->deliveryRate->name ?? '—' }})</span>
                        <span class="font-medium">₦{{ number_format($order->delivery_fee, 2) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-green-700">
                            <span>Discount</span>
                            <span>− ₦{{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200">
                        <span>Total</span>
                        <span>₦{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Status History --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Order Timeline</h3>
                </div>
                <div class="p-6 space-y-3">
                    @foreach($order->statusHistory as $event)
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-indigo-600 rounded-full mt-1.5"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $event->status)) }}</p>
                                @if($event->note)
                                    <p class="text-xs text-gray-600 mt-0.5">{{ $event->note }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-0.5">{{ $event->created_at->format('M j, Y g:i A') }} • by {{ ucfirst($event->changed_by_type) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Admin Notes --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Internal Notes</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Only visible to admins</p>
                </div>
                <form method="POST" action="{{ route('admin.orders.add-note', $order) }}" class="p-4">
                    @csrf
                    <textarea name="admin_notes" rows="3"
                              placeholder="Add internal note about this order..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">{{ $order->admin_notes }}</textarea>
                    <div class="mt-2 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded">
                            Save Note
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-6">

            {{-- Customer --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Customer</h3>
                @if($order->customer)
                    <p class="text-sm font-medium text-gray-900">{{ $order->customer->full_name }}</p>
                    <p class="text-xs text-gray-500">{{ $order->customer->email }}</p>
                    <p class="text-xs text-gray-500">{{ $order->customer->phone }}</p>
                    <p class="text-xs text-gray-500 mt-2">Customer since {{ $order->customer->created_at->format('M Y') }}</p>
                @else
                    <p class="text-sm text-gray-500">Customer record removed</p>
                @endif
            </div>

            {{-- Delivery Address --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Delivery Address</h3>
                <div class="text-sm text-gray-700 space-y-0.5">
                    <p class="font-medium">{{ $order->delivery_recipient_name }}</p>
                    <p>{{ $order->delivery_phone }}</p>
                    <p>{{ $order->delivery_address_line_1 }}</p>
                    @if($order->delivery_address_line_2)
                        <p>{{ $order->delivery_address_line_2 }}</p>
                    @endif
                    <p>{{ $order->delivery_city }}, {{ $order->delivery_state }}</p>
                    @if($order->delivery_landmark)
                        <p class="text-xs text-gray-500 pt-1">Landmark: {{ $order->delivery_landmark }}</p>
                    @endif
                </div>
            </div>

            {{-- Delivery Agent --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Delivery Agent</h3>

                @if($order->delivery?->agent)
                    <p class="text-sm font-medium text-gray-900">{{ $order->delivery->agent->name }}</p>
                    <p class="text-xs text-gray-500">{{ $order->delivery->agent->phone }}</p>
                    @if($order->delivery->assigned_at)
                        <p class="text-xs text-gray-500 mt-1">Assigned {{ $order->delivery->assigned_at->diffForHumans() }}</p>
                    @endif
                @endif

                @if(!in_array($order->status, ['delivered', 'cancelled']))
                    <form method="POST" action="{{ route('admin.orders.assign-agent', $order) }}" class="mt-3">
                        @csrf
                        <select name="delivery_agent_id" required class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Assign agent —</option>
                            @foreach($availableAgents as $agent)
                                <option value="{{ $agent->id }}" {{ $order->delivery?->delivery_agent_id == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full mt-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded">
                            {{ $order->delivery?->agent ? 'Reassign Agent' : 'Assign Agent' }}
                        </button>
                    </form>
                @endif
            </div>

            {{-- Payment --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Payment</h3>
                @if($order->latestPayment)
                    <p class="text-sm">
                        <span class="text-gray-600">Method:</span>
                        <span class="font-medium text-gray-900">{{ $order->latestPayment->method_label }}</span>
                    </p>
                    <p class="text-sm">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-medium text-gray-900">{{ $order->latestPayment->status_label }}</span>
                    </p>
                    <p class="text-sm">
                        <span class="text-gray-600">Amount:</span>
                        <span class="font-medium text-gray-900">₦{{ number_format($order->latestPayment->amount, 2) }}</span>
                    </p>
                    @if($order->latestPayment->paid_at)
                        <p class="text-xs text-gray-500 mt-1">Paid {{ $order->latestPayment->paid_at->format('M j, Y g:i A') }}</p>
                    @endif

                    @if($order->is_cod && $order->payment_status === 'pending' && $order->status !== 'cancelled')
                        <form method="POST" action="{{ route('admin.orders.mark-paid', $order) }}"
                              onsubmit="return confirm('Mark this COD payment as received?');" class="mt-3">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded">
                                Mark Payment Received
                            </button>
                        </form>
                    @endif
                @else
                    <p class="text-sm text-gray-500">No payment record</p>
                @endif
            </div>
        </div>
    </div>

@endsection