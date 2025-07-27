@extends('layouts.tenant')

@section('title', 'Cash Flow Statement - ' . $tenant->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Cash Flow Statement</h1>
            <p class="mt-1 text-sm text-gray-500">
                Cash flow analysis from {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <form method="GET" class="flex items-center space-x-3">
                <div>
                    <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date"
                           name="from_date"
                           id="from_date"
                           value="{{ $fromDate }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date"
                           name="to_date"
                           id="to_date"
                           value="{{ $toDate }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="flex items-end">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Update
                    </button>
                </div>
            </form>
            <button onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
            <button onclick="exportToCSV()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export
            </button>
            <a href="{{ route('tenant.reports.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Reports
            </a>
        </div>
    </div>

    <!-- Cash Flow Statement -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Cash Flow Statement</h3>
            <p class="mt-1 text-sm text-gray-500">
                Statement of cash flows for the period from {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}
            </p>
        </div>

        <div class="p-6 space-y-8">
            <!-- Operating Activities -->
            <div>
                <h4 class="text-md font-semibold text-gray-900 mb-4">Cash Flow from Operating Activities</h4>
                @if(count($operatingActivities) > 0)
                    <div class="space-y-2">
                        @foreach($operatingActivities as $activity)
                            <div class="flex justify-between items-center py-2">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-3
                                        @if($activity['type'] == 'income') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($activity['type']) }}
                                    </span>
                                    <span class="text-sm text-gray-900">{{ $activity['description'] }}</span>
                                </div>
                                <span class="text-sm font-mono {{ $activity['amount'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($activity['amount'], 2) }}
                                </span>
                            </div>
                        @endforeach
                        <hr class="my-3">
                        <div class="flex justify-between items-center py-2 font-semibold">
                            <span class="text-gray-900">Net Cash Flow from Operating Activities</span>
                            <span class="text-lg font-mono {{ $operatingTotal >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($operatingTotal, 2) }}
                            </span>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No operating activities for this period</p>
                @endif
            </div>

            <!-- Investing Activities -->
            <div>
                <h4 class="text-md font-semibold text-gray-900 mb-4">Cash Flow from Investing Activities</h4>
                @if(count($investingActivities) > 0)
                    <div class="space-y-2">
                        @foreach($investingActivities as $activity)
                            <div class="flex justify-between items-center py-2">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-3 bg-blue-100 text-blue-800">
                                        Investing
                                    </span>
                                    <span class="text-sm text-gray-900">{{ $activity['description'] }}</span>
                                </div>
                                <span class="text-sm font-mono {{ $activity['amount'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($activity['amount'], 2) }}
                                </span>
                            </div>
                        @endforeach
                        <hr class="my-3">
                        <div class="flex justify-between items-center py-2 font-semibold">
                            <span class="text-gray-900">Net Cash Flow from Investing Activities</span>
                            <span class="text-lg font-mono {{ $investingTotal >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($investingTotal, 2) }}
                            </span>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No investing activities for this period</p>
                @endif
            </div>

            <!-- Financing Activities -->
            <div>
                <h4 class="text-md font-semibold text-gray-900 mb-4">Cash Flow from Financing Activities</h4>
                @if(count($financingActivities) > 0)
                    <div class="space-y-2">
                        @foreach($financingActivities as $activity)
                            <div class="flex justify-between items-center py-2">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-3
                                        @if($activity['type'] == 'equity') bg-purple-100 text-purple-800
                                        @else bg-orange-100 text-orange-800
                                        @endif">
                                        {{ ucfirst($activity['type']) }}
                                    </span>
                                    <span class="text-sm text-gray-900">{{ $activity['description'] }}</span>
                                </div>
                                <span class="text-sm font-mono {{ $activity['amount'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($activity['amount'], 2) }}
                                </span>
                            </div>
                        @endforeach
                        <hr class="my-3">
                        <div class="flex justify-between items-center py-2 font-semibold">
                            <span class="text-gray-900">Net Cash Flow from Financing Activities</span>
                            <span class="text-lg font-mono {{ $financingTotal >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($financingTotal, 2) }}
                            </span>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No financing activities for this period</p>
                @endif
            </div>

            <!-- Net Cash Flow Summary -->
            <div class="border-t-2 border-gray-300 pt-6">
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Net Cash Flow from Operating Activities</span>
                        <span class="text-sm font-mono {{ $operatingTotal >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($operatingTotal, 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Net Cash Flow from Investing Activities</span>
                        <span class="text-sm font-mono {{ $investingTotal >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($investingTotal, 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Net Cash Flow from Financing Activities</span>
                        <span class="text-sm font-mono {{ $financingTotal >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($financingTotal, 2) }}
                        </span>
                    </div>
                    <hr class="border-gray-300">
                    <div class="flex justify-between items-center text-lg font-bold">
                        <span class="text-gray-900">Net Increase/(Decrease) in Cash</span>
                        <span class="font-mono {{ $netCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($netCashFlow, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Cash Position -->
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-md font-semibold text-gray-900 mb-4">Cash Position</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-blue-900">Opening Cash</div>
                        <div class="text-lg font-bold text-blue-600 font-mono">{{ number_format($openingCash, 2) }}</div>
                        <div class="text-xs text-blue-700">As of {{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }}</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-green-900">Net Cash Flow</div>
                        <div class="text-lg font-bold {{ $netCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }} font-mono">
                            {{ number_format($netCashFlow, 2) }}
                        </div>
                        <div class="text-xs text-green-700">For the period</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-purple-900">Closing Cash</div>
                        <div class="text-lg font-bold text-purple-600 font-mono">{{ number_format($closingCash, 2) }}</div>
                        <div class="text-xs text-purple-700">As of {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Cash Accounts Detail -->
            @if(count($cashAccounts) > 0)
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Cash Accounts Detail</h4>
                    <div class="space-y-2">
                        @foreach($cashAccounts as $account)
                            <div class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded">
                                <span class="text-sm text-gray-900">{{ $account->name }} ({{ $account->code }})</span>
                                <span class="text-sm font-mono text-gray-700">
                                    {{ number_format($account->current_balance ?? 0, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }

        .print-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .bg-gray-50 {
            background-color: #f9f9f9 !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
function exportToCSV() {
    let csvContent = "data:text/csv;charset=utf-8,";

    // Add header
    csvContent += "Cash Flow Statement - {{ $tenant->name }}\n";
    csvContent += "Period: {{ $fromDate }} to {{ $toDate }}\n\n";

    // Operating Activities
    csvContent += "OPERATING ACTIVITIES\n";
    csvContent += "Description,Amount\n";
    @foreach($operatingActivities as $activity)
        csvContent += "{{ addslashes($activity['description']) }},{{ number_format($activity['amount'], 2) }}\n";
    @endforeach
    csvContent += "Net Operating Cash Flow,{{ number_format($operatingTotal, 2) }}\n\n";

    // Investing Activities
    csvContent += "INVESTING ACTIVITIES\n";
    csvContent += "Description,Amount\n";
    @foreach($investingActivities as $activity)
        csvContent += "{{ addslashes($activity['description']) }},{{ number_format($activity['amount'], 2) }}\n";
    @endforeach
    csvContent += "Net Investing Cash Flow,{{ number_format($investingTotal, 2) }}\n\n";

    // Financing Activities
    csvContent += "FINANCING ACTIVITIES\n";
    csvContent += "Description,Amount\n";
    @foreach($financingActivities as $activity)
        csvContent += "{{ addslashes($activity['description']) }},{{ number_format($activity['amount'], 2) }}\n";
    @endforeach
    csvContent += "Net Financing Cash Flow,{{ number_format($financingTotal, 2) }}\n\n";

    // Summary
    csvContent += "SUMMARY\n";
    csvContent += "Opening Cash,{{ number_format($openingCash, 2) }}\n";
    csvContent += "Net Cash Flow,{{ number_format($netCashFlow, 2) }}\n";
    csvContent += "Closing Cash,{{ number_format($closingCash, 2) }}\n";

    // Create download
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "cash_flow_{{ $fromDate }}_to_{{ $toDate }}.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Date validation
document.getElementById('from_date').addEventListener('change', function() {
    const fromDate = new Date(this.value);
    const toDate = new Date(document.getElementById('to_date').value);
    const today = new Date();

    if (fromDate > today) {
        alert('From date cannot be in the future');
        this.value = today.toISOString().split('T')[0];
    }

    if (toDate && fromDate > toDate) {
        alert('From date cannot be later than To date');
        this.value = '';
    }
});

document.getElementById('to_date').addEventListener('change', function() {
    const toDate = new Date(this.value);
    const fromDate = new Date(document.getElementById('from_date').value);
    const today = new Date();

    if (toDate > today) {
        alert('To date cannot be in the future');
        this.value = today.toISOString().split('T')[0];
    }

    if (fromDate && toDate < fromDate) {
        alert('To date cannot be earlier than From date');
        this.value = '';
    }
});
</script>
@endpush
@endsection
