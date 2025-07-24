<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->voucherType->prefix ?? '' }}{{ $invoice->voucher_number }} - {{ $tenant->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.4;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2563eb;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin-top: 15px;
            color: #374151;
        }
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-info, .customer-info {
            width: 48%;
        }
        .invoice-info h3, .customer-info h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
            color: #374151;
        }
        .info-row {
            margin-bottom: 8px;
            display: flex;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
            color: #6b7280;
        }
        .info-value {
            color: #374151;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #e5e7eb;
            padding: 12px 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f9fafb;
            font-weight: bold;
            color: #374151;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .summary-section {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }
        .summary-table {
            width: 300px;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
        }
        .summary-table .label {
            background-color: #f9fafb;
            font-weight: bold;
            text-align: right;
            width: 60%;
        }
        .summary-table .amount {
            text-align: right;
            width: 40%;
        }
        .summary-table .total {
            background-color: #dbeafe;
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .notes {
            margin-top: 30px;
            margin-bottom: 20px;
        }
        .notes h4 {
            margin-bottom: 10px;
            color: #374151;
        }
        .notes-content {
            background-color: #f9fafb;
            padding: 15px;
            border-left: 4px solid #2563eb;
        }
    </style>
</head>
<body>
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
                <span class="info-value">{{ $invoice->voucherType->prefix ?? '' }}{{ $invoice->voucher_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Invoice Date:</span>
                <span class="info-value">{{ $invoice->voucher_date->format('M d, Y') }}</span>
            </div>
            @if($invoice->reference_number)
            <div class="info-row">
                <span class="info-label">Reference:</span>
                <span class="info-value">{{ $invoice->reference_number }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">{{ ucfirst($invoice->status) }}</span>
            </div>
        </div>

        <div class="customer-info">
            <h3>Bill To</h3>
            @if($customer)
                <div class="info-row">
                    <span class="info-label">Customer:</span>
                    <span class="info-value">{{ $customer->name }}</span>
                </div>
                @if($customer->address)
                <div class="info-row">
                    <span class="info-label">Address:</span>
                    <span class="info-value">{{ $customer->address }}</span>
                </div>
                @endif
                @if($customer->phone)
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $customer->phone }}</span>
                </div>
                @endif
                @if($customer->email)
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $customer->email }}</span>
                </div>
                @endif
            @else
                <div class="info-row">
                    <span class="info-value">Cash Sale / Walk-in Customer</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Invoice Items -->
    @if($invoice->items && $invoice->items->count() > 0)
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
                @foreach($invoice->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-right">₦{{ number_format($item->rate, 2) }}</td>
                        <td class="text-right">₦{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="amount">₦{{ number_format($invoice->items->sum('amount'), 2) }}</td>
                </tr>
                <tr class="total">
                    <td class="label">Total Amount:</td>
                    <td class="amount">₦{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>
    @endif

    <!-- Notes -->
    @if($invoice->narration)
        <div class="notes">
            <h4>Notes:</h4>
            <div class="notes-content">
                {{ $invoice->narration }}
            </div>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Generated on {{ now()->format('M d, Y \a\t g:i A') }}</p>
        <p><strong>{{ $tenant->name }}</strong> - Powered by Ballie Business Management System</p>
        <p>Thank you for your business!</p>
    </div>
</body>
</html>
