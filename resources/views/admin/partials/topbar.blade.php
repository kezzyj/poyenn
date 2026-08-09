<header class="bg-white border-b border-gray-200 sticky top-0 z-10">
    <div class="px-6 py-3 flex items-center justify-between">

        {{-- Mobile menu toggle --}}
        <button id="sidebar-toggle" class="lg:hidden p-2 text-gray-600 hover:bg-gray-100 rounded">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Page breadcrumb / search area --}}
        <div class="hidden lg:flex items-center space-x-2 text-sm text-gray-600">
            <span>Admin</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 font-medium">@yield('title', 'Dashboard')</span>
        </div>

        {{-- Right side --}}
        <div class="flex items-center space-x-4">

            {{-- Notifications --}}
            <button class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            {{-- Profile dropdown --}}
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open" class="flex items-center space-x-2 p-1 hover:bg-gray-100 rounded-lg">
                    <div class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                        {{ substr(auth('admin')->user()->name, 0, 1) }}
                    </div>
                    <span class="hidden md:block text-sm font-medium text-gray-700">{{ auth('admin')->user()->name }}</span>
                    <svg class="hidden md:block w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-900">{{ auth('admin')->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ auth('admin')->user()->email }}</p>
                        <span class="inline-block mt-1 px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs rounded">
                            {{ str_replace('_', ' ', ucfirst(auth('admin')->user()->role)) }}
                        </span>
                    </div>

                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Account Settings</a>

                    <div class="border-t border-gray-100 mt-1">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>
</header>