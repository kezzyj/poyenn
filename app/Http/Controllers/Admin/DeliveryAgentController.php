<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DeliveryAgentController extends Controller
{
    public function index(Request $request): View
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;

        $query = DeliveryAgent::where('platform_id', $platformId)
            ->withCount(['deliveries as completed_count' => fn($q) => $q->where('status', 'delivered')]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $agents = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.delivery-agents.index', compact('agents'));
    }

    public function create(): View
    {
        return view('admin.delivery-agents.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:delivery_agents,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'vehicle_type' => 'nullable|string|max:50',
            'vehicle_plate' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'is_available' => 'nullable|boolean',
        ]);

        $validated['platform_id'] = $platformId;
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_available'] = $request->boolean('is_available', true);

        DeliveryAgent::create($validated);

        return redirect()->route('admin.delivery-agents.index')
            ->with('success', 'Delivery agent created successfully.');
    }

    public function edit(DeliveryAgent $deliveryAgent): View
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($deliveryAgent->platform_id !== $platformId, 403);

        return view('admin.delivery-agents.edit', ['agent' => $deliveryAgent]);
    }

    public function update(Request $request, DeliveryAgent $deliveryAgent): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($deliveryAgent->platform_id !== $platformId, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:delivery_agents,email,' . $deliveryAgent->id,
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'vehicle_type' => 'nullable|string|max:50',
            'vehicle_plate' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'is_available' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_available'] = $request->boolean('is_available');

        // Only update password if a new one was provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $deliveryAgent->update($validated);

        return redirect()->route('admin.delivery-agents.index')
            ->with('success', 'Delivery agent updated successfully.');
    }

    public function destroy(DeliveryAgent $deliveryAgent): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($deliveryAgent->platform_id !== $platformId, 403);

        // Don't delete agents with active deliveries
        $activeDeliveries = $deliveryAgent->deliveries()
            ->whereIn('status', ['assigned', 'picked_up', 'in_transit'])
            ->count();

        if ($activeDeliveries > 0) {
            return back()->with('error', 'Cannot delete an agent with active deliveries. Reassign them first.');
        }

        $deliveryAgent->delete();

        return redirect()->route('admin.delivery-agents.index')
            ->with('success', 'Delivery agent deleted.');
    }

    public function toggleStatus(DeliveryAgent $deliveryAgent): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($deliveryAgent->platform_id !== $platformId, 403);

        $deliveryAgent->update(['is_active' => !$deliveryAgent->is_active]);

        return back()->with('success', 'Agent status updated.');
    }
}