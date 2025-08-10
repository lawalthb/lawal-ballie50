@extends('layouts.tenant')

@section('title', 'Close Cash Register Session - ' . $tenant->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-r from-red-600 to-red-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-cash-register text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Close Cash Register Session</h1>
            <p class="text-gray-600">Count your cash drawer and close the session</p>
        </div>

        <!-- Session Summary -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <i class="fas fa-chart-line mr-3"></i>
                    Session Summary
                </h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Session Info -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Session Details</h3>
                        <div class="space-y-1 text-sm">
                            <p><span class="text-gray-600">Register:</span> {{ $activeSession->cashRegister->name }}</p>
                            <p><span class="text-gray-600">Started:</span> {{ $activeSession->opened_at->format('M d, Y H:i') }}</p>
                            <p><span class="text-gray-600">Duration:</span> {{ $activeSession->opened_at->diffForHumans(null, true) }}</p>
                        </div>
                    </div>

                    <!-- Opening Balance -->
                    <div class="bg-green-50 rounded-xl p-4">
                        <h3 class="font-semibold text-green-900 mb-2">Opening Balance</h3>
                        <p class="text-2xl font-bold text-green-600">₦{{ number_format($activeSession->opening_balance, 2) }}</p>
                    </div>

                    <!-- Total Sales -->
                    <div class="bg-blue-50 rounded-xl p-4">
                        <h3 class="font-semibold text-blue-900 mb-2">Total Sales</h3>
                        <p class="text-2xl font-bold text-blue-600">₦{{ number_format($activeSession->total_sales, 2) }}</p>
                        <p class="text-xs text-blue-500">{{ $activeSession->sales->count() }} transactions</p>
                    </div>

                    <!-- Expected Cash -->
                    <div class="bg-purple-50 rounded-xl p-4">
                        <h3 class="font-semibold text-purple-900 mb-2">Expected Cash</h3>
                        <p class="text-2xl font-bold text-purple-600">₦{{ number_format($activeSession->opening_balance + $activeSession->total_cash_sales, 2) }}</p>
                        <p class="text-xs text-purple-500">Opening + Cash Sales</p>
                    </div>
                </div>

                <!-- Recent Transactions -->
                @if($activeSession->sales->count() > 0)
                    <div class="mt-6">
                        <h3 class="font-semibold text-gray-900 mb-3">Recent Transactions</h3>
                        <div class="bg-gray-50 rounded-xl p-4 max-h-64 overflow-y-auto">
                            <div class="space-y-2">
                                @foreach($activeSession->sales->take(10) as $sale)
                                    <div class="flex items-center justify-between bg-white rounded-lg p-3">
                                        <div>
                                            <span class="font-medium">{{ $sale->sale_number }}</span>
                                            <span class="text-gray-500 text-sm ml-2">{{ $sale->created_at->format('H:i') }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-semibold">₦{{ number_format($sale->total_amount, 2) }}</span>
                                            <div class="text-xs text-gray-500">
                                                @if($sale->customer)
                                                    {{ $sale->customer->customer_type === 'individual'
                                                        ? $sale->customer->first_name . ' ' . $sale->customer->last_name
                                                        : $sale->customer->company_name }}
                                                @else
                                                    Walk-in Customer
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Close Session Form -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <i class="fas fa-times-circle mr-3"></i>
                    Close Session
                </h2>
            </div>

            <form action="{{ route('tenant.pos.store-close-session', ['tenant' => $tenant->slug]) }}" method="POST" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Actual Cash Count -->
                    <div>
                        <label for="closing_balance" class="block text-sm font-medium text-gray-700 mb-2">
                            Actual Cash Count (₦) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="closing_balance"
                               id="closing_balance"
                               step="0.01"
                               min="0"
                               required
                               value="{{ old('closing_balance') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="Count your cash drawer">
                        @error('closing_balance')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Difference Indicator -->
                        <div id="difference-indicator" class="mt-2 hidden">
                            <div id="difference-positive" class="text-green-600 text-sm hidden">
                                <i class="fas fa-arrow-up mr-1"></i>
                                <span>Overage: ₦</span><span id="difference-amount-positive"></span>
                            </div>
                            <div id="difference-negative" class="text-red-600 text-sm hidden">
                                <i class="fas fa-arrow-down mr-1"></i>
                                <span>Shortage: ₦</span><span id="difference-amount-negative"></span>
                            </div>
                            <div id="difference-exact" class="text-green-600 text-sm hidden">
                                <i class="fas fa-check mr-1"></i>
                                <span>Perfect balance!</span>
                            </div>
                        </div>
                    </div>

                    <!-- Expected vs Actual -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">Balance Check</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>Expected Cash:</span>
                                <span class="font-medium">₦{{ number_format($activeSession->opening_balance + $activeSession->total_cash_sales, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Actual Count:</span>
                                <span class="font-medium" id="actual-count">₦0.00</span>
                            </div>
                            <div class="flex justify-between font-bold border-t pt-2">
                                <span>Difference:</span>
                                <span id="difference-display">₦0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Closing Notes -->
                <div class="mt-6">
                    <label for="closing_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Closing Notes
                    </label>
                    <textarea name="closing_notes"
                              id="closing_notes"
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                              placeholder="Any notes about discrepancies, issues, or observations...">{{ old('closing_notes') }}</textarea>
                    @error('closing_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cash Counting Helper -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h3 class="text-blue-900 font-semibold mb-3">Cash Counting Helper</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <label class="block text-blue-700 font-medium mb-1">₦1000 Notes:</label>
                            <input type="number" id="notes-1000" min="0" class="w-full px-2 py-1 border border-blue-300 rounded" onchange="calculateTotal()">
                        </div>
                        <div>
                            <label class="block text-blue-700 font-medium mb-1">₦500 Notes:</label>
                            <input type="number" id="notes-500" min="0" class="w-full px-2 py-1 border border-blue-300 rounded" onchange="calculateTotal()">
                        </div>
                        <div>
                            <label class="block text-blue-700 font-medium mb-1">₦200 Notes:</label>
                            <input type="number" id="notes-200" min="0" class="w-full px-2 py-1 border border-blue-300 rounded" onchange="calculateTotal()">
                        </div>
                        <div>
                            <label class="block text-blue-700 font-medium mb-1">₦100 Notes:</label>
                            <input type="number" id="notes-100" min="0" class="w-full px-2 py-1 border border-blue-300 rounded" onchange="calculateTotal()">
                        </div>
                        <div>
                            <label class="block text-blue-700 font-medium mb-1">₦50 Notes:</label>
                            <input type="number" id="notes-50" min="0" class="w-full px-2 py-1 border border-blue-300 rounded" onchange="calculateTotal()">
                        </div>
                        <div>
                            <label class="block text-blue-700 font-medium mb-1">₦20 Notes:</label>
                            <input type="number" id="notes-20" min="0" class="w-full px-2 py-1 border border-blue-300 rounded" onchange="calculateTotal()">
                        </div>
                        <div>
                            <label class="block text-blue-700 font-medium mb-1">₦10 Notes:</label>
                            <input type="number" id="notes-10" min="0" class="w-full px-2 py-1 border border-blue-300 rounded" onchange="calculateTotal()">
                        </div>
                        <div>
                            <label class="block text-blue-700 font-medium mb-1">₦5 Notes:</label>
                            <input type="number" id="notes-5" min="0" class="w-full px-2 py-1 border border-blue-300 rounded" onchange="calculateTotal()">
                        </div>
                    </div>
                    <div class="mt-3 flex justify-between items-center">
                        <span class="text-blue-700 font-medium">Total from helper:</span>
                        <span id="helper-total" class="text-blue-900 font-bold text-lg">₦0.00</span>
                    </div>
                    <button type="button" onclick="useHelperTotal()" class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                        Use This Amount
                    </button>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                    <a href="{{ route('tenant.pos.index', ['tenant' => $tenant->slug]) }}"
                       class="text-gray-600 hover:text-gray-800 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to POS
                    </a>

                    <div class="flex space-x-3">
                        <button type="button"
                                onclick="clearForm()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-colors duration-200">
                            Clear
                        </button>
                        <button type="submit"
                                class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center space-x-2">
                            <i class="fas fa-lock"></i>
                            <span>Close Session</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const expectedBalance = {{ $activeSession->opening_balance + $activeSession->total_cash_sales }};

function calculateTotal() {
    const denominations = [1000, 500, 200, 100, 50, 20, 10, 5];
    let total = 0;

    denominations.forEach(denom => {
        const count = parseInt(document.getElementById(`notes-${denom}`).value) || 0;
        total += count * denom;
    });

    document.getElementById('helper-total').textContent = `₦${total.toFixed(2)}`;
}

function useHelperTotal() {
    const helperTotal = document.getElementById('helper-total').textContent.replace('₦', '');
    document.getElementById('closing_balance').value = parseFloat(helperTotal).toFixed(2);
    updateDifference();
}

function updateDifference() {
    const closingBalance = parseFloat(document.getElementById('closing_balance').value) || 0;
    const difference = closingBalance - expectedBalance;

    document.getElementById('actual-count').textContent = `₦${closingBalance.toFixed(2)}`;
    document.getElementById('difference-display').textContent = `₦${Math.abs(difference).toFixed(2)}`;

    // Show/hide difference indicators
    const indicator = document.getElementById('difference-indicator');
    const positive = document.getElementById('difference-positive');
    const negative = document.getElementById('difference-negative');
    const exact = document.getElementById('difference-exact');

    // Hide all first
    [positive, negative, exact].forEach(el => el.classList.add('hidden'));

    if (Math.abs(difference) < 0.01) {
        exact.classList.remove('hidden');
        document.getElementById('difference-display').className = 'text-green-600 font-bold';
    } else if (difference > 0) {
        positive.classList.remove('hidden');
        document.getElementById('difference-amount-positive').textContent = Math.abs(difference).toFixed(2);
        document.getElementById('difference-display').className = 'text-green-600 font-bold';
    } else {
        negative.classList.remove('hidden');
        document.getElementById('difference-amount-negative').textContent = Math.abs(difference).toFixed(2);
        document.getElementById('difference-display').className = 'text-red-600 font-bold';
    }

    indicator.classList.remove('hidden');
}

function clearForm() {
    document.getElementById('closing_balance').value = '';
    document.getElementById('closing_notes').value = '';

    // Clear cash counting helper
    const denominations = [1000, 500, 200, 100, 50, 20, 10, 5];
    denominations.forEach(denom => {
        document.getElementById(`notes-${denom}`).value = '';
    });

    document.getElementById('helper-total').textContent = '₦0.00';
    document.getElementById('difference-indicator').classList.add('hidden');
    document.getElementById('actual-count').textContent = '₦0.00';
    document.getElementById('difference-display').textContent = '₦0.00';
}

// Add event listener for closing balance input
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('closing_balance').addEventListener('input', updateDifference);
});
</script>
@endsection
