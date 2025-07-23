<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;

class PayrollController extends Controller
{
    /**
     * Display the payroll dashboard
     */
    public function index(Request $request, Tenant $tenant)
    {
        $currentTenant = $tenant;
        $user = auth()->user();

        // You would typically load payroll data here
        // For example:
        // $totalEmployees = Employee::where('tenant_id', $tenant->id)->count();
        // $monthlyPayroll = Payroll::where('tenant_id', $tenant->id)->whereMonth('pay_date', now()->month)->sum('net_pay');
        // $pendingPayrolls = Payroll::where('tenant_id', $tenant->id)->where('status', 'pending')->count();

        return view('tenant.payroll.index', [
            'currentTenant' => $currentTenant,
            'user' => $user,
            'tenant' => $currentTenant,
        ]);
    }
}
