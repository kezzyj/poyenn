<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryRate;
use App\Models\DeliveryZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeliveryRateController extends Controller
{
    public function store(Request $request, DeliveryZone $deliveryZone): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($deliveryZone->platform_id !== $platformId, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'estimated_days_min' => 'required|integer|min:1',
            'estimated_days_max' => 'required|integer|min:1|gte:estimated_days_min',
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['delivery_zone_id'] = $deliveryZone->id;
        $validated['is_active'] = $request->boolean('is_active', true);

        DeliveryRate::create($validated);

        return back()->with('success', 'Delivery rate added successfully.');
    }

    public function update(Request $request, DeliveryRate $deliveryRate): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($deliveryRate->zone->platform_id !== $platformId, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'estimated_days_min' => 'required|integer|min:1',
            'estimated_days_max' => 'required|integer|min:1|gte:estimated_days_min',
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $deliveryRate->update($validated);

        return back()->with('success', 'Delivery rate updated.');
    }

    public function destroy(DeliveryRate $deliveryRate): RedirectResponse
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($deliveryRate->zone->platform_id !== $platformId, 403);

        $deliveryRate->delete();

        return back()->with('success', 'Delivery rate removed.');
    }
}