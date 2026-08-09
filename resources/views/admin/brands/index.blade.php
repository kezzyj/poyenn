@extends('admin.layouts.app')

@section('title', 'Brands')
@section('heading', 'Brands')
@section('subheading', 'Manage your product brands')

@section('actions')
    <a href="{{ route('admin.brands.create') }}"
       class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Brand
    </a>
@endsection

@section('content')

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.brands.index') }}" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search brands..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>

            <select name="status"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="submit"
                    class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg">
                Filter
            </button>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.brands.index') }}"
                   class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">

        @if($brands->isEmpty())
            <div class="p-16 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-700 mb-1">No brands yet</h3>
                <p class="text-sm text-gray-500 mb-6">Add your first brand to start tagging products.</p>
                <a href="{{ route('admin.brands.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                    Create Brand
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left">Logo</th>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Products</th>
                            <th class="px-6 py-3 text-left">Featured</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($brands as $brand)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">
                                    @if($brand->logo)
                                        <img src="{{ asset('storage/' . $brand->logo) }}"
                                             alt="{{ $brand->name }}"
                                             class="w-12 h-12 rounded-lg object-contain bg-gray-50 p-1">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 font-semibold">
                                            {{ strtoupper(substr($brand->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $brand->name }}</p>
                                    <p class="text-xs text-gray-500">/{{ $brand->slug }}</p>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-700">
                                    <span class="font-semibold">{{ $brand->products_count }}</span>
                                </td>
                                <td class="px-6 py-3">
                                    @if($brand->is_featured)
                                        <span class="text-yellow-600">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.151c.969 0 1.371 1.24.588 1.81l-3.36 2.442a1 1 0 00-.364 1.118l1.287 3.957c.3.922-.755 1.688-1.54 1.118l-3.36-2.442a1 1 0 00-1.176 0l-3.36 2.442c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.075 9.384c-.783-.57-.38-1.81.588-1.81h4.15a1 1 0 00.951-.69l1.286-3.957z"/>
                                            </svg>
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    <form method="POST" action="{{ route('admin.brands.toggle-status', $brand) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="inline-block px-2 py-1 text-xs font-medium rounded transition {{ $brand->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                            {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.brands.edit', $brand) }}"
                                           class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}"
                                              onsubmit="return confirm('Are you sure?');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $brands->links() }}
            </div>
        @endif

    </div>

@endsection