<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard — Poyenn Agent</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-orange-50 min-h-screen pb-20">

    {{-- Top Bar --}}
    <nav class="bg-orange-500 shadow sticky top-0 z-10">
        <div class="max-w-3xl mx-auto px-4 py-3 flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-white">Poyenn Agent</h1>
                <p class="text-xs text-orange-100">{{ auth('agent')->user()->name }}</p>
            </div>

            <div class="flex items-center space-x-2">
                <a href="{{ route('agent.history') }}" class="px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-medium rounded">
                    History
                </a>
                <form method="POST" action="{{ route('agent.logout') }}">
                    @csrf
                    <button class="px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-medium rounded">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto p-4">

        {{-- Flash --}}
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-3 mb-4 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 mb-4 rounded text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 gap-3 mb-5">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-xs text-gray-500 uppercase">Assigned</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['assigned'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-xs text-gray-500 uppercase">In Progress</p>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['in_progress'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-xs text-gray-500 uppercase">Done Today</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['completed_today'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-xs text-gray-500 uppercase">Total Done</p>
                <p class="text-3xl font-bold text-gray-700">{{ $stats['completed_total'] }}</p>
            </div>
        </div>

        {{-- Active Deliveries --}}
        <h2 class="font-semibold text-gray-800 mb-3">Active Deliveries</h2>

        @if($activeDeliveries->isEmpty())
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1"/>
                </svg>
                <p class="text-sm text-gray-600">No active deliveries right now.</p>
                <p class="text-xs text-gray-400 mt-1">Take a break — you've earned it!</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($activeDeliveries as $delivery)
                    <a href="{{ route('agent.delivery.show', $delivery) }}"
                       class="block bg-white rounded-lg shadow hover:shadow-md transition overflow-hidden">

                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $delivery->order->order_number }}</p>
                                <p class="text-xs text-gray-500">{{ $delivery->order->items->count() }} item(s) • ₦{{ number_format($delivery->order->total_amount, 0) }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded
                                @if($delivery->status === 'assigned') bg-yellow-100 text-yellow-700
                                @elseif($delivery->status === 'picked_up') bg-blue-100 text-blue-700
                                @else bg-purple-100 text-purple-700 @endif">
                                {{ $delivery->status_label }}
                            </span>
                        </div>

                        <div class="px-4 py-3">
                            <p class="text-sm font-medium text-gray-900">{{ $delivery->order->delivery_recipient_name }}</p>
                            <p class="text-xs text-gray-600">{{ $delivery->order->delivery_phone }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $delivery->order->delivery_address_line_1 }}, {{ $delivery->order->delivery_city }}</p>

                            @if($delivery->order->is_cod)
                                <p class="text-xs mt-2 font-semibold text-orange-700">
                                    💰 Collect Cash: ₦{{ number_format($delivery->order->total_amount, 0) }}
                                </p>
                            @else
                                <p class="text-xs mt-2 text-green-700">
                                    ✓ Already paid online
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </main>

</body>
</html>