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
        <div class="relative hidden md:block search-container">
            <input type="text"
                   id="header-ledger-search"
                   placeholder="Search for Ledger..."
                   class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                   autocomplete="off">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <!-- Search Results Dropdown -->
            <div id="header-search-results" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-64 overflow-y-auto z-50 hidden fade-in">
                <!-- Results will be populated here -->
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

<style>
/* Header Search Styles */
.search-result-item.active {
    background-color: #f3f4f6;
}

.search-result-item:hover {
    background-color: #f9fafb;
}

#header-search-results {
    max-height: 400px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

#header-search-results::-webkit-scrollbar {
    width: 6px;
}

#header-search-results::-webkit-scrollbar-track {
    background: #f7fafc;
}

#header-search-results::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}

#header-search-results::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

.search-container {
    position: relative;
}

#header-ledger-search:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.fade-in {
    animation: fadeIn 0.2s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
// Header Ledger Search Autocomplete
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('header-ledger-search');
    const searchResults = document.getElementById('header-search-results');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();

            // Clear previous timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            // Hide results if query is too short
            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }

            // Debounce search requests
            searchTimeout = setTimeout(() => {
                performHeaderSearch(query);
            }, 300);
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(event) {
            if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                searchResults.classList.add('hidden');
            }
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(event) {
            const items = searchResults.querySelectorAll('.search-result-item');
            const currentActive = searchResults.querySelector('.search-result-item.active');
            let currentIndex = -1;

            if (currentActive) {
                currentIndex = Array.from(items).indexOf(currentActive);
            }

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                const nextIndex = Math.min(currentIndex + 1, items.length - 1);
                setActiveHeaderItem(items, nextIndex);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                const prevIndex = Math.max(currentIndex - 1, 0);
                setActiveHeaderItem(items, prevIndex);
            } else if (event.key === 'Enter') {
                event.preventDefault();
                if (currentActive) {
                    currentActive.click();
                }
            } else if (event.key === 'Escape') {
                searchResults.classList.add('hidden');
                searchInput.blur();
            }
        });
    }

    function performHeaderSearch(query) {
        // Show loading state
        searchResults.innerHTML = '<div class="p-4 text-center text-gray-500">Searching...</div>';
        searchResults.classList.remove('hidden');

        // Get current tenant from URL or use a global variable
        const pathParts = window.location.pathname.split('/');
        const tenant = pathParts[1]; // Assuming tenant is the first part of the path

        const searchUrl = `/${tenant}/accounting/ledger-accounts/search?q=${encodeURIComponent(query)}`;

        // Make API request - using the correct route
        fetch(searchUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                displayHeaderResults(data);
            })
            .catch(error => {
                console.error('Search error:', error);
                searchResults.innerHTML = '<div class="p-4 text-center text-red-500">Search failed. Please try again.</div>';
            });
    }    function displayHeaderResults(accounts) {
        console.log('Search results:', accounts); // Debug log

        if (!Array.isArray(accounts) || accounts.length === 0) {
            searchResults.innerHTML = '<div class="p-4 text-center text-gray-500">No accounts found</div>';
            return;
        }

        const resultsHtml = accounts.map(account => {
            const balanceClass = account.current_balance >= 0 ? 'text-green-600' : 'text-red-600';
            const balanceType = account.current_balance >= 0 ? 'Dr' : 'Cr';

            return `
                <div class="search-result-item p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" data-url="${account.url}">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <span class="font-medium text-gray-900">${account.name}</span>
                                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600">${account.code}</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                ${account.account_type} • ${account.account_group}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium ${balanceClass}">
                                ₦${new Intl.NumberFormat().format(Math.abs(account.current_balance))} ${balanceType}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        searchResults.innerHTML = resultsHtml;

        // Add click handlers
        searchResults.querySelectorAll('.search-result-item').forEach(item => {
            item.addEventListener('click', function() {
                const url = this.dataset.url;
                window.location.href = url;
            });
        });
    }

    function setActiveHeaderItem(items, index) {
        items.forEach(item => item.classList.remove('active'));
        if (items[index]) {
            items[index].classList.add('active');
            items[index].scrollIntoView({ block: 'nearest' });
        }
    }
});
</script>
