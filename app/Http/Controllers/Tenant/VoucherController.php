<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherType;
use App\Models\VoucherEntry;
use App\Models\LedgerAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::where('tenant_id', tenant()->id)
            ->with(['voucherType', 'createdBy'])
            ->orderBy('voucher_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('tenant.vouchers.index', compact('vouchers'));
    }

    public function create($type = null)
    {
        $voucherTypes = VoucherType::where('tenant_id', tenant()->id)
            ->active()
            ->get();

        $selectedType = null;
        if ($type) {
            $selectedType = VoucherType::where('tenant_id', tenant()->id)
                ->where('code', strtoupper($type))
                ->first();
        }

        $ledgerAccounts = LedgerAccount::where('tenant_id', tenant()->id)
            ->with('accountGroup')
            ->active()
            ->orderBy('name')
            ->get();

        return view('tenant.vouchers.create', compact('voucherTypes', 'selectedType', 'ledgerAccounts'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'voucher_type_id' => 'required|exists:voucher_types,id',
            'voucher_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'narration' => 'nullable|string',
            'entries' => 'required|array|min:2',
            'entries.*.ledger_account_id' => 'required|exists:ledger_accounts,id',
            'entries.*.debit_amount' => 'nullable|numeric|min:0',
            'entries.*.credit_amount' => 'nullable|numeric|min:0',
            'entries.*.particulars' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate that debits equal credits
        $totalDebits = collect($request->entries)->sum('debit_amount');
        $totalCredits = collect($request->entries)->sum('credit_amount');

        if (abs($totalDebits - $totalCredits) > 0.01) {
            return redirect()->back()
                ->withErrors(['entries' => 'Total debits must equal total credits'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $voucherType = VoucherType::findOrFail($request->voucher_type_id);

            $voucher = new Voucher([
                'tenant_id' => tenant()->id,
                'voucher_type_id' => $request->voucher_type_id,
                'voucher_number' => $voucherType->getNextVoucherNumber(),
                'voucher_date' => $request->voucher_date,
                'reference_number' => $request->reference_number,
                'narration' => $request->narration,
                'total_amount' => $totalDebits, // or $totalCredits, they should be equal
                'created_by' => auth()->id(),
            ]);
            $voucher->save();

            // Create voucher entries
            foreach ($request->entries as $entry) {
                if (($entry['debit_amount'] ?? 0) > 0 || ($entry['credit_amount'] ?? 0) > 0) {
                    VoucherEntry::create([
                        'voucher_id' => $voucher->id,
                        'ledger_account_id' => $entry['ledger_account_id'],
                        'debit_amount' => $entry['debit_amount'] ?? 0,
                        'credit_amount' => $entry['credit_amount'] ?? 0,
                        'particulars' => $entry['particulars'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('tenant.vouchers.show', ['tenant' => tenant()->slug, 'voucher' => $voucher->id])
                ->with('success', 'Voucher created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create voucher: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        $voucher = Voucher::where('tenant_id', tenant()->id)
            ->with(['voucherType', 'entries.ledgerAccount', 'createdBy', 'postedBy'])
            ->findOrFail($id);

        return view('tenant.vouchers.show', compact('voucher'));
    }

    public function edit($id)
    {
        $voucher = Voucher::where('tenant_id', tenant()->id)
            ->with(['entries'])
            ->findOrFail($id);

        if ($voucher->status === 'posted') {
            return redirect()->route('tenant.vouchers.show', ['tenant' => tenant()->slug, 'voucher' => $id])
                ->with('error', 'Posted vouchers cannot be edited.');
        }

        $voucherTypes = VoucherType::where('tenant_id', tenant()->id)
            ->active()
            ->get();

        $ledgerAccounts = LedgerAccount::where('tenant_id', tenant()->id)
            ->with('accountGroup')
            ->active()
            ->orderBy('name')
            ->get();

        return view('tenant.vouchers.edit', compact('voucher', 'voucherTypes', 'ledgerAccounts'));
    }

    public function update(Request $request, $id)
    {
        $voucher = Voucher::where('tenant_id', tenant()->id)->findOrFail($id);

        if ($voucher->status === 'posted') {
            return redirect()->back()
                ->with('error', 'Posted vouchers cannot be updated.');
        }

        $validator = Validator::make($request->all(), [
            'voucher_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'narration' => 'nullable|string',
            'entries' => 'required|array|min:2',
            'entries.*.ledger_account_id' => 'required|exists:ledger_accounts,id',
            'entries.*.debit_amount' => 'nullable|numeric|min:0',
            'entries.*.credit_amount' => 'nullable|numeric|min:0',
            'entries.*.particulars' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate that debits equal credits
        $totalDebits = collect($request->entries)->sum('debit_amount');
        $totalCredits = collect($request->entries)->sum('credit_amount');

        if (abs($totalDebits - $totalCredits) > 0.01) {
            return redirect()->back()
                ->withErrors(['entries' => 'Total debits must equal total credits'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Update voucher
            $voucher->update([
                'voucher_date' => $request->voucher_date,
                'reference_number' => $request->reference_number,
                'narration' => $request->narration,
                'total_amount' => $totalDebits,
            ]);

            // Delete existing entries
            $voucher->entries()->delete();

            // Create new entries
            foreach ($request->entries as $entry) {
                if (($entry['debit_amount'] ?? 0) > 0 || ($entry['credit_amount'] ?? 0) > 0) {
                    VoucherEntry::create([
                        'voucher_id' => $voucher->id,
                        'ledger_account_id' => $entry['ledger_account_id'],
                        'debit_amount' => $entry['debit_amount'] ?? 0,
                        'credit_amount' => $entry['credit_amount'] ?? 0,
                        'particulars' => $entry['particulars'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('tenant.vouchers.show', ['tenant' => tenant()->slug, 'voucher' => $voucher->id])
                ->with('success', 'Voucher updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update voucher: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function post($id)
    {
        $voucher = Voucher::where('tenant_id', tenant()->id)->findOrFail($id);

        try {
            $voucher->post(auth()->id());
            return redirect()->back()
                ->with('success', 'Voucher posted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function unpost($id)
    {
        $voucher = Voucher::where('tenant_id', tenant()->id)->findOrFail($id);

        try {
            $voucher->unpost();
            return redirect()->back()
                ->with('success', 'Voucher un-posted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $voucher = Voucher::where('tenant_id', tenant()->id)->findOrFail($id);

        if ($voucher->status === 'posted') {
            return redirect()->back()
                ->with('error', 'Posted vouchers cannot be deleted.');
        }

        $voucher->delete();

        return redirect()->route('tenant.vouchers.index', ['tenant' => tenant()->slug])
            ->with('success', 'Voucher deleted successfully.');
    }
}
