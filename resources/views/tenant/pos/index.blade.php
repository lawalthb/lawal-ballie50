@extends('layouts.tenant')

@section('title', 'Point of Sale - ' . tenant()->name)

@section('content')
<div x-data="posSystem()" class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- POS Header -->
    <div class="bg-white shadow-lg border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-cash-register text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Point of Sale</h1>
                    @if(isset($activeSession))
                        <p class="text-sm text-gray-600">{{ $activeSession->cashRegister->name }}</p>
                    @endif
                </div>
            </div>
            @if(isset($activeSession))
                <div class="hidden md:flex items-center space-x-4 text-sm">
                    <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full">
                        <i class="fas fa-circle text-xs mr-1"></i>
                        Session Active
                    </div>
                    <span class="text-gray-600">
                        Started: {{ $activeSession->opened_at->format('H:i') }}
                    </span>
                </div>
            @endif
        </div>
        <div class="flex items-center space-x-3">
            @if(isset($activeSession))
                <a href="{{ route('tenant.pos.close-session', ['tenant' => $tenant->slug]) }}"
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="hidden md:inline">Close Session</span>
                </a>
            @else
                <a href="{{ route('tenant.pos.register-session', ['tenant' => $tenant->slug]) }}"
                   class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                    <i class="fas fa-cash-register"></i>
                    <span class="hidden md:inline">Open Session</span>
                </a>
            @endif
        </div>
    </div>

    @if(!isset($activeSession))
        <!-- No Active Session Message -->
        <div class="flex items-center justify-center min-h-[60vh]">
            <div class="text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-cash-register text-gray-400 text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">No Active Session</h2>
                <p class="text-gray-600 mb-6">Please open a cash register session to start selling</p>
                <a href="{{ route('tenant.pos.register-session', ['tenant' => $tenant->slug]) }}"
                   class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold">
                    Open Cash Register Session
                </a>
            </div>
        </div>
    @else
        <!-- Main POS Interface -->
        <div class="flex overflow-hidden" style="height: calc(100vh - 140px);">
            <!-- Product Grid -->
            <div class="flex-1 bg-gray-50 p-6 overflow-y-auto">
                <!-- Search and Filters -->
                <div class="mb-6">
                    <div class="flex flex-col lg:flex-row lg:items-center space-y-4 lg:space-y-0 lg:space-x-4">
                        <div class="flex-1">
                            <div class="relative">
                                <input type="text"
                                       x-model="searchQuery"
                                       @input="filterProducts()"
                                       placeholder="Search products by name, SKU, or barcode..."
                                       class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            @if(isset($categories) && $categories->count() > 0)
                                <select x-model="selectedCategory"
                                        @change="filterProducts()"
                                        class="px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white shadow-sm">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        @if($category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            @endif
                            @if(isset($recentSales) && $recentSales->count() > 0)
                                <button @click="showRecentSales = !showRecentSales"
                                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-xl transition-colors duration-200 flex items-center space-x-2">
                                    <i class="fas fa-history"></i>
                                    <span class="hidden md:inline">Recent</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4" x-show="!showRecentSales">
                    @if(isset($products) && $products->count() > 0)
                        @foreach($products as $product)
                        <div @click="addToCart({{ $product->toJson() }})"
                             class="group bg-white rounded-xl shadow-sm border border-gray-200 p-4 cursor-pointer hover:shadow-lg hover:border-purple-300 transition-all duration-200 transform hover:-translate-y-1">
                            @if($product->images && $product->images->count() > 0)
                                <img src="{{ $product->images->first()->image_url }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-32 object-cover rounded-lg mb-3">
                            @else
                                <div class="w-full h-32 bg-gradient-to-br from-purple-100 to-purple-200 rounded-lg mb-3 flex items-center justify-center">
                                    <i class="fas fa-box text-purple-400 text-2xl"></i>
                                </div>
                            @endif
                            <h3 class="font-semibold text-gray-900 text-sm mb-1 line-clamp-2 group-hover:text-purple-600">{{ $product->name }}</h3>
                            <p class="text-xs text-gray-500 mb-2">{{ $product->sku }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-purple-600">₦{{ number_format($product->selling_price, 2) }}</span>
                                <span class="text-xs {{ $product->stock_quantity > 10 ? 'text-green-600' : ($product->stock_quantity > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $product->stock_quantity }} left
                                </span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-span-full text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-box text-gray-400 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Products Available</h3>
                            <p class="text-gray-500 mb-4">Add products to your inventory to start selling</p>
                            <a href="{{ route('tenant.inventory.products.create', ['tenant' => $tenant->slug]) }}"
                               class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                                Add Products
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Recent Sales -->
                @if(isset($recentSales) && $recentSales->count() > 0)
                    <div x-show="showRecentSales" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-900">Recent Sales</h2>
                            <button @click="showRecentSales = false" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="grid gap-4">
                            @foreach($recentSales as $sale)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-semibold text-gray-900">{{ $sale->sale_number }}</span>
                                    <span class="text-sm text-gray-500">{{ $sale->created_at->format('H:i') }}</span>
                                </div>
                                <div class="text-sm text-gray-600 mb-2">
                                    @if($sale->customer)
                                        {{ $sale->customer->customer_type === 'individual'
                                            ? $sale->customer->first_name . ' ' . $sale->customer->last_name
                                            : $sale->customer->company_name }}
                                    @else
                                        Walk-in Customer
                                    @endif
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-purple-600">₦{{ number_format($sale->total_amount, 2) }}</span>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('tenant.pos.receipt', ['tenant' => $tenant->slug, 'sale' => $sale->id]) }}"
                                           class="text-blue-600 hover:text-blue-800 text-sm" target="_blank">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Cart Sidebar -->
            <div class="w-96 bg-white shadow-2xl border-l border-gray-200 flex flex-col">
                <!-- Cart Header -->
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-purple-600 to-purple-700 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-sm"></i>
                            </div>
                            <h2 class="text-lg font-semibold">Shopping Cart</h2>
                        </div>
                        <button @click="clearCart()"
                                x-show="cartItems.length > 0"
                                class="text-purple-200 hover:text-white text-sm transition-colors duration-200">
                            Clear All
                        </button>
                    </div>
                    <div class="mt-2 text-purple-200 text-sm" x-show="cartItems.length > 0">
                        <span x-text="cartItems.length"></span> item<span x-show="cartItems.length !== 1">s</span> in cart
                    </div>
                </div>

                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div x-show="cartItems.length === 0" class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shopping-cart text-gray-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Cart is Empty</h3>
                        <p class="text-gray-500 text-sm">Add products to start selling</p>
                    </div>

                    <template x-for="(item, index) in cartItems" :key="index">
                        <div class="bg-gray-50 rounded-xl p-4 mb-3 border border-gray-100">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 text-sm" x-text="item.name"></h4>
                                    <p class="text-xs text-gray-500" x-text="item.sku"></p>
                                    <p class="text-xs text-purple-600 font-medium" x-text="'₦' + formatMoney(item.unit_price) + ' each'"></p>
                                </div>
                                <button @click="removeFromCart(index)"
                                        class="text-red-500 hover:text-red-700 p-1">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <button @click="updateQuantity(index, item.quantity - 1)"
                                            class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center transition-colors duration-200">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <input type="number"
                                           x-model="item.quantity"
                                           @input="updateLineTotal(index)"
                                           class="w-16 px-2 py-1 text-center border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-purple-500 focus:border-purple-500"
                                           min="0.01" step="0.01">
                                    <button @click="updateQuantity(index, parseFloat(item.quantity) + 1)"
                                            class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center transition-colors duration-200">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold text-purple-600" x-text="'₦' + formatMoney(item.lineTotal)"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Cart Totals -->
                <div class="border-t border-gray-200 p-4 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span>Subtotal:</span>
                        <span x-text="'₦' + formatMoney(cartSubtotal)"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Tax:</span>
                        <span x-text="'₦' + formatMoney(cartTax)"></span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t pt-3 text-purple-600">
                        <span>Total:</span>
                        <span x-text="'₦' + formatMoney(cartTotal)"></span>
                    </div>
                </div>

                <!-- Customer Selection -->
                <div class="border-t border-gray-200 p-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Customer (Optional)</label>
                    <select x-model="selectedCustomer"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Walk-in Customer</option>
                        @if(isset($customers))
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">
                                    @if($customer->customer_type === 'individual')
                                        {{ $customer->first_name }} {{ $customer->last_name }}
                                    @else
                                        {{ $customer->company_name }}
                                    @endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Checkout Button -->
                <div class="p-4 border-t border-gray-200">
                    <button @click="proceedToPayment()"
                            :disabled="cartItems.length === 0"
                            :class="cartItems.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800'"
                            class="w-full text-white py-3 px-4 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-credit-card"></i>
                        <span>Proceed to Payment</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div x-show="showPaymentModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
             style="display: none;">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <!-- Payment modal content would go here -->
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Payment Processing</h3>
                    <p class="text-gray-600 mb-4">Payment modal functionality to be implemented...</p>
                    <button @click="showPaymentModal = false"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function posSystem() {
    return {
        cartItems: [],
        searchQuery: '',
        selectedCategory: '',
        selectedCustomer: '',
        showPaymentModal: false,
        showRecentSales: false,

        // Computed properties
        get cartSubtotal() {
            return this.cartItems.reduce((sum, item) => sum + (parseFloat(item.quantity) * parseFloat(item.unit_price)), 0);
        },

        get cartTax() {
            return this.cartItems.reduce((sum, item) => {
                const itemSubtotal = parseFloat(item.quantity) * parseFloat(item.unit_price);
                return sum + (itemSubtotal * (parseFloat(item.tax_rate) || 0) / 100);
            }, 0);
        },

        get cartTotal() {
            return this.cartSubtotal + this.cartTax;
        },

        // Methods
        addToCart(product) {
            const existingItem = this.cartItems.find(item => item.id === product.id);

            if (existingItem) {
                this.updateQuantity(this.cartItems.indexOf(existingItem), parseFloat(existingItem.quantity) + 1);
            } else {
                this.cartItems.push({
                    id: product.id,
                    name: product.name,
                    sku: product.sku,
                    quantity: 1,
                    unit_price: parseFloat(product.selling_price),
                    tax_rate: parseFloat(product.tax_rate || 0),
                    stock_quantity: product.stock_quantity,
                    lineTotal: parseFloat(product.selling_price)
                });
            }
        },

        removeFromCart(index) {
            this.cartItems.splice(index, 1);
        },

        updateQuantity(index, newQuantity) {
            if (newQuantity <= 0) {
                this.removeFromCart(index);
                return;
            }

            const item = this.cartItems[index];
            if (newQuantity > item.stock_quantity) {
                alert(`Only ${item.stock_quantity} items available in stock`);
                return;
            }

            item.quantity = newQuantity;
            this.updateLineTotal(index);
        },

        updateLineTotal(index) {
            const item = this.cartItems[index];
            const itemSubtotal = parseFloat(item.quantity) * parseFloat(item.unit_price);
            const itemTax = itemSubtotal * (parseFloat(item.tax_rate) || 0) / 100;
            item.lineTotal = itemSubtotal + itemTax;
        },

        clearCart() {
            if (confirm('Are you sure you want to clear the cart?')) {
                this.cartItems = [];
            }
        },

        proceedToPayment() {
            if (this.cartItems.length === 0) return;
            this.showPaymentModal = true;
        },

        formatMoney(amount) {
            return new Intl.NumberFormat('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount || 0);
        },

        filterProducts() {
            // Product filtering implementation
            console.log('Filtering products...', this.searchQuery, this.selectedCategory);
        }
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endsection
