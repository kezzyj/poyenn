@extends('admin.layouts.app')

@section('title', 'Delivery Agents')
@section('heading', 'Delivery Agents')
@section('subheading', 'Manage your delivery riders')

@section('actions')
    <a href="{{ route('admin.delivery-agents.create') }}"
       class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Agent
    </a>
@endsection

@section('content')

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.delivery-agents.index') }}" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by name, email, phone..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg">Filter</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.delivery-agents.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($agents->isEmpty())
            <div class="p-16 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-9a4 4 0 11-8 0 4 4 0 018 0zm6 3a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-700 mb-1">No delivery agents yet</h3>
                <p class="text-sm text-gray-500 mb-6">Add your first rider to start assigning deliveries.</p>
                <a href="{{ route('admin.delivery-agents.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Add Agent</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left">Agent</th>
                            <th class="px-6 py-3 text-left">Contact</th>
                            <th class="px-6 py-3 text-left">Vehicle</th>
                            <th class="px-6 py-3 text-left">Completed</th>
                            <th class="px-6 py-3 text-left">Available</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($agents as $agent)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 bg-orange-500 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                                            {{ strtoupper(substr($agent->name, 0, 1)) }}
                                        </div>
                                        <p class="text-sm font-medium text-gray-900">{{ $agent->name }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-sm">
                                    <p class="text-gray-900">{{ $agent->phone }}</p>
                                    <p class="text-xs text-gray-500">{{ $agent->email }}</p>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-700">
                                    @if($agent->vehicle_type)
                                        {{ ucfirst($agent->vehicle_type) }}
                                        @if($agent->vehicle_plate)<br><span class="text-xs text-gray-500">{{ $agent->vehicle_plate }}</span>@endif
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm font-semibold text-gray-900">{{ $agent->completed_count }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-block px-2 py-0.5 text-xs rounded {{ $agent->is_available ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $agent->is_available ? 'Available' : 'Busy' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    <form method="POST" action="{{ route('admin.delivery-agents.toggle-status', $agent) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-block px-2 py-1 text-xs font-medium rounded transition {{ $agent->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                            {{ $agent->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.delivery-agents.edit', $agent) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Edit</a>
                                        <form method="POST" action="{{ route('admin.delivery-agents.destroy', $agent) }}" onsubmit="return confirm('Delete this agent?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">{{ $agents->links() }}</div>
        @endif
    </div>

@endsection