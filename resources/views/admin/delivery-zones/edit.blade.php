@extends('admin.layouts.app')

@section('title', 'Edit Zone')
@section('heading', 'Edit Delivery Zone')

@section('content')

    <form method="POST" action="{{ route('admin.delivery-zones.update', $zone) }}" class="max-w-2xl">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Zone Details</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Zone Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $zone->name) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State <span class="text-red-500">*</span></label>
                    <input type="text" name="state" value="{{ old('state', $zone->state) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Covered Cities</label>
                    <textarea name="covered_cities" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('covered_cities', $zone->covered_cities) }}</textarea>
                </div>

                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $zone->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>

            <div class="mt-6 flex items-center space-x-3">
                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg">
                    Update Zone
                </button>
                <a href="{{ route('admin.delivery-zones.index') }}"
                   class="text-sm text-gray-600 hover:text-gray-800">Cancel</a>
            </div>
        </div>
    </form>

@endsection