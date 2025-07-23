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
            ->where('type', 'income')
            ->where('is_active', true)
            ->get();

        // Get expense accounts
        $expenseAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('type', 'expense')
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

        $accounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $trialBalanceData = [];
        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($accounts as $account) {
            $balance = $account->current_balance ?? 0;

            if ($balance != 0) {
                // Determine if balance is debit or credit based on account type
                $isDebit = in_array($account->type, ['asset', 'expense']);

                $debitAmount = $isDebit ? abs($balance) : 0;
                $creditAmount = !$isDebit ? abs($balance) : 0;

                $trialBalanceData[] = [
                    'account' => $account,
                    'debit_amount' => $debitAmount,
                    'credit_amount' => $creditAmount,
                ];

                $totalDebits += $debitAmount;
                $totalCredits += $creditAmount;
            }
        }

        return view('tenant.reports.trial-balance', compact(
            'trialBalanceData',
            'totalDebits',
            'totalCredits',
            'asOfDate'
        ));
    }
}
