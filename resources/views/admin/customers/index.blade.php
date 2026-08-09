@extends('admin.layouts.app')

@section('title', 'Customers')
@section('heading', 'Customers')
@section('subheading', 'View registered customers')

@section('content')

    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase">Total Customers</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase">Verified</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['verified'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase">With Orders</p>
            <p class="text-2xl font-bold text-indigo-600">{{ $stats['with_orders'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by name, email, phone..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <select name="verified" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">All</option>
                <option value="yes" {{ request('verified') === 'yes' ? 'selected' : '' }}>Verified</option>
                <option value="no" {{ request('verified') === 'no' ? 'selected' : '' }}>Unverified</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg">Filter</button>
            @if(request()->hasAny(['search', 'verified']))
                <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($customers->isEmpty())
            <div class="p-16 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-700 mb-1">No customers yet</h3>
                <p class="text-sm text-gray-500">Customers will appear here as they register.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left">Customer</th>
                            <th class="px-6 py-3 text-left">Contact</th>
                            <th class="px-6 py-3 text-left">Orders</th>
                            <th class="px-6 py-3 text-left">Total Spent</th>
                            <th class="px-6 py-3 text-left">Verified</th>
                            <th class="px-6 py-3 text-left">Joined</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($customers as $customer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 bg-indigo-500 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                                            {{ strtoupper(substr($customer->first_name, 0, 1)) }}
                                        </div>
                                        <p class="text-sm font-medium text-gray-900">{{ $customer->full_name }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-sm">
                                    <p class="text-gray-900">{{ $customer->phone ?? '—' }}</p>
                                    <p class="text-xs text-gray-500">{{ $customer->email }}</p>
                                </td>
                                <td class="px-6 py-3 text-sm font-semibold text-gray-900">{{ $customer->orders_count }}</td>
                                <td class="px-6 py-3 text-sm font-semibold text-gray-900">₦{{ number_format($customer->total_spent ?? 0, 2) }}</td>
                                <td class="px-6 py-3">
                                    @if($customer->email_verified_at)
                                        <span class="inline-block px-2 py-0.5 text-xs rounded bg-green-100 text-green-700">Verified</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-600">No</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $customer->created_at->format('M j, Y') }}</td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.customers.show', $customer) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">{{ $customers->links() }}</div>
        @endif
    </div>

@endsection