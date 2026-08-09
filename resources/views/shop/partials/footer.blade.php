<footer class="bg-gray-900 text-gray-300 mt-12">
    <div class="max-w-7xl mx-auto px-4 py-12">

        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">

            {{-- Brand --}}
            <div class="col-span-2 md:col-span-1">
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">P</span>
                    </div>
                    <h3 class="text-xl font-bold text-white">Poyenn</h3>
                </div>
                <p class="text-sm text-gray-400 mb-4">Quality electronics from trusted brands, delivered to your doorstep across Nigeria.</p>
            </div>

            {{-- Shop --}}
            <div>
                <h4 class="text-white font-semibold mb-3 text-sm">Shop</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('shop.products.index') }}" class="hover:text-white">All Products</a></li>
                    <li><a href="#" class="hover:text-white">New Arrivals</a></li>
                    <li><a href="#" class="hover:text-white">Best Sellers</a></li>
                    <li><a href="#" class="hover:text-white">On Sale</a></li>
                </ul>
            </div>

            {{-- Help --}}
             <div>
                <h4 class="text-white font-semibold mb-3 text-sm">Help</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('shop.orders.track') }}" class="hover:text-white">Track Order</a></li>
                    <li><a href="#" class="hover:text-white">Contact Us</a></li>
                    <li><a href="#" class="hover:text-white">Delivery Info</a></li>
                    <li><a href="#" class="hover:text-white">Returns</a></li>
                </ul>
            </div>

            {{-- Account --}}
            <div>
                <h4 class="text-white font-semibold mb-3 text-sm">Account</h4>
                <ul class="space-y-2 text-sm">
                    @auth
                        <li><a href="{{ route('shop.orders.index') }}" class="hover:text-white">My Orders</a></li>
                        <li><a href="{{ route('shop.orders.track') }}" class="hover:text-white">Track Order</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="hover:text-white">Sign In</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white">Register</a></li>
                    @endauth
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col md:flex-row items-center justify-between text-sm">
            <p>© {{ date('Y') }} Poyenn. All rights reserved.</p>
            <p class="mt-2 md:mt-0 text-gray-500">Built by <a href="#" class="hover:text-white">KOD Techsource</a></p>
        </div>
    </div>
</footer>