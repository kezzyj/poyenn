<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\Platform;
use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    public function index(): View
    {
        $enquiries = Enquiry::with('product')
            ->where('customer_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('shop.enquiries.index', compact('enquiries'));
    }

    public function store(Request $request, Product $product, NotificationService $notifier): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'location' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        $platform = Platform::where('slug', 'poyenn')->firstOrFail();

        $enquiry = Enquiry::create([
            'platform_id' => $platform->id,
            'customer_id' => auth()->id(),
            'product_id' => $product->id,
            'customer_name' => $validated['customer_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'location' => $validated['location'] ?? null,
            'message' => $validated['message'] ?? null,
            'status' => 'new',
        ]);

        $notifier->sendEmail(
            toEmail: config('mail.from.address'),
            toName: 'Poyenn Admin',
            subject: 'New Enquiry: ' . $product->name,
            htmlContent: view('emails.enquiry-admin-alert', ['enquiry' => $enquiry, 'product' => $product])->render(),
        );

        if ($enquiry->email) {
            $notifier->sendEmail(
                toEmail: $enquiry->email,
                toName: $enquiry->customer_name,
                subject: 'We received your enquiry — Poyenn',
                htmlContent: view('emails.enquiry-customer-confirmation', ['enquiry' => $enquiry, 'product' => $product])->render(),
            );
        }

        return back()->with('success', 'Your enquiry has been received. We\'ll get back to you shortly.');
    }
}