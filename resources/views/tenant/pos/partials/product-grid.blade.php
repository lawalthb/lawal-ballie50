<!-- Search and Filters -->
<div class="mb-6 p-4 bg-white/60 dark:bg-gray-800/40 backdrop-blur-sm rounded-2xl border border-gray-200/80 dark:border-gray-700/50 animate-slide-in-up">
    <div class="flex flex-col lg:flex-row lg:items-center gap-3 lg:gap-4">
        <div class="flex-1 relative">
            <div class="relative">
                <input type="text"
                       x-model="searchQuery"
                       @input.debounce.300ms="filterProducts()"
                       placeholder="Search products by name, SKU, or barcode..."
                       class="w-full pl-12 pr-12 py-3 border border-gray-300/80 dark:border-gray-600/60 rounded-xl focus:ring-2 focus:ring-[var(--color-dark-purple)] focus:border-[var(--color-dark-purple)] bg-white/80 dark:bg-gray-700/60 shadow-sm touch-input dark:text-gray-200 dark:placeholder-gray-400 transition-colors duration-300">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
                </div>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <button @click="toggleScanner()" class="text-gray-400 dark:text-gray-500 hover:text-[var(--color-dark-purple)] dark:hover:text-[var(--color-purple-accent)] focus:outline-none">
                        <i class="fas fa-barcode"></i>
                    </button>
                </div>
            </div>

            <!-- Barcode Scanner (shows when scanner is active) -->
            <div x-show="showScanner" x-transition class="absolute top-full left-0 right-0 mt-2 p-4 bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700 z-10" style="display: none;">
                <div class="text-center space-y-4">
                    <div class="w-full h-12 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-600">
                        <span class="text-gray-500 dark:text-gray-400 text-sm">Scan barcode...</span>
                    </div>
                    <button @click="toggleScanner()" class="px-3 py-2 rounded bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm">
                        <i class="fas fa-times mr-1"></i> Close Scanner
                    </button>
                </div>
            </div>
        </div>

        <div class="flex gap-2 flex-wrap sm:flex-nowrap">
            @if(isset($categories) && $categories->count() > 0)
                <select x-model="selectedCategory"
                        @change="filterProducts()"
                        class="px-4 py-3 border border-gray-300/80 dark:border-gray-600/60 rounded-xl focus:ring-2 focus:ring-[var(--color-dark-purple)] focus:border-[var(--color-dark-purple)] bg-white/80 dark:bg-gray-700/60 shadow-sm text-gray-700 dark:text-gray-200 transition-colors duration-300">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        @if($category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endif
                    @endforeach
                </select>
            @endif

            <button @click="toggleQuickAdd()"
                    :class="quickAddEnabled ? 'bg-primary text-white' : 'btn-outline'"
                    class="px-3 py-3 rounded-xl flex items-center gap-2 transition-all duration-200">
                <i class="fas fa-bolt"></i>
                <span class="hidden md:inline">Quick Add</span>
                <span class="shortcut-label hidden md:inline">Ctrl+B</span>
            </button>

            @if(isset($recentSales) && $recentSales->count() > 0)
                <button @click="showRecentSales = !showRecentSales"
                        class="px-3 py-3 rounded-xl transition-all duration-200 flex items-center gap-2 btn-primary">
                    <i class="fas fa-history"></i>
                    <span class="hidden md:inline">Recent</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Favorite Products Quick Bar (if any) -->
    <div x-show="favoriteProducts.length > 0" class="mt-4 overflow-x-auto" style="display: none;">
        <div class="flex gap-2 pb-2">
            <template x-for="(product, index) in favoriteProducts" :key="index">
                <div @click="addToCart(product)" class="flex-shrink-0 px-3 py-2 bg-white/80 dark:bg-gray-700/50 hover:bg-white dark:hover:bg-gray-600 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-600/50 cursor-pointer transition-all duration-200 group">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 flex items-center justify-center bg-[var(--color-dark-purple)] dark:bg-[var(--color-purple-accent)] rounded-full text-white text-xs">
                            <i class="fas fa-star"></i>
                        </span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-[var(--color-dark-purple)] dark:group-hover:text-[var(--color-purple-accent)]" x-text="product.name.substring(0, 15) + (product.name.length > 15 ? '...' : '')"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<!-- Products Grid/List -->
<div :class="(viewMode || 'grid') === 'grid' ? 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4' : 'space-y-3'"
     class="animate-fade-in"
     x-show="!showRecentSales">
    @if(isset($products) && $products->count() > 0)
        @foreach($products as $product)
        <div @click="quickAddEnabled ? addToCart({{ $product->toJson() }}) : null"
             class="product-card bg-white/80 dark:bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-lg border border-gray-200/80 dark:border-gray-700/50 hover:border-[var(--color-dark-purple)] dark:hover:border-[var(--color-purple-accent)] transition-all duration-200 transform hover:-translate-y-1 touch-grow"
             :class="(viewMode || 'grid') === 'grid' ? 'p-4' : 'list-view'">

            <div class="price-tag">₦{{ number_format($product->selling_price, 0) }}</div>

            <div :class="(viewMode || 'grid') === 'grid' ? '' : 'product-image'">
                @if($product->images && $product->images->count() > 0)
                    <img src="{{ $product->images->first()->image_url }}"
                         alt="{{ $product->name }}"
                         class="w-full h-32 object-cover rounded-lg mb-3">
                @else
                    <div class="w-full h-32 bg-gradient-to-br from-[var(--color-purple-muted)] to-[var(--color-purple-light)] rounded-lg mb-3 flex items-center justify-center">
                        <i class="fas fa-box text-[var(--color-dark-purple)] dark:text-white text-2xl"></i>
                    </div>
                @endif
            </div>

            <div :class="(viewMode || 'grid') === 'grid' ? '' : 'product-info'">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm mb-1 line-clamp-2 group-hover:text-[var(--color-dark-purple)] dark:group-hover:text-[var(--color-purple-accent)]">{{ $product->name }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $product->sku }}</p>
                </div>

                <div class="flex items-center justify-between mt-2">
                    <div class="flex items-center gap-2">
                        <span class="stock-indicator {{ $product->stock_quantity > 10 ? 'bg-green-500' : ($product->stock_quantity > 0 ? 'bg-yellow-500' : 'bg-red-500') }}"></span>
                        <span class="text-xs {{ $product->stock_quantity > 10 ? 'text-green-600 dark:text-green-400' : ($product->stock_quantity > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                            {{ $product->stock_quantity }} left
                        </span>
                    </div>
                </div>

                <div class="card-actions">
                    <div class="flex gap-1">
                        <button @click.stop="addToFavorites({{ $product->toJson() }})" class="w-7 h-7 rounded-full bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:text-[var(--color-dark-purple)] dark:hover:text-[var(--color-purple-accent)] flex items-center justify-center shadow-sm border border-gray-200 dark:border-gray-700">
                            <i class="fas fa-star text-xs"></i>
                        </button>
                        <button @click.stop="addToCart({{ $product->toJson() }})" class="w-7 h-7 rounded-full bg-[var(--color-dark-purple)] dark:bg-[var(--color-purple-accent)] text-white flex items-center justify-center shadow-sm">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <div class="col-span-full text-center py-12 bg-white/80 dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-gray-200/80 dark:border-gray-700/50">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-box text-gray-400 dark:text-gray-500 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Products Available</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Add products to your inventory to start selling</p>
            <a href="{{ route('tenant.inventory.products.create', ['tenant' => $tenant->slug]) }}"
               class="px-4 py-2 rounded-lg btn-primary">
                Add Products
            </a>
        </div>
    @endif
</div>
