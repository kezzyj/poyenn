@extends('shop.layouts.app')

@section('title', 'Checkout — Poyenn')

@section('content')

    <div class="max-w-7xl mx-auto px-4 py-6"
         x-data="checkoutForm({{ $defaultAddress?->id ?? 'null' }})">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500 mb-4">
            <a href="{{ route('shop.home') }}" class="hover:text-indigo-600">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('shop.cart.index') }}" class="hover:text-indigo-600">Cart</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">Checkout</span>
        </nav>

        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">Checkout</h1>

        @if (session('warning'))
            <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800 p-3 rounded text-sm mb-4">
                {{ session('warning') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Step 1: Delivery Address --}}
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="font-semibold text-gray-900 flex items-center">
                                <span class="w-7 h-7 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm mr-2">1</span>
                                Delivery Address
                            </h2>
                            <button type="button" @click="showAddAddress = !showAddAddress"
                                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                <span x-text="showAddAddress ? 'Cancel' : '+ Add New Address'"></span>
                            </button>
                        </div>

                        @if($addresses->isEmpty())
                            <p class="text-sm text-gray-500 mb-4">You don't have any saved addresses yet. Add one below.</p>
                        @else
                            <div class="space-y-2 mb-4" x-show="!showAddAddress">
                                @foreach($addresses as $address)
                                    <label class="flex items-start p-3 border rounded-lg cursor-pointer transition"
                                           :class="selectedAddressId === {{ $address->id }} ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                        <input type="radio" name="address_radio" value="{{ $address->id }}"
                                               x-model.number="selectedAddressId"
                                               class="mt-0.5 text-indigo-600 focus:ring-indigo-500">
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm font-semibold text-gray-900">{{ $address->label }}</span>
                                                @if($address->is_default)
                                                    <span class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded">Default</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-700 mt-0.5">{{ $address->recipient_name }} — {{ $address->phone }}</p>
                                            <p class="text-sm text-gray-600">{{ $address->full_address }}</p>
                                            @if($address->landmark)
                                                <p class="text-xs text-gray-500">Landmark: {{ $address->landmark }}</p>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        {{-- Add New Address Form --}}
                        <div x-show="showAddAddress || {{ $addresses->isEmpty() ? 'true' : 'false' }}" x-cloak class="border-t border-gray-100 pt-4">
                            <form method="POST" action="{{ route('shop.checkout.address.store') }}">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Label</label>
                                        <input type="text" name="label" placeholder="Home, Office, etc." value="Home"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Recipient Name *</label>
                                        <input type="text" name="recipient_name" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Phone *</label>
                                        <input type="tel" name="phone" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Address Line 1 *</label>
                                        <input type="text" name="address_line_1" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Address Line 2</label>
                                        <input type="text" name="address_line_2"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">City *</label>
                                        <input type="text" name="city" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">State *</label>
                                        <input type="text" name="state" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Landmark (optional)</label>
                                        <input type="text" name="landmark" placeholder="Nearby notable place"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="flex items-center text-sm">
                                            <input type="checkbox" name="is_default" value="1"
                                                   class="text-indigo-600 focus:ring-indigo-500 rounded">
                                            <span class="ml-2 text-gray-700">Set as default address</span>
                                        </label>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <button type="submit"
                                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg">
                                            Save Address
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Step 2: Delivery Method --}}
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h2 class="font-semibold text-gray-900 mb-4 flex items-center">
                            <span class="w-7 h-7 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm mr-2">2</span>
                            Delivery Method
                        </h2>

                        @if($deliveryZones->isEmpty())
                            <p class="text-sm text-red-600">No delivery zones available. Please contact us.</p>
                        @else
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Zone</label>
                                    <select x-model.number="selectedZoneId" @change="fetchRates()"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                        <option value="">— Select your delivery zone —</option>
                                        @foreach($deliveryZones->groupBy('state') as $state => $zones)
                                            <optgroup label="{{ $state }}">
                                                @foreach($zones as $zone)
                                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>

                                <div x-show="rates.length > 0" x-cloak>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Choose Rate</label>
                                    <div class="space-y-2">
                                        <template x-for="rate in rates" :key="rate.id">
                                            <label class="flex items-center p-3 border rounded-lg cursor-pointer transition"
                                                   :class="selectedRateId === rate.id ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                                <input type="radio" :value="rate.id" x-model.number="selectedRateId"
                                                       class="text-indigo-600 focus:ring-indigo-500">
                                                <div class="ml-3 flex-1 flex items-center justify-between">
                                                    <div>
                                                        <p class="text-sm font-semibold text-gray-900" x-text="rate.name"></p>
                                                        <p class="text-xs text-gray-500" x-text="rate.estimated_days_label"></p>
                                                    </div>
                                                    <p class="text-sm font-bold text-gray-900" x-text="rate.price_formatted"></p>
                                                </div>
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                <p x-show="selectedZoneId && rates.length === 0 && !loadingRates" x-cloak
                                   class="text-sm text-gray-500">No delivery rates configured for this zone.</p>

                                <p x-show="loadingRates" x-cloak class="text-sm text-gray-500">Loading rates...</p>
                            </div>
                        @endif
                    </div>

                    {{-- Step 3: Payment Method --}}
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h2 class="font-semibold text-gray-900 mb-4 flex items-center">
                            <span class="w-7 h-7 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm mr-2">3</span>
                            Payment Method
                        </h2>

                        <div class="space-y-2">
                            <label class="flex items-start p-3 border rounded-lg cursor-pointer transition"
                                   :class="selectedPaymentMethod === 'flutterwave' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" value="flutterwave" x-model="selectedPaymentMethod"
                                       class="mt-0.5 text-indigo-600 focus:ring-indigo-500">
                                <div class="ml-3">
                                    <p class="text-sm font-semibold text-gray-900">Pay Online (Flutterwave)</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Card, USSD, Bank Transfer, Mobile Money</p>
                                </div>
                            </label>

                            <label class="flex items-start p-3 border rounded-lg cursor-pointer transition"
                                   :class="selectedPaymentMethod === 'cash_on_delivery' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" value="cash_on_delivery" x-model="selectedPaymentMethod"
                                       class="mt-0.5 text-indigo-600 focus:ring-indigo-500">
                                <div class="ml-3">
                                    <p class="text-sm font-semibold text-gray-900">Cash on Delivery</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Pay with cash when your order arrives</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Right Column - Order Summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg border border-gray-200 p-6 lg:sticky lg:top-24">
                        <h2 class="font-semibold text-gray-900 mb-4">Order Summary</h2>

                        <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                            @foreach($cart->items as $item)
                                <div class="flex items-center space-x-3">
                                    <div class="relative flex-shrink-0">
                                        @if($item->product->images->first())
                                            <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}"
                                                 class="w-12 h-12 rounded object-cover">
                                        @else
                                            <div class="w-12 h-12 bg-gray-100 rounded"></div>
                                        @endif
                                        <span class="absolute -top-1 -right-1 bg-gray-700 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $item->quantity }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-900 line-clamp-1">{{ $item->product->name }}</p>
                                        <p class="text-xs text-gray-500">₦{{ number_format($item->price, 2) }}</p>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900">₦{{ number_format($item->subtotal, 0) }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-gray-100 pt-4 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium text-gray-900">₦{{ number_format($cart->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Delivery</span>
                                <span class="font-medium text-gray-900" x-text="deliveryFeeFormatted"></span>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 mt-4 pt-4">
                            <div class="flex justify-between items-baseline">
                                <span class="text-gray-900 font-semibold">Total</span>
                                <span class="text-2xl font-bold text-gray-900" x-text="totalFormatted"></span>
                            </div>
                        </div>

                        <button type="button"
                                @click="placeOrder()"
                                x-bind:disabled="!canPlaceOrder"
                                x-bind:class="canPlaceOrder ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-gray-300 cursor-not-allowed text-white'"
                                class="block w-full mt-6 px-6 py-3 text-white text-center font-semibold rounded-lg transition">
                            <span x-show="!isPlacing">Place Order</span>
                            <span x-show="isPlacing" x-cloak>Placing Order...</span>
                        </button>

                        <p class="text-xs text-gray-500 text-center mt-3">
                            By placing this order, you agree to our terms.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function checkoutForm(defaultAddressId) {
                const cartSubtotal = {{ $cart->subtotal }};

                return {
                    selectedAddressId: defaultAddressId,
                    selectedZoneId: null,
                    selectedRateId: null,
                    selectedPaymentMethod: null,
                    rates: [],
                    loadingRates: false,
                    showAddAddress: false,
                    isPlacing: false,

                    async fetchRates() {
                        this.rates = [];
                        this.selectedRateId = null;
                        if (!this.selectedZoneId) return;

                        this.loadingRates = true;
                        try {
                            const response = await fetch(`/checkout/zones/${this.selectedZoneId}/rates`);
                            const data = await response.json();
                            this.rates = data.rates;
                            if (this.rates.length === 1) {
                                this.selectedRateId = this.rates[0].id;
                            }
                        } catch (err) {
                            console.error(err);
                        } finally {
                            this.loadingRates = false;
                        }
                    },

                    get deliveryFee() {
                        if (!this.selectedRateId) return 0;
                        const rate = this.rates.find(r => r.id === this.selectedRateId);
                        return rate ? rate.price : 0;
                    },

                    get deliveryFeeFormatted() {
                        if (!this.selectedRateId) return '— Select zone —';
                        return '₦' + this.deliveryFee.toLocaleString('en-NG', { minimumFractionDigits: 2 });
                    },

                    get total() {
                        return cartSubtotal + this.deliveryFee;
                    },

                    get totalFormatted() {
                        return '₦' + this.total.toLocaleString('en-NG', { minimumFractionDigits: 2 });
                    },

                    get canPlaceOrder() {
                        return this.selectedAddressId
                            && this.selectedZoneId
                            && this.selectedRateId
                            && this.selectedPaymentMethod
                            && !this.isPlacing;
                    },

                    async placeOrder() {
                        if (!this.canPlaceOrder || this.isPlacing) return;
                        this.isPlacing = true;

                        try {
                            const response = await fetch('{{ route('shop.checkout.place-order') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: JSON.stringify({
                                    address_id: this.selectedAddressId,
                                    delivery_zone_id: this.selectedZoneId,
                                    delivery_rate_id: this.selectedRateId,
                                    payment_method: this.selectedPaymentMethod,
                                }),
                            });

                            const data = await response.json();

                            if (data.success && data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                this.isPlacing = false;
                                alert(data.message || 'Could not place order. Please try again.');
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                }
                            }
                        } catch (err) {
                            console.error(err);
                            this.isPlacing = false;
                            alert('Something went wrong. Please try again.');
                        }
                    }
                }
            }
        </script>
    @endpush

@endsection