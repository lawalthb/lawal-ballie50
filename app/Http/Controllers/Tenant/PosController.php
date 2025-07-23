<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;

class PosController extends Controller
{
    /**
     * Display the POS dashboard
     */
    public function index(Request $request, Tenant $tenant)
    {
        $currentTenant = $tenant;
        $user = auth()->user();

        // You would typically load POS data here
        // For example:
        // $todaySales = Sale::where('tenant_id', $tenant->id)->whereDate('created_at', today())->sum('total');
        // $todayTransactions = Sale::where('tenant_id', $tenant->id)->whereDate('created_at', today())->count();
        // $topSellingProducts = Product::where('tenant_id', $tenant->id)->orderBy('sales_count', 'desc')->take(5)->get();

        return view('tenant.pos.index', [
            'currentTenant' => $currentTenant,
            'user' => $user,
            'tenant' => $currentTenant,
        ]);
    }
}
