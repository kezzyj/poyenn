@extends('admin.layouts.app')

@section('title', 'Enquiry Details')
@section('heading', 'Enquiry from ' . $enquiry->customer_name)
@section('subheading', $enquiry->created_at->format('M j, Y \a\t g:i A'))

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Enquiry Details --}}
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Customer</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Name</dt>
                        <dd class="text-gray-900 font-medium">{{ $enquiry->customer_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Phone</dt>
                        <dd class="text-gray-900 font-medium">{{ $enquiry->phone }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Email</dt>
                        <dd class="text-gray-900 font-medium">{{ $enquiry->email ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Location</dt>
                        <dd class="text-gray-900 font-medium">{{ $enquiry->location ?: '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Product</h3>
                @if($enquiry->product)
                    <a href="{{ route('admin.products.show', $enquiry->product) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                        {{ $enquiry->product->name }}
                    </a>
                @else
                    <p class="text-gray-500">General enquiry — no specific product</p>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Message</h3>
                <p class="text-gray-700 whitespace-pre-line">{{ $enquiry->message ?: 'No message provided.' }}</p>
            </div>

        </div>

        {{-- Status & Notes --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Update Status</h3>
                <form method="POST" action="{{ route('admin.enquiries.update-status', $enquiry) }}" class="space-y-4">
                    @csrf
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="new" {{ $enquiry->status === 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ $enquiry->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="quoted" {{ $enquiry->status === 'quoted' ? 'selected' : '' }}>Quoted</option>
                        <option value="closed" {{ $enquiry->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>

                    <textarea name="admin_notes" rows="4" placeholder="Internal notes..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">{{ $enquiry->admin_notes }}</textarea>

                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                        Save
                    </button>
                </form>
            </div>

            <a href="{{ route('admin.enquiries.index') }}" class="block text-center text-sm text-gray-500 hover:text-gray-700">
                ← Back to Enquiries
            </a>
        </div>

    </div>

@endsection