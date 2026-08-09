<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Delivery {{ $delivery->order->order_number }} — Poyenn Agent</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-orange-50 min-h-screen pb-20">

    {{-- Top Bar --}}
    <nav class="bg-orange-500 shadow sticky top-0 z-10">
        <div class="max-w-3xl mx-auto px-4 py-3 flex items-center">
            <a href="{{ route('agent.dashboard') }}" class="text-white mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-base font-bold text-white">{{ $delivery->order->order_number }}</h1>
                <p class="text-xs text-orange-100">{{ $delivery->status_label }}</p>
            </div>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto p-4 space-y-4">

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-3 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Customer Info --}}
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-semibold text-gray-800 mb-2 text-sm">Deliver To</h3>
            <p class="text-base font-bold text-gray-900">{{ $delivery->order->delivery_recipient_name }}</p>

            <a href="tel:{{ $delivery->order->delivery_phone }}"
               class="inline-flex items-center mt-2 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                Call {{ $delivery->order->delivery_phone }}
            </a>
        </div>

        {{-- Address --}}
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-semibold text-gray-800 mb-2 text-sm">Delivery Address</h3>
            <p class="text-sm text-gray-900">{{ $delivery->order->delivery_address_line_1 }}</p>
            @if($delivery->order->delivery_address_line_2)
                <p class="text-sm text-gray-900">{{ $delivery->order->delivery_address_line_2 }}</p>
            @endif
            <p class="text-sm text-gray-900">{{ $delivery->order->delivery_city }}, {{ $delivery->order->delivery_state }}</p>
            @if($delivery->order->delivery_landmark)
                <p class="text-xs text-orange-700 mt-1 font-medium">📍 Landmark: {{ $delivery->order->delivery_landmark }}</p>
            @endif
        </div>

        {{-- Payment Info --}}
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-semibold text-gray-800 mb-2 text-sm">Payment</h3>
            @if($delivery->order->is_cod && $delivery->order->payment_status === 'pending')
                <div class="bg-orange-50 border border-orange-200 rounded p-3">
                    <p class="text-base font-bold text-orange-900">💰 Collect Cash on Delivery</p>
                    <p class="text-2xl font-bold text-orange-700 mt-1">₦{{ number_format($delivery->order->total_amount, 2) }}</p>
                    <p class="text-xs text-orange-600 mt-1">Verify amount before handing over the package</p>
                </div>
            @else
                <div class="bg-green-50 border border-green-200 rounded p-3">
                    <p class="text-base font-bold text-green-900">✓ Already Paid Online</p>
                    <p class="text-sm text-green-700 mt-1">No cash to collect. Just hand over the package.</p>
                </div>
            @endif
        </div>

        {{-- Items --}}
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-semibold text-gray-800 mb-3 text-sm">Items to Deliver</h3>
            <div class="space-y-2">
                @foreach($delivery->order->items as $item)
                    <div class="flex items-center space-x-3 py-2 border-b border-gray-100 last:border-0">
                        @if($item->product_image)
                            <img src="{{ asset('storage/' . $item->product_image) }}" class="w-12 h-12 rounded object-cover">
                        @else
                            <div class="w-12 h-12 bg-gray-100 rounded"></div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                            <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="space-y-2" x-data="{ showFailForm: false, showDeliverForm: false }">

            @if($delivery->status === 'assigned')
                <form method="POST" action="{{ route('agent.delivery.pick-up', $delivery) }}">
                    @csrf
                    <button type="submit"
                            class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                        📦 Mark as Picked Up
                    </button>
                </form>
            @endif

            @if($delivery->status === 'picked_up')
                <form method="POST" action="{{ route('agent.delivery.in-transit', $delivery) }}">
                    @csrf
                    <button type="submit"
                            class="w-full px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg">
                        🚚 Mark as In Transit
                    </button>
                </form>
            @endif

            @if(in_array($delivery->status, ['picked_up', 'in_transit']))
                <button @click="showDeliverForm = !showDeliverForm; showFailForm = false"
                        class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg">
                    ✓ Mark as Delivered
                </button>

                <button @click="showFailForm = !showFailForm; showDeliverForm = false"
                        class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg">
                    ✕ Mark as Failed
                </button>
            @endif

            {{-- Delivery Confirmation Form --}}
            <div x-show="showDeliverForm" x-cloak class="bg-white rounded-lg shadow p-4 border-2 border-green-500">
                <h3 class="font-semibold text-gray-800 mb-3 text-sm">Confirm Delivery</h3>
                <form method="POST" action="{{ route('agent.delivery.deliver', $delivery) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Proof of Delivery (optional)</label>
                        <input type="file" name="proof_image" accept="image/*" capture="environment"
                               class="w-full text-xs">
                        <p class="text-xs text-gray-500 mt-1">Photo of package handover or signature</p>
                    </div>

                    <div class="mb-3">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Note (optional)</label>
                        <textarea name="note" rows="2" placeholder="Any notes about the delivery..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500"></textarea>
                    </div>

                    @if($delivery->order->is_cod && $delivery->order->payment_status === 'pending')
                        <div class="bg-orange-50 border border-orange-200 rounded p-3 mb-3">
                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" required class="mt-1 text-orange-600 rounded">
                                <span class="ml-2 text-sm text-orange-900">
                                    I confirm I have collected ₦{{ number_format($delivery->order->total_amount, 2) }} in cash from the customer.
                                </span>
                            </label>
                        </div>
                    @endif

                    <button type="submit"
                            class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg">
                        Confirm Delivery
                    </button>
                </form>
            </div>

            {{-- Failed Delivery Form --}}
            <div x-show="showFailForm" x-cloak class="bg-white rounded-lg shadow p-4 border-2 border-red-500">
                <h3 class="font-semibold text-gray-800 mb-3 text-sm">Failed Delivery</h3>
                <form method="POST" action="{{ route('agent.delivery.fail', $delivery) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Reason *</label>
                        <select name="failure_reason" required class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-red-500">
                            <option value="">— Select reason —</option>
                            <option value="Customer not available">Customer not available</option>
                            <option value="Wrong/incomplete address">Wrong/incomplete address</option>
                            <option value="Customer refused delivery">Customer refused delivery</option>
                            <option value="Could not contact customer">Could not contact customer</option>
                            <option value="Customer did not have cash">Customer did not have cash (COD)</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <button type="submit"
                            onclick="return confirm('Are you sure this delivery failed?');"
                            class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg">
                        Confirm Failed Delivery
                    </button>
                </form>
            </div>

            @if(in_array($delivery->status, ['delivered', 'failed']))
                <div class="bg-gray-100 rounded-lg p-4 text-center text-sm text-gray-600">
                    This delivery is already {{ $delivery->status_label }}. No further actions available.
                </div>
            @endif
        </div>

    </main>

</body>
</html>