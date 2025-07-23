<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->voucherType->abbreviation }}-{{ $invoice->voucher_number }} - {{ $tenant->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            margin-top: 15px;
        }
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-info, .company-info {
            width: 48%;
        }
        .invoice-info h3, .company-info h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 16px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .accounting-entries {
            margin-top: 30px;
        }
        .accounting-entries h3 {
            margin-bottom: 15px;
            font-size: 16px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 20px;
        }
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Print Button (hidden when printing) -->
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Print Invoice
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
            Close
        </button>
    </div>

    <!-- Invoice Header -->
    <div class="invoice-header">
        <div class="company-name">{{ $tenant->name }}</div>
        @if($tenant->address)
            <div>{{ $tenant->address }}</div>
        @endif
        @if($tenant->phone)
            <div>Phone: {{ $tenant->phone }}</div>
        @endif
        @if($tenant->email)
            <div>Email: {{ $tenant->email }}</div>
        @endif
        <div class="invoice-title">SALES INVOICE</div>
    </div>

    <!-- Invoice Details -->
    <div class="invoice-details">
        <div class="invoice-info">
            <h3>Invoice Information</h3>
            <div class="info-row">
                <span class="info-label">Invoice No:</span>
                {{ $invoice->voucherType->abbreviation }}-{{ $invoice->voucher_number }}
            </div>
            <div class="info-row">
                <span class="info-label">Invoice Date:</span>
                {{ $invoice->voucher_date->format('M d, Y') }}
            </div>
            @if($invoice->reference_number)
            <div class="info-row">
                <span class="info-label">Reference:</span>
                {{ $invoice->reference_number }}
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Status:</span>
                {{ ucfirst($invoice->status) }}
            </div>
        </div>

        <div class="company-info">
            <h3>Bill To</h3>
            @if(isset($customer))
                <div class="info-row">
                    <span class="info-label">Customer:</span>
                    {{ $customer->name }}
                </div>
                @if($customer->address)
                <div class="info-row">
                    <span class="info-label">Address:</span>
                    {{ $customer->address }}
                </div>
                @endif
                @if($customer->phone)
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    {{ $customer->phone }}
                </div>
                @endif
                @if($customer->email)
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    {{ $customer->email }}
                </div>
                @endif
            @else
                <div class="info-row">Cash Sale / Walk-in Customer</div>
            @endif
        </div>
    </div>

    <!-- Invoice Items -->
    @if(count($inventoryItems) > 0)
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Product</th>
                    <th style="width: 25%;">Description</th>
                    <th style="width: 10%;" class="text-center">Qty</th>
                    <th style="width: 12%;" class="text-right">Rate</th>
                    <th style="width: 13%;" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventoryItems as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item['product_name'] }}</td>
                        <td>{{ $item['description'] }}</td>
                        <td class="text-center">{{ number_format($item['quantity'], 2) }}</td>
                        <td class="text-right">₦{{ number_format($item['rate'], 2) }}</td>
                        <td class="text-right">₦{{ number_format($item['amount'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-right">Total Amount:</td>
                    <td class="text-right">₦{{ number_format($inventoryItems->sum('amount'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <!-- Narration -->
    @if($invoice->narration)
        <div style="margin-bottom: 20px;">
            <strong>Notes:</strong><br>
            {{ $invoice->narration }}
        </div>
    @endif

    <!-- Accounting Entries -->
    <div class="accounting-entries">
        <h3>Accounting Entries</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Account</th>
                    <th style="width: 35%;">Particulars</th>
                    <th style="width: 12%;" class="text-right">Debit</th>
                    <th style="width: 13%;" class="text-right">Credit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->entries as $entry)
                    <tr>
                        <td>
                            {{ $entry->ledgerAccount->name }}<br>
                            <small style="color: #666;">{{ $entry->ledgerAccount->code }}</small>
                        </td>
                        <td>{{ $entry->particulars }}</td>
                        <td class="text-right">
                            @if($entry->debit_amount > 0)
                                ₦{{ number_format($entry->debit_amount, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            @if($entry->credit_amount > 0)
                                ₦{{ number_format($entry->credit_amount, 2) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" class="text-right">Totals:</td>
                    <td class="text-right">₦{{ number_format($invoice->entries->sum('debit_amount'), 2) }}</td>
                    <td class="text-right">₦{{ number_format($invoice->entries->sum('credit_amount'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Generated on {{ now()->format('M d, Y \a\t g:i A') }}</p>
        <p>{{ $tenant->name }} - Powered by Ballie Business Management System</p>
    </div>

    <script>
        // Auto-print when page loads (optional)
        // window.onload = function() { window.print(); }

        // Close window after printing
        window.onafterprint = function() {
            // Uncomment the line below if you want to auto-close after printing
            // window.close();
        }
    </script>
</body>
</html>