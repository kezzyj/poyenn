<div x-data="{ open: false }"
     @toggle-mobile-menu.window="open = !open"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 lg:hidden">

    {{-- Overlay --}}
    <div @click="open = false" class="absolute inset-0 bg-black bg-opacity-50"></div>

    {{-- Drawer --}}
    <div class="absolute left-0 top-0 bottom-0 w-72 bg-white overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0">

        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Menu</h2>
            <button @click="open = false" class="p-1 hover:bg-gray-100 rounded">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="p-4 space-y-1">
            <a href="{{ route('shop.home') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">Home</a>
            <a href="{{ route('shop.products.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">All Products</a>

            <div class="pt-3">
                <p class="px-3 text-xs uppercase font-semibold text-gray-500 mb-1">Categories</p>
                @foreach($navCategories ?? [] as $category)
                    <a href="{{ route('shop.categories.show', $category->slug) }}"
                       class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>

            <div class="pt-3 border-t border-gray-100 mt-3">
                @auth
                    <a href="{{ route('shop.orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Orders</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded">Sign Out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">Sign In</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 text-sm text-indigo-600 font-medium hover:bg-indigo-50 rounded">Register</a>
                @endauth
            </div>
        </nav>
    </div>
</div>