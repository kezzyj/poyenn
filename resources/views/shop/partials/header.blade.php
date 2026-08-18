<header class="bg-white shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4">

        {{-- Top row --}}
        <div class="flex items-center justify-between py-3 gap-4">

            {{-- Mobile menu button --}}
            <button x-data @click="$dispatch('toggle-mobile-menu')"
                    class="lg:hidden p-2 text-gray-700 hover:bg-gray-100 rounded">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Logo --}}
            <a href="{{ route('shop.home') }}" class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-xl">P</span>
                </div>
                <div class="hidden sm:block">
                    <h1 class="text-xl font-bold text-gray-900">Poyenn</h1>
                    <p class="text-xs text-gray-500 -mt-1">Quality Electronics</p>
                </div>
            </a>

            {{-- Search bar --}}
            <form action="{{ route('shop.products.index') }}" method="GET" class="flex-1 max-w-2xl hidden md:block">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search for products, brands, categories..."
                           class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-transparent rounded-lg focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                    <svg class="w-5 h-5 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </form>

            {{-- Right actions --}}
            <div class="flex items-center space-x-2">

                {{-- Cart --}}
                <!-- <a href="{{ route('shop.cart.index') }}" class="relative p-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="absolute -top-1 -right-1 bg-indigo-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                </a> -->

                {{-- Account --}}
                @auth
                    <div x-data="{ open: false }" @click.away="open = false" class="relative">
                        <button @click="open = !open" class="flex items-center space-x-1 p-2 hover:bg-gray-100 rounded-lg">
                            <div class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                                {{ substr(auth()->user()->first_name, 0, 1) }}
                            </div>
                            <svg class="hidden sm:block w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
<a href="{{ route('shop.enquiries.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Enquiries</a>                            
<!-- <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Saved Addresses</a> -->
                            <div class="border-t border-gray-100 mt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Sign Out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:block px-4 py-2 text-sm text-gray-700 hover:text-gray-900">Sign In</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Register</a>
                @endauth

            </div>
        </div>

        {{-- Mobile search --}}
        <form action="{{ route('shop.products.index') }}" method="GET" class="md:hidden pb-3">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search products..."
                       class="w-full pl-10 pr-4 py-2 bg-gray-100 border border-transparent rounded-lg focus:bg-white focus:border-indigo-500 text-sm">
                <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </form>

        {{-- Category nav --}}
        <nav class="hidden lg:block border-t border-gray-100">
            <div class="flex items-center space-x-6 py-3 overflow-x-auto">
                <a href="{{ route('shop.products.index') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600 whitespace-nowrap">All Products</a>
                @foreach($navCategories ?? [] as $category)
                    <a href="{{ route('shop.categories.show', $category->slug) }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600 whitespace-nowrap">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </nav>
    </div>
</header>