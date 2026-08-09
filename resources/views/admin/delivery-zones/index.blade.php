@extends('admin.layouts.app')

@section('title', 'Delivery Zones')
@section('heading', 'Delivery Zones')
@section('subheading', 'Define where you deliver and how much it costs')

@section('actions')
    <a href="{{ route('admin.delivery-zones.create') }}"
       class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Zone
    </a>
@endsection

@section('content')

    @if($zones->isEmpty())
        <div class="bg-white rounded-lg shadow p-16 text-center">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-700 mb-1">No delivery zones yet</h3>
            <p class="text-sm text-gray-500 mb-6">Add your first delivery zone to enable checkout.</p>
            <a href="{{ route('admin.delivery-zones.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                Add Zone
            </a>
        </div>
    @else
        @foreach($zones as $state => $stateZones)
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">{{ $state }}</h3>

                <div class="space-y-3">
                    @foreach($stateZones as $zone)
                        <div class="bg-white rounded-lg shadow overflow-hidden">

                            {{-- Zone Header --}}
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                                <div>
                                    <div class="flex items-center space-x-3">
                                        <h4 class="font-semibold text-gray-900">{{ $zone->name }}</h4>
                                        <span class="px-2 py-0.5 text-xs rounded {{ $zone->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $zone->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    @if($zone->covered_cities)
                                        <p class="text-xs text-gray-500 mt-1">{{ $zone->covered_cities }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.delivery-zones.edit', $zone) }}"
                                       class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Edit</a>
                                    <form method="POST" action="{{ route('admin.delivery-zones.destroy', $zone) }}"
                                          onsubmit="return confirm('Delete this zone? All its rates will be removed.');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                    </form>
                                </div>
                            </div>

                            {{-- Rates Table --}}
                            <div class="px-6 py-4">
                                @if($zone->rates->isEmpty())
                                    <p class="text-sm text-gray-500 mb-3">No delivery rates set for this zone yet.</p>
                                @else
                                    <table class="w-full mb-3">
                                        <thead class="text-xs uppercase text-gray-500">
                                            <tr>
                                                <th class="text-left py-2">Rate Name</th>
                                                <th class="text-left py-2">Price</th>
                                                <th class="text-left py-2">Days</th>
                                                <th class="text-left py-2">Status</th>
                                                <th class="text-right py-2">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($zone->rates as $rate)
                                                <tr>
                                                    <td class="py-2 text-sm">
                                                        <p class="font-medium text-gray-900">{{ $rate->name }}</p>
                                                        @if($rate->description)
                                                            <p class="text-xs text-gray-500">{{ $rate->description }}</p>
                                                        @endif
                                                    </td>
                                                    <td class="py-2 text-sm font-semibold text-gray-900">₦{{ number_format($rate->price, 2) }}</td>
                                                    <td class="py-2 text-sm text-gray-700">{{ $rate->estimated_days_label }}</td>
                                                    <td class="py-2">
                                                        <span class="px-2 py-0.5 text-xs rounded {{ $rate->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                                            {{ $rate->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td>
                                                    <td class="py-2 text-right">
                                                        <form method="POST" action="{{ route('admin.delivery-rates.destroy', $rate) }}"
                                                              onsubmit="return confirm('Remove this rate?');" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs">Remove</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif

                                {{-- Add Rate Form --}}
                                <details class="mt-2">
                                    <summary class="cursor-pointer text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                        + Add Rate to this Zone
                                    </summary>
                                    <form method="POST" action="{{ route('admin.delivery-rates.store', $zone) }}" class="mt-3 grid grid-cols-1 md:grid-cols-5 gap-2">
                                        @csrf
                                        <input type="text" name="name" placeholder="Rate name (e.g. Standard)" required
                                               class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                        <input type="number" name="price" step="0.01" min="0" placeholder="Price (₦)" required
                                               class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                        <input type="number" name="estimated_days_min" min="1" value="1" required
                                               class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                                               title="Min days">
                                        <input type="number" name="estimated_days_max" min="1" value="3" required
                                               class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                                               title="Max days">
                                        <button type="submit" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                                            Add
                                        </button>
                                    </form>
                                </details>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif

@endsection