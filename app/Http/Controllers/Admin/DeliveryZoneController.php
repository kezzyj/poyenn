<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryZoneController extends Controller
{
    public function index(): View
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;

        $zones = DeliveryZone::where('platform_id', $platformId)
            ->with(['rates' => fn($q) => $q->orderBy('price')])
            ->orderBy('state')
            ->orderBy('name')
            ->get()
            ->groupBy('state');

        return view('admin.delivery-zones.index', compact('zones'));
    }

    public function create(): View
    {
        return view('admin.delivery-zones.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state' => 'required|string|max:100',
            'covered_cities' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['platform_id'] = $platformId;
        $validated['is_active'] = $request->boolean('is_active', true);

        DeliveryZone::create($validated);

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'Delivery zone created successfully.');
    }

    public function edit(DeliveryZone $deliveryZone): View
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($deliveryZone->platform_id !== $platformId, 403);

        $deliveryZone->load(['rates' => fn($q) => $q->orderBy('price')]);

        return view('admin.delivery-zones.edit', ['zone' => $deliveryZone]);
    }

    public function update(Request $request, DeliveryZone $deliveryZone): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($deliveryZone->platform_id !== $platformId, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state' => 'required|string|max:100',
            'covered_cities' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $deliveryZone->update($validated);

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'Delivery zone updated successfully.');
    }

    public function destroy(DeliveryZone $deliveryZone): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($deliveryZone->platform_id !== $platformId, 403);

        $deliveryZone->delete();

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'Delivery zone deleted.');
    }
}