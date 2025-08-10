@extends('layouts.tenant')

@section('title', 'Payroll Processing')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Payroll Processing</h1>
                    <p class="text-cyan-100 text-lg">Manage payroll periods and processing</p>
                </div>
                <a href="{{ route('tenant.payroll.processing.create', $tenant) }}"
                   class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                    <i class="fas fa-plus mr-2"></i>Create Payroll Period
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-lg mb-8">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-8">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Payroll Periods List -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Payroll Periods</h2>
                <p class="text-gray-600 mt-1">Manage and process payroll for different periods</p>
            </div>

            @if($payrollPeriods->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($payrollPeriods as $period)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $period->name }}</div>
                                        <div class="text-sm text-gray-500">Created {{ $period->created_at->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $period->start_date->format('M d') }} - {{ $period->end_date->format('M d, Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Pay Date: {{ $period->pay_date->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                            {{ $period->type === 'monthly' ? 'bg-blue-100 text-blue-800' :
                                               ($period->type === 'weekly' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ $period->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $period->status === 'draft' ? 'bg-gray-100 text-gray-800' :
                                               ($period->status === 'processing' ? 'bg-yellow-100 text-yellow-800' :
                                               ($period->status === 'completed' ? 'bg-green-100 text-green-800' :
                                               ($period->status === 'approved' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800'))) }}">
                                            <span class="w-1.5 h-1.5 rounded-full mr-1
                                                {{ $period->status === 'draft' ? 'bg-gray-400' :
                                                   ($period->status === 'processing' ? 'bg-yellow-400' :
                                                   ($period->status === 'completed' ? 'bg-green-400' :
                                                   ($period->status === 'approved' ? 'bg-blue-400' : 'bg-red-400'))) }}"></span>
                                            {{ ucfirst($period->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $period->payrollRuns ? $period->payrollRuns->count() : 0 }} employees
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            ₦{{ number_format($period->total_gross_salary ?? 0, 2) }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Net: ₦{{ number_format($period->total_net_salary ?? 0, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('tenant.payroll.processing.show', [$tenant, $period]) }}"
                                               class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($period->status === 'draft')
                                                <button onclick="generatePayroll({{ $period->id }})"
                                                        class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                                        title="Generate Payroll">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @endif

                                            @if($period->status === 'completed')
                                                <button onclick="approvePayroll({{ $period->id }})"
                                                        class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                                        title="Approve Payroll">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif

                                            @if($period->status === 'approved')
                                                <a href="{{ route('tenant.payroll.processing.export-bank-file', [$tenant, $period]) }}"
                                                   class="text-purple-600 hover:text-purple-900 transition-colors duration-200"
                                                   title="Export Bank File">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif

                                            <button onclick="deletePeriod({{ $period->id }})"
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                    title="Delete Period">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($payrollPeriods->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $payrollPeriods->links() }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-calendar-alt text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">No payroll periods found</h3>
                    <p class="text-gray-500 mb-6">Get started by creating your first payroll period.</p>
                    <a href="{{ route('tenant.payroll.processing.create', $tenant) }}"
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-300">
                        <i class="fas fa-plus mr-2"></i>Create Payroll Period
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function generatePayroll(periodId) {
    if (confirm('Are you sure you want to generate payroll for this period? This will calculate salaries for all active employees.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/tenant/{{ $tenant->id }}/payroll/processing/${periodId}/generate`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function approvePayroll(periodId) {
    if (confirm('Are you sure you want to approve this payroll? This will create accounting entries and finalize the payroll.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/tenant/{{ $tenant->id }}/payroll/processing/${periodId}/approve`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function deletePeriod(periodId) {
    if (confirm('Are you sure you want to delete this payroll period? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/tenant/{{ $tenant->id }}/payroll/processing/${periodId}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
