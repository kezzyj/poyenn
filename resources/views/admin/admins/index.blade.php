@extends('admin.layouts.app')

@section('title', 'Admins')
@section('heading', 'Admins')
@section('subheading', 'Manage admin accounts')

@section('actions')
    <a href="{{ route('admin.admins.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
        + Add Admin
    </a>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Role</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($admins as $admin)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $admin->name }}</td>
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $admin->email }}</td>
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $admin->role === 'super_admin' ? 'Super Admin' : 'Platform Admin' }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded {{ $admin->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $admin->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right space-x-3">
                                @if($admin->id !== auth('admin')->id())
                                    <form method="POST" action="{{ route('admin.admins.toggle-status', $admin) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-800">
                                            {{ $admin->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" class="inline" onsubmit="return confirm('Delete this admin?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                @else
                                    <span class="text-sm text-gray-400">You</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">{{ $admins->links() }}</div>
    </div>
@endsection