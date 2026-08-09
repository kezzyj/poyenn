<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;

        $query = Customer::where('platform_id', $platformId)
            ->withCount('orders')
            ->withSum(['orders as total_spent' => fn($q) => $q->where('payment_status', 'paid')], 'total_amount');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('verified')) {
            if ($request->verified === 'yes') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        $customers = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => Customer::where('platform_id', $platformId)->count(),
            'verified' => Customer::where('platform_id', $platformId)->whereNotNull('email_verified_at')->count(),
            'with_orders' => Customer::where('platform_id', $platformId)->has('orders')->count(),
        ];

        return view('admin.customers.index', compact('customers', 'stats'));
    }

    public function show(Customer $customer): View
    {
        $platformId = auth('admin')->user()->platform_id ?? 1;
        abort_if($customer->platform_id !== $platformId, 403);

        $customer->load(['orders' => fn($q) => $q->latest()->with('items')]);

        $orderStats = [
            'total_orders' => $customer->orders->count(),
            'total_spent' => $customer->orders->where('payment_status', 'paid')->sum('total_amount'),
            'pending_orders' => $customer->orders->whereIn('status', ['pending', 'confirmed', 'packed', 'out_for_delivery'])->count(),
        ];

        return view('admin.customers.show', compact('customer', 'orderStats'));
    }
}