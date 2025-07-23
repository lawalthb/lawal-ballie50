<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherType;
use App\Models\VoucherEntry;
use App\Models\Product;
use App\Models\LedgerAccount;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    public function index(Request $request, Tenant $tenant)
    {
        $query = Voucher::where('tenant_id', $tenant->id)
            ->whereHas('voucherType', function($q) {
                $q->where('affects_inventory', true)
                  ->where('code', 'LIKE', '%SALES%');
            })
            ->with(['voucherType', 'createdBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('voucher_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('narration', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('voucher_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('voucher_date', '<=', $request->date_to);
        }

        $invoices = $query->latest('voucher_date')->paginate(15);

        return view('tenant.accounting.invoices.index', compact('invoices', 'tenant'));
    }

    public function create(Tenant $tenant)
    {
        // Get sales voucher types that affect inventory
        $voucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->where('code', 'LIKE', '%SALES%')
            ->orderBy('name')
            ->get();

        // Get products
        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('is_saleable', true)
            ->with(['primaryUnit'])
            ->orderBy('name')
            ->get();

        // Get customers (if you have a Customer model)
        $customers = collect(); // Replace with actual customer query when implemented

        // Get default sales voucher type
        $selectedType = $voucherTypes->where('code', 'SALES')->first() ?? $voucherTypes->first();

        return view('tenant.accounting.invoices.create', compact(
            'tenant',
            'voucherTypes',
            'products',
            'customers',
            'selectedType'
        ));
    }

    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'voucher_type_id' => 'required|exists:voucher_types,id',
            'voucher_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'narration' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
            'inventory_items' => 'required|array|min:1',
            'inventory_items.*.product_id' => 'required|exists:products,id',
            'inventory_items.*.quantity' => 'required|numeric|min:0.01',
            'inventory_items.*.rate' => 'required|numeric|min:0',
            'inventory_items.*.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Get voucher type
            $voucherType = VoucherType::findOrFail($request->voucher_type_id);

            // Calculate total amount
            $totalAmount = 0;
            $inventoryItems = [];

            foreach ($request->inventory_items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $amount = $item['quantity'] * $item['rate'];
                $totalAmount += $amount;

                $inventoryItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'description' => $item['description'] ?? $product->name,
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'amount' => $amount,
                ];
            }

            // Generate voucher number
            $lastVoucher = Voucher::where('tenant_id', $tenant->id)
                ->where('voucher_type_id', $voucherType->id)
                ->latest('id')
                ->first();

            $nextNumber = $lastVoucher ? $lastVoucher->voucher_number + 1 : 1;

            // Create voucher
            $voucher = Voucher::create([
                'tenant_id' => $tenant->id,
                'voucher_type_id' => $voucherType->id,
                'voucher_number' => $nextNumber,
                'voucher_date' => $request->voucher_date,
                'reference_number' => $request->reference_number,
                'narration' => $request->narration,
                'total_amount' => $totalAmount,
                'status' => $request->action === 'save_and_post' ? 'posted' : 'draft',
                'created_by' => auth()->id(),
                'posted_at' => $request->action === 'save_and_post' ? now() : null,
                'posted_by' => $request->action === 'save_and_post' ? auth()->id() : null,
            ]);

            // Store inventory items in voucher meta or separate table
            $voucher->update([
                'meta_data' => json_encode(['inventory_items' => $inventoryItems])
            ]);

            // Create accounting entries
            $this->createAccountingEntries($voucher, $inventoryItems, $tenant);

            // Update product stock if posted
            if ($request->action === 'save_and_post') {
                $this->updateProductStock($inventoryItems, 'decrease');
            }

            DB::commit();

            $message = $request->action === 'save_and_post'
                ? 'Invoice created and posted successfully!'
                : 'Invoice saved as draft successfully!';

            return redirect()
                ->route('tenant.accounting.invoices.show', ['tenant' => $tenant->slug, 'invoice' => $voucher->id])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating invoice: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while creating the invoice. Please try again.')
                ->withInput();
        }
    }

    public function show(Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        $invoice->load(['voucherType', 'entries.ledgerAccount', 'createdBy', 'postedBy']);

        // Get inventory items from meta data
        $inventoryItems = collect();
        if ($invoice->meta_data) {
            $metaData = json_decode($invoice->meta_data, true);
            if (isset($metaData['inventory_items'])) {
                $inventoryItems = collect($metaData['inventory_items']);
            }
        }

        return view('tenant.accounting.invoices.show', compact('tenant', 'invoice', 'inventoryItems'));
    }

    public function edit(Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant and is editable
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        if ($invoice->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft invoices can be edited.');
        }

        // Get voucher types
        $voucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->where('code', 'LIKE', '%SALES%')
            ->orderBy('name')
            ->get();

        // Get products
        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('is_saleable', true)
            ->with(['primaryUnit'])
            ->orderBy('name')
            ->get();

        // Get customers
        $customers = collect();

        // Get inventory items
        $inventoryItems = collect();
        if ($invoice->meta_data) {
            $metaData = json_decode($invoice->meta_data, true);
            if (isset($metaData['inventory_items'])) {
                $inventoryItems = collect($metaData['inventory_items']);
            }
        }

        return view('tenant.accounting.invoices.edit', compact(
            'tenant',
            'invoice',
            'voucherTypes',
            'products',
            'customers',
            'inventoryItems'
        ));
    }

    public function update(Request $request, Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant and is editable
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        if ($invoice->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft invoices can be edited.');
        }

        $validator = Validator::make($request->all(), [
            'voucher_type_id' => 'required|exists:voucher_types,id',
            'voucher_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'narration' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
            'inventory_items' => 'required|array|min:1',
            'inventory_items.*.product_id' => 'required|exists:products,id',
            'inventory_items.*.quantity' => 'required|numeric|min:0.01',
            'inventory_items.*.rate' => 'required|numeric|min:0',
            'inventory_items.*.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Calculate total amount
            $totalAmount = 0;
            $inventoryItems = [];

            foreach ($request->inventory_items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $amount = $item['quantity'] * $item['rate'];
                $totalAmount += $amount;

                $inventoryItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'description' => $item['description'] ?? $product->name,
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'amount' => $amount,
                ];
            }

            // Update voucher
            $invoice->update([
                'voucher_type_id' => $request->voucher_type_id,
                'voucher_date' => $request->voucher_date,
                'reference_number' => $request->reference_number,
                'narration' => $request->narration,
                'total_amount' => $totalAmount,
                'status' => $request->action === 'save_and_post' ? 'posted' : 'draft',
                'posted_at' => $request->action === 'save_and_post' ? now() : null,
                'posted_by' => $request->action === 'save_and_post' ? auth()->id() : null,
                'meta_data' => json_encode(['inventory_items' => $inventoryItems])
            ]);

            // Delete old entries and create new ones
            $invoice->entries()->delete();
            $this->createAccountingEntries($invoice, $inventoryItems, $tenant);

            // Update product stock if posted
            if ($request->action === 'save_and_post') {
                $this->updateProductStock($inventoryItems, 'decrease');
            }

            DB::commit();

            $message = $request->action === 'save_and_post'
                ? 'Invoice updated and posted successfully!'
                : 'Invoice updated successfully!';

            return redirect()
                ->route('tenant.accounting.invoices.show', ['tenant' => $tenant->slug, 'invoice' => $invoice->id])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating invoice: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while updating the invoice. Please try again.')
                ->withInput();
        }
    }

    public function destroy(Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        if ($invoice->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft invoices can be deleted.');
        }

        try {
            DB::beginTransaction();

            // Delete entries
            $invoice->entries()->delete();

            // Delete invoice
            $invoice->delete();

            DB::commit();

            return redirect()
                ->route('tenant.accounting.invoices.index', ['tenant' => $tenant->slug])
                ->with('success', 'Invoice deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting invoice: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while deleting the invoice. Please try again.');
        }
    }

    public function post(Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        if ($invoice->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft invoices can be posted.');
        }

        try {
            DB::beginTransaction();

            // Post the invoice
            $invoice->update([
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => auth()->id(),
            ]);

            // Update product stock
            if ($invoice->meta_data) {
                $metaData = json_decode($invoice->meta_data, true);
                if (isset($metaData['inventory_items'])) {
                    $this->updateProductStock($metaData['inventory_items'], 'decrease');
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Invoice posted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error posting invoice: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while posting the invoice. Please try again.');
        }
    }

    public function unpost(Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        if ($invoice->status !== 'posted') {
            return redirect()->back()
                ->with('error', 'Only posted invoices can be unposted.');
        }

        try {
            DB::beginTransaction();

            // Unpost the invoice
            $invoice->update([
                'status' => 'draft',
                'posted_at' => null,
                'posted_by' => null,
            ]);

            // Reverse product stock changes
            if ($invoice->meta_data) {
                $metaData = json_decode($invoice->meta_data, true);
                if (isset($metaData['inventory_items'])) {
                    $this->updateProductStock($metaData['inventory_items'], 'increase');
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Invoice unposted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error unposting invoice: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while unposting the invoice. Please try again.');
        }
    }

    public function print(Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        $invoice->load(['voucherType', 'entries.ledgerAccount', 'createdBy', 'postedBy']);

        // Get inventory items from meta data
        $inventoryItems = collect();
        if ($invoice->meta_data) {
            $metaData = json_decode($invoice->meta_data, true);
            if (isset($metaData['inventory_items'])) {
                $inventoryItems = collect($metaData['inventory_items']);
            }
        }

        // Get customer info if available
        $customer = null; // Replace with actual customer lookup when implemented

        return view('tenant.accounting.invoices.print', compact('tenant', 'invoice', 'inventoryItems', 'customer'));
    }

    private function createAccountingEntries(Voucher $voucher, array $inventoryItems, Tenant $tenant)
    {
        // Get default accounts
        $salesAccount = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('name', 'LIKE', '%Sales%')
            ->first();

        $cashAccount = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('name', 'LIKE', '%Cash%')
            ->first();

        if (!$salesAccount || !$cashAccount) {
            throw new \Exception('Required ledger accounts (Sales, Cash) not found. Please create them first.');
        }

        $totalAmount = collect($inventoryItems)->sum('amount');

        // Debit: Cash/Accounts Receivable
        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $cashAccount->id,
            'debit_amount' => $totalAmount,
            'credit_amount' => 0,
            'particulars' => 'Sales invoice - ' . $voucher->getDisplayNumber(),
        ]);

        // Credit: Sales
        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $salesAccount->id,
            'debit_amount' => 0,
            'credit_amount' => $totalAmount,
            'particulars' => 'Sales invoice - ' . $voucher->getDisplayNumber(),
        ]);

        // If you want to track Cost of Goods Sold (COGS), add those entries here
        // This would require tracking purchase costs of products
    }

    private function updateProductStock(array $inventoryItems, string $operation)
    {
        foreach ($inventoryItems as $item) {
            $product = Product::find($item['product_id']);
            if ($product && $product->maintain_stock) {
                $quantity = $item['quantity'];

                if ($operation === 'decrease') {
                    $product->decrement('current_stock', $quantity);
                } else {
                    $product->increment('current_stock', $quantity);
                }

                // Update stock value
                $product->current_stock_value = $product->current_stock * $product->purchase_rate;
                $product->save();
            }
        }
    }
}
