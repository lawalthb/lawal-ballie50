<!-- Payment Modal Content -->
<div class="p-6">
    <!-- Modal Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg flex items-center justify-center">
                <i class="fas fa-credit-card text-white"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Payment</h3>
                <p class="text-sm text-gray-600">Complete the transaction</p>
            </div>
        </div>
        <button @click="showPaymentModal = false"
                class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <!-- Order Summary -->
    <div class="bg-gray-50 rounded-xl p-4 mb-6">
        <h4 class="font-semibold text-gray-900 mb-3">Order Summary</h4>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span>Subtotal:</span>
                <span x-text="'₦' + formatMoney(cartSubtotal)"></span>
            </div>
            <div class="flex justify-between">
                <span>Tax:</span>
                <span x-text="'₦' + formatMoney(cartTax)"></span>
            </div>
            <div class="flex justify-between font-bold text-lg border-t pt-2 text-purple-600">
                <span>Total:</span>
                <span x-text="'₦' + formatMoney(cartTotal)"></span>
            </div>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3">Payment Methods</h4>
        <template x-for="(payment, index) in payments" :key="index">
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                        <select x-model="payment.method_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Select Method</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}">{{ $method->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <input type="number"
                               x-model="payment.amount"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                               min="0"
                               step="0.01"
                               placeholder="0.00">
                    </div>
                </div>
                <div class="mt-3" x-show="payment.method_id && getPaymentMethod(payment.method_id)?.requires_reference">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                    <input type="text"
                           x-model="payment.reference"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Enter reference number">
                </div>
                <div class="mt-3 flex justify-end" x-show="payments.length > 1">
                    <button @click="removePayment(index)"
                            class="text-red-600 hover:text-red-800 text-sm">
                        <i class="fas fa-trash mr-1"></i> Remove
                    </button>
                </div>
            </div>
        </template>

        <button @click="addPayment()"
                class="text-purple-600 hover:text-purple-800 text-sm font-medium">
            <i class="fas fa-plus mr-1"></i> Add Another Payment Method
        </button>
    </div>

    <!-- Payment Summary -->
    <div class="bg-blue-50 rounded-lg p-4 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <span class="text-sm text-gray-600">Total Paid:</span>
                <span class="ml-2 font-semibold text-blue-600" x-text="'₦' + formatMoney(totalPaid)"></span>
            </div>
            <div>
                <span class="text-sm text-gray-600">Balance:</span>
                <span class="ml-2 font-semibold"
                      :class="balance >= 0 ? 'text-green-600' : 'text-red-600'"
                      x-text="'₦' + formatMoney(balance)"></span>
            </div>
        </div>
        <div class="mt-2" x-show="change > 0">
            <span class="text-sm text-gray-600">Change Due:</span>
            <span class="ml-2 font-bold text-green-600 text-lg" x-text="'₦' + formatMoney(change)"></span>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex space-x-3">
        <button @click="showPaymentModal = false"
                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-4 rounded-xl font-semibold transition-colors duration-200">
            Cancel
        </button>
        <button @click="completeSale()"
                :disabled="isProcessing || balance < 0 || totalPaid === 0"
                :class="(isProcessing || balance < 0 || totalPaid === 0) ? 'bg-gray-300 cursor-not-allowed' : 'bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800'"
                class="flex-1 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2">
            <i x-show="!isProcessing" class="fas fa-check"></i>
            <i x-show="isProcessing" class="fas fa-spinner fa-spin"></i>
            <span x-text="isProcessing ? 'Processing...' : 'Complete Sale'"></span>
        </button>
    </div>

    <!-- Quick Amount Buttons -->
    <div class="mt-4">
        <p class="text-sm font-medium text-gray-700 mb-2">Quick Amount</p>
        <div class="grid grid-cols-4 gap-2">
            <template x-for="amount in [500, 1000, 2000, 5000]" :key="amount">
                <button @click="setQuickAmount(amount)"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200"
                        x-text="'₦' + amount"></button>
            </template>
        </div>
        <button @click="setExactAmount()"
                class="w-full mt-2 bg-purple-100 hover:bg-purple-200 text-purple-700 py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200">
            Exact Amount
        </button>
    </div>
</div>

<script>
// Add these methods to the posSystem() function
window.posSystemExtensions = {
    get totalPaid() {
        return this.payments.reduce((sum, payment) => sum + (parseFloat(payment.amount) || 0), 0);
    },

    get balance() {
        return this.totalPaid - this.cartTotal;
    },

    get change() {
        return Math.max(0, this.balance);
    },

    addPayment() {
        this.payments.push({
            method_id: '',
            amount: Math.max(0, this.cartTotal - this.totalPaid),
            reference: ''
        });
    },

    removePayment(index) {
        if (this.payments.length > 1) {
            this.payments.splice(index, 1);
        }
    },

    setQuickAmount(amount) {
        if (this.payments.length > 0) {
            this.payments[0].amount = amount;
        }
    },

    setExactAmount() {
        if (this.payments.length > 0) {
            this.payments[0].amount = this.cartTotal;
        }
    },

    getPaymentMethod(methodId) {
        const methods = @json($paymentMethods);
        return methods.find(method => method.id == methodId);
    }
};

// Extend the main posSystem function with these methods
document.addEventListener('alpine:init', () => {
    Alpine.data('posSystem', () => ({
        ...window.posSystemExtensions,
        // ... other existing methods
    }));
});
</script>
