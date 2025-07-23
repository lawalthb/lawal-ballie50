@extends('layouts.tenant')

@section('title', 'Create Sales Invoice - ' . $tenant->name)

@section('content')
<div class="space-y-6" x-data="invoiceForm()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Sales Invoice</h1>
            <p class="mt-1 text-sm text-gray-500">
                Create a new sales invoice with inventory management
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('tenant.accounting.invoices.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Invoices
            </a>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('tenant.accounting.invoices.store', ['tenant' => $tenant->slug]) }}" class="space-y-6">
        @csrf

        <!-- Invoice Header -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Invoice Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Voucher Type -->
                    <div>
                        <label for="voucher_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Invoice Type <span class="text-red-500">*</span>
                        </label>
                        <select name="voucher_type_id"
                                id="voucher_type_id"
                                x-model="voucherTypeId"
                                @change="updateVoucherType()"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-lg @error('voucher_type_id') border-red-300 @enderror"
                                required>
                            <option value="">Select Invoice Type</option>
                            @foreach($voucherTypes as $type)
                                <option value="{{ $type->id }}"
                                        {{ (old('voucher_type_id', $selectedType?->id ?? '') == $type->id) ? 'selected' : '' }}>
                                    {{ $type->name }} ({{ $type->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('voucher_type_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Invoice Date -->
                    <div>
                        <label for="voucher_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Invoice Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="voucher_date"
                               id="voucher_date"
                               value="{{ old('voucher_date', date('Y-m-d')) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('voucher_date') border-red-300 @enderror"
                               required>
                        @error('voucher_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reference Number -->
                    <div>
                        <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Reference Number
                        </label>
                        <input type="text"
                               name="reference_number"
                               id="reference_number"
                               value="{{ old('reference_number') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('reference_number') border-red-300 @enderror"
                               placeholder="Optional reference">
                        @error('reference_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Invoice Number Preview -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Invoice Number
                        </label>
                        <div class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500">
                            <span x-text="invoiceNumberPreview"></span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Auto-generated on save</p>
                    </div>
                </div>

                <!-- Customer Information (if you have customers) -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Customer -->
                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Customer
                        </label>
                        <select name="customer_id"
                                id="customer_id"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-lg">
                            <option value="">Select Customer (Optional)</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Narration -->
                    <div>
                        <label for="narration" class="block text-sm font-medium text-gray-700 mb-2">
                            Description/Notes
                        </label>
                        <textarea name="narration"
                                  id="narration"
                                  rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('narration') border-red-300 @enderror"
                                  placeholder="Invoice description or notes">{{ old('narration') }}</textarea>
                        @error('narration')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Items Section -->
        @include('tenant.accounting.invoices.partials.invoice-items')

        <!-- Submit Buttons -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">Total Amount: </span>
                        <span class="text-lg font-bold text-gray-900">₦<span x-text="formatNumber(totalAmount)">0.00</span></span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="button"
                                onclick="window.history.back()"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button type="submit"
                                name="action"
                                value="save_draft"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Save as Draft
                        </button>
                        <button type="submit"
                                name="action"
                                value="save_and_post"
                                class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Save & Post Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function invoiceForm() {
    return {
        voucherTypeId: '{{ old('voucher_type_id', $selectedType?->id ?? '') }}',
        invoiceNumberPreview: 'Auto-generated',
        voucherTypes: @json($voucherTypes->keyBy('id')),
        totalAmount: 0,

        init() {
            this.updateVoucherType();

            // Listen for inventory total updates
            document.addEventListener('inventory-total-updated', (e) => {
                this.totalAmount = e.detail.total;
            });

            console.log('✅ Invoice form initialized');
        },

        updateVoucherType() {
            if (this.voucherTypeId && this.voucherTypes[this.voucherTypeId]) {
                const voucherType = this.voucherTypes[this.voucherTypeId];
                this.invoiceNumberPreview = voucherType.prefix + 'XXXX';

                // Notify inventory component about voucher type change
                document.dispatchEvent(new CustomEvent('voucher-type-changed', {
                    detail: { voucherType: voucherType }
                }));
            } else {
                this.invoiceNumberPreview = 'Auto-generated';
            }
        },

        formatNumber(num) {
            if (!num || isNaN(num)) return '0.00';
            return parseFloat(num).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }
}
</script>
@endpush
@endsection
