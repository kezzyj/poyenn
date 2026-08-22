<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $admins = Admin::latest()->paginate(20);
        return view('admin.admins.index', compact('admins'));
    }

    public function create(): View
    {
        return view('admin.admins.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:super_admin,platform_admin',
        ]);

        Admin::create([
            'platform_id' => Platform::first()->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        return redirect()->route('admin.admins.index')->with('success', 'Admin created successfully.');
    }

    public function toggleStatus(Admin $admin): RedirectResponse
    {
        if ($admin->id === auth('admin')->id()) {
            return back()->with('error', "You can't deactivate your own account.");
        }

        $admin->update(['is_active' => !$admin->is_active]);
        return back()->with('success', 'Admin status updated.');
    }

    public function destroy(Admin $admin): RedirectResponse
    {
        if ($admin->id === auth('admin')->id()) {
            return back()->with('error', "You can't delete your own account.");
        }

        $admin->delete();
        return back()->with('success', 'Admin deleted.');
    }
}