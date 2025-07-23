<header class="glass-effect shadow-sm border-b border-gray-200 h-20 flex items-center justify-between px-6 sticky top-0 z-20">
    <div class="flex items-center space-x-4">
        <button id="mobileSidebarToggle" class="p-2 rounded-lg hover:bg-gray-100 lg:hidden transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                @yield('page-title', 'Dashboard')
            </h1>
            <p class="text-sm text-gray-500 mt-1">@yield('page-description', 'Welcome back! Here\'s what\'s happening with your business today.')</p>
        </div>
    </div>

    <div class="flex items-center space-x-4">
        <!-- Search -->
        <div class="relative hidden md:block">
            <input type="text" placeholder="Search for Ledger..." class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <!-- Notifications -->
        <button class="relative p-2 rounded-xl hover:bg-gray-100 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full flex items-center justify-center text-xs text-white pulse-animation">3</span>
        </button>

        <!-- User Menu -->
        <div class="relative">
            <button class="flex items-center space-x-3 p-2 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="hidden md:block text-left">
                    <div class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'User' }}</div>
                    <div class="text-xs text-gray-500">{{ auth()->user()->role ?? 'Admin' }}</div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>
    </div>
</header>