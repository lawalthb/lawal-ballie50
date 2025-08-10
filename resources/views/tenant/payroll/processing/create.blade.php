@extends('tenant.layouts.app')

@section('title', 'Create Payroll Period')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Create Payroll Period</h1>
                    <p class="text-cyan-100 text-lg">Set up a new payroll processing period</p>
                </div>
                <a href="{{ route('tenant.payroll.processing.index', $tenant) }}"
                   class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Processing
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="{{ route('tenant.payroll.processing.store', $tenant) }}" method="POST" class="space-y-8">
            @csrf

            <!-- Period Information -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                    Period Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Period Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $startDate->format('F Y') . ' Payroll') }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">A descriptive name for this payroll period</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date', $startDate->format('Y-m-d')) }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('start_date') border-red-500 @enderror">
                        @error('start_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" value="{{ old('end_date', $endDate->format('Y-m-d')) }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('end_date') border-red-500 @enderror">
                        @error('end_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pay Date <span class="text-red-500">*</span></label>
                        <input type="date" name="pay_date" value="{{ old('pay_date', $payDate->format('Y-m-d')) }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pay_date') border-red-500 @enderror">
                        @error('pay_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Date when salaries will be paid to employees</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payroll Type <span class="text-red-500">*</span></label>
                        <select name="type" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror">
                            <option value="monthly" {{ old('type', 'monthly') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="weekly" {{ old('type') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="contract" {{ old('type') === 'contract' ? 'selected' : '' }}>Contract</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Period Summary -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-green-500"></i>
                    Period Summary
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-week text-blue-500 text-2xl mr-3"></i>
                            <div>
                                <p class="text-sm font-medium text-blue-600">Duration</p>
                                <p class="text-lg font-bold text-blue-900" id="duration">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-users text-green-500 text-2xl mr-3"></i>
                            <div>
                                <p class="text-sm font-medium text-green-600">Active Employees</p>
                                <p class="text-lg font-bold text-green-900">{{ $activeEmployees ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-money-bill-wave text-purple-500 text-2xl mr-3"></i>
                            <div>
                                <p class="text-sm font-medium text-purple-600">Estimated Cost</p>
                                <p class="text-lg font-bold text-purple-900" id="estimatedCost">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-amber-500 text-xl mr-3 mt-1"></i>
                    <div>
                        <h4 class="text-lg font-medium text-amber-800 mb-2">Important Notes</h4>
                        <ul class="text-amber-700 space-y-1 text-sm">
                            <li>• Ensure all employee information is up to date before creating the payroll period</li>
                            <li>• The pay date should be after the end date of the period</li>
                            <li>• Once created, you can generate payroll calculations for all active employees</li>
                            <li>• Salary components and tax rates will be applied based on current settings</li>
                            <li>• You can review and approve the payroll before finalizing payments</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('tenant.payroll.processing.index', $tenant) }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-medium transition-colors duration-300">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors duration-300">
                    <i class="fas fa-save mr-2"></i>Create Payroll Period
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    const typeInput = document.querySelector('select[name="type"]');
    const durationElement = document.getElementById('duration');
    const estimatedCostElement = document.getElementById('estimatedCost');

    function updateSummary() {
        const startDate = startDateInput.value ? new Date(startDateInput.value) : null;
        const endDate = endDateInput.value ? new Date(endDateInput.value) : null;

        if (startDate && endDate && endDate >= startDate) {
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            durationElement.textContent = `${diffDays} days`;

            // Rough estimation based on average salary (you can make this more sophisticated)
            const activeEmployees = {{ $activeEmployees ?? 0 }};
            const avgMonthlySalary = 150000; // You can get this from your database
            const estimatedTotal = activeEmployees * avgMonthlySalary;

            estimatedCostElement.textContent = `₦${estimatedTotal.toLocaleString()}`;
        } else {
            durationElement.textContent = '-';
            estimatedCostElement.textContent = '-';
        }
    }

    function updatePayDate() {
        const endDate = endDateInput.value ? new Date(endDateInput.value) : null;
        const payDateInput = document.querySelector('input[name="pay_date"]');

        if (endDate) {
            const payDate = new Date(endDate);
            payDate.setDate(payDate.getDate() + 2); // Default: 2 days after end date
            payDateInput.value = payDate.toISOString().split('T')[0];
        }
    }

    startDateInput.addEventListener('change', updateSummary);
    endDateInput.addEventListener('change', function() {
        updateSummary();
        updatePayDate();
    });
    typeInput.addEventListener('change', updateSummary);

    // Initial calculation
    updateSummary();
});
</script>
@endsection
