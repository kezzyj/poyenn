<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History — Poyenn Agent</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-orange-50 min-h-screen pb-20">

    <nav class="bg-orange-500 shadow sticky top-0 z-10">
        <div class="max-w-3xl mx-auto px-4 py-3 flex items-center">
            <a href="{{ route('agent.dashboard') }}" class="text-white mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-base font-bold text-white">Delivery History</h1>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto p-4">

        @if($completedDeliveries->isEmpty())
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-sm text-gray-600">No completed deliveries yet.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($completedDeliveries as $delivery)
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-bold text-gray-900">{{ $delivery->order->order_number }}</p>
                            <span class="px-2 py-1 text-xs font-semibold rounded {{ $delivery->status === 'delivered' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($delivery->status) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-700">{{ $delivery->order->delivery_recipient_name }}</p>
                        <p class="text-xs text-gray-500">{{ $delivery->order->delivery_city }}, {{ $delivery->order->delivery_state }}</p>
                        <p class="text-xs text-gray-400 mt-2">
                            {{ $delivery->status === 'delivered' ? 'Delivered' : 'Failed' }}
                            {{ ($delivery->delivered_at ?? $delivery->failed_at)?->diffForHumans() }}
                        </p>
                        @if($delivery->failure_reason)
                            <p class="text-xs text-red-600 mt-1">Reason: {{ $delivery->failure_reason }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $completedDeliveries->links() }}
            </div>
        @endif

    </main>

</body>
</html>