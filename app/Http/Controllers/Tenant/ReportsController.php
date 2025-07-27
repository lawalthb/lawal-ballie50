<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\LedgerAccount;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Tenant $tenant)
    {
        return view('tenant.reports.index', compact('tenant'));
    }

    public function profitLoss(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        // Get income accounts
        $incomeAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'income')
            ->where('is_active', true)
            ->get();

        // Get expense accounts
        $expenseAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'expense')
            ->where('is_active', true)
            ->get();

        $incomeData = [];
        $expenseData = [];
        $totalIncome = 0;
        $totalExpenses = 0;

        // Calculate income
        foreach ($incomeAccounts as $account) {
            $balance = $account->current_balance ?? 0;
            if ($balance != 0) {
                $incomeData[] = [
                    'account' => $account,
                    'amount' => abs($balance),
                ];
                $totalIncome += abs($balance);
            }
        }

        // Calculate expenses
        foreach ($expenseAccounts as $account) {
            $balance = $account->current_balance ?? 0;
            if ($balance != 0) {
                $expenseData[] = [
                    'account' => $account,
                    'amount' => abs($balance),
                ];
                $totalExpenses += abs($balance);
            }
        }

        // Calculate stock values for the period
        $openingStock = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->sum('opening_stock_value');

        $closingStock = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->sum('current_stock_value');

        // Add stock to P&L calculation
        if ($openingStock > 0) {
            $expenseData[] = [
                'account' => (object)['name' => 'Opening Stock', 'code' => 'OPENING_STOCK'],
                'amount' => $openingStock,
            ];
            $totalExpenses += $openingStock;
        }

        if ($closingStock > 0) {
            $incomeData[] = [
                'account' => (object)['name' => 'Closing Stock', 'code' => 'CLOSING_STOCK'],
                'amount' => $closingStock,
            ];
            $totalIncome += $closingStock;
        }

        $netProfit = $totalIncome - $totalExpenses;

        return view('tenant.reports.profit-loss', compact(
            'incomeData',
            'expenseData',
            'totalIncome',
            'totalExpenses',
            'netProfit',
            'fromDate',
            'toDate',
            'openingStock',
            'closingStock'
        ));
    }

    public function trialBalance(Request $request, Tenant $tenant)
    {
        $asOfDate = $request->get('as_of_date', now()->toDateString());

        // Get all active accounts with their relationships
        $accounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with(['accountGroup', 'voucherEntries' => function($query) use ($asOfDate) {
                $query->whereHas('voucher', function($voucherQuery) use ($asOfDate) {
                    $voucherQuery->where('voucher_date', '<=', $asOfDate)
                             ->where('status', 'posted');
                });
            }])
            ->orderBy('code')
            ->get();

        $trialBalanceData = [];
        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($accounts as $account) {
            // Calculate actual balance based on transactions up to the specified date
            $balance = $this->calculateAccountBalance($account, $asOfDate);

            if (abs($balance) >= 0.01) { // Show accounts with balance >= 1 cent
                // Determine the natural balance side for this account type
                $naturalBalanceSide = $this->getNaturalBalanceSide($account->account_type);

                if ($naturalBalanceSide === 'debit') {
                    $debitAmount = $balance >= 0 ? $balance : 0;
                    $creditAmount = $balance < 0 ? abs($balance) : 0;
                } else {
                    $creditAmount = $balance >= 0 ? $balance : 0;
                    $debitAmount = $balance < 0 ? abs($balance) : 0;
                }

                $trialBalanceData[] = [
                    'account' => $account,
                    'opening_balance' => $account->opening_balance ?? 0,
                    'current_balance' => $balance,
                    'debit_amount' => $debitAmount,
                    'credit_amount' => $creditAmount,
                ];

                $totalDebits += $debitAmount;
                $totalCredits += $creditAmount;
            }
        }

        // Sort by account code
        usort($trialBalanceData, function($a, $b) {
            return strcmp($a['account']->code, $b['account']->code);
        });

        return view('tenant.reports.trial-balance', compact(
            'trialBalanceData',
            'totalDebits',
            'totalCredits',
            'asOfDate',
            'tenant'
        ));
    }

    /**
     * Calculate account balance as of specific date
     */
    private function calculateAccountBalance($account, $asOfDate)
    {
        // Start with opening balance
        $balance = $account->opening_balance ?? 0;

        // Add all transactions up to the specified date
        $totalDebits = $account->voucherEntries()->whereHas('voucher', function($query) use ($asOfDate) {
            $query->where('voucher_date', '<=', $asOfDate)
                  ->where('status', 'posted');
        })->sum('debit_amount');

        $totalCredits = $account->voucherEntries()->whereHas('voucher', function($query) use ($asOfDate) {
            $query->where('voucher_date', '<=', $asOfDate)
                  ->where('status', 'posted');
        })->sum('credit_amount');

        // For accounting: Debit increases assets and expenses, Credit increases liabilities, equity, and income
        if (in_array($account->account_type, ['asset', 'expense'])) {
            // Assets and Expenses: Debit increases, Credit decreases
            $balance = $balance + $totalDebits - $totalCredits;
        } else {
            // Liabilities, Equity, Income: Credit increases, Debit decreases
            $balance = $balance + $totalCredits - $totalDebits;
        }

        return $balance;
    }

    /**
     * Get the natural balance side for an account type
     */
    private function getNaturalBalanceSide($accountType)
    {
        return match($accountType) {
            'asset', 'expense' => 'debit',
            'liability', 'equity', 'income' => 'credit',
            default => 'debit'
        };
    }
}
