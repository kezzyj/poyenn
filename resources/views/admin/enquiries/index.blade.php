@extends('admin.layouts.app')

@section('title', 'Enquiries')
@section('heading', 'Enquiries')
@section('subheading', 'Manage customer quote requests')

@section('content')

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.enquiries.index') }}" class="flex flex-wrap gap-3">
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">All Status</option>
                <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                <option value="quoted" {{ request('status') === 'quoted' ? 'selected' : '' }}>Quoted</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>

            @if(request('status'))
                <a href="{{ route('admin.enquiries.index') }}" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg">✕</a>
            @endif
        </form>
    </div>

    {{-- Enquiries Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($enquiries->isEmpty())
            <div class="p-16 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4v-4z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-700 mb-1">No enquiries found</h3>
                <p class="text-sm text-gray-500">Customer quote requests will appear here.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left">Customer</th>
                            <th class="px-6 py-3 text-left">Phone</th>
                            <th class="px-6 py-3 text-left">Product</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Date</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($enquiries as $enquiry)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm">
                                    <p class="text-gray-900 font-medium">{{ $enquiry->customer_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $enquiry->email }}</p>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-700">{{ $enquiry->phone }}</td>
                                <td class="px-6 py-3 text-sm text-gray-700">{{ $enquiry->product->name ?? 'General enquiry' }}</td>
                                <td class="px-6 py-3">
                                    @php
                                        $statusColors = ['new' => 'yellow', 'contacted' => 'blue', 'quoted' => 'purple', 'closed' => 'green'];
                                        $color = $statusColors[$enquiry->status] ?? 'gray';
                                    @endphp
                                    <span class="inline-block px-2 py-1 text-xs font-medium rounded bg-{{ $color }}-100 text-{{ $color }}-700">
                                        {{ ucfirst($enquiry->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500">
                                    {{ $enquiry->created_at->format('M j, Y') }}<br>
                                    <span class="text-xs">{{ $enquiry->created_at->format('g:i A') }}</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $enquiries->links() }}
            </div>
        @endif
    </div>

@endsection