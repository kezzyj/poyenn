@extends('shop.layouts.app')

@section('title', 'My Enquiries')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">My Enquiries</h1>

    @if($enquiries->isEmpty())
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <p class="text-gray-500">You haven't made any enquiries yet.</p>
            <a href="{{ route('shop.products.index') }}" class="inline-block mt-4 text-indigo-600 hover:text-indigo-800 font-medium">Browse Products</a>
        </div>
    @else
        <div class="bg-white rounded-lg shadow divide-y divide-gray-100">
            @foreach($enquiries as $enquiry)
                <div class="p-4 flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900">{{ $enquiry->product->name ?? 'General enquiry' }}</p>
                        <p class="text-sm text-gray-500">{{ $enquiry->created_at->format('M j, Y') }}</p>
                    </div>
                    @php
                        $statusColors = ['new' => 'yellow', 'contacted' => 'blue', 'quoted' => 'purple', 'closed' => 'green'];
                        $color = $statusColors[$enquiry->status] ?? 'gray';
                    @endphp
                    <span class="px-2 py-1 text-xs font-medium rounded bg-{{ $color }}-100 text-{{ $color }}-700">
                        {{ ucfirst($enquiry->status) }}
                    </span>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $enquiries->links() }}</div>
    @endif
</div>
@endsection