<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\Accounting\VoucherController;
use App\Http\Controllers\Tenant\Accounting\VoucherTypeController;
use App\Http\Controllers\Tenant\Accounting\AccountGroupController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\AuthController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\OnboardingController;
use App\Http\Controllers\Tenant\ProductController;
use App\Http\Controllers\Tenant\Crm\CustomerController;
use App\Http\Controllers\Tenant\Accounting\InvoiceController;
use App\Http\Controllers\Tenant\HelpController;
use App\Http\Controllers\Tenant\SupportController;
use App\Http\Controllers\Tenant\CommunityController;
use App\Http\Controllers\Tenant\AccountingController;
use App\Http\Controllers\Tenant\InventoryController;
use App\Http\Controllers\Tenant\Crm\CrmController;
use App\Http\Controllers\Tenant\PosController;
use App\Http\Controllers\Tenant\PayrollController;
use App\Http\Controllers\Tenant\ReportsController;
use App\Http\Controllers\Tenant\DocumentsController;
use App\Http\Controllers\Tenant\ActivityController;
use App\Http\Controllers\Tenant\ProductCategoryController;
use App\Http\Controllers\Tenant\SettingsController;
use App\Http\Controllers\Tenant\Crm\VendorController;
use App\Http\Controllers\Tenant\UnitController;
use App\Models\Tenant;
use App\Http\Controllers\Tenant\Accounting\LedgerAccountController;
use App\Http\Controllers\Auth\SocialAuthController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the RouteServiceProvider and are
| prefixed with the tenant slug from the main web.php routes.
|
*/

// Route model binding for tenant
Route::bind('tenant', function ($value) {
    return Tenant::where('slug', $value)->firstOrFail();
});

// Guest routes (login, register, etc.)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('tenant.login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('tenant.register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('tenant.password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('tenant.password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('tenant.password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('tenant.password.update');

    // Social Authentication Routes
    Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('tenant.auth.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('tenant.auth.callback');
    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('tenant.auth.google');
    Route::get('/auth/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('tenant.auth.facebook');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Logout route
    Route::post('/logout', [AuthController::class, 'logout'])->name('tenant.logout');

    // Onboarding routes
    Route::prefix('onboarding')->name('tenant.onboarding.')->group(function () {
        Route::get('/', [OnboardingController::class, 'index'])->name('index');
        Route::post('/complete', [OnboardingController::class, 'complete'])->name('complete');
        Route::get('/{step}', [OnboardingController::class, 'showStep'])->name('step');
        Route::post('/{step}', [OnboardingController::class, 'saveStep'])->name('save-step');
        Route::get('/show-step', [OnboardingController::class, 'showStep'])->name('show-step');
    });

    // Routes that require completed onboarding
    Route::middleware(['onboarding.completed'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');

        // Accounting Module
        Route::prefix('accounting')->name('tenant.accounting.')->group(function () {
            Route::get('/', [AccountingController::class, 'index'])->name('index');

            // Invoices (moved from root level)
         // Sales Invoices
Route::prefix('invoices')->name('invoices.')->group(function () {
    Route::get('/', [InvoiceController::class, 'index'])->name('index');
    Route::get('/create', [InvoiceController::class, 'create'])->name('create');
    Route::post('/', [InvoiceController::class, 'store'])->name('store');
    Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
    Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
    Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
    Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
    Route::post('/{invoice}/post', [InvoiceController::class, 'post'])->name('post');
    Route::post('/{invoice}/unpost', [InvoiceController::class, 'unpost'])->name('unpost');
    Route::get('/{invoice}/print', [InvoiceController::class, 'print'])->name('print');
    Route::get('/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('pdf');
    Route::post('/{invoice}/email', [InvoiceController::class, 'email'])->name('email');
    Route::post('/{invoice}/record-payment', [InvoiceController::class, 'recordPayment'])->name('record-payment');



});

            // Account Groups
           Route::prefix('account-groups')->name('account-groups.')->group(function () {
    Route::get('/', [AccountGroupController::class, 'index'])->name('index');
    Route::get('/create', [AccountGroupController::class, 'create'])->name('create');
    Route::post('/', [AccountGroupController::class, 'store'])->name('store');
    Route::get('/{account_group}', [AccountGroupController::class, 'show'])->name('show');
    Route::get('/{account_group}/edit', [AccountGroupController::class, 'edit'])->name('edit');
    Route::put('/{account_group}', [AccountGroupController::class, 'update'])->name('update');
    Route::delete('/{account_group}', [AccountGroupController::class, 'destroy'])->name('destroy');

    // Account Group specific actions
    Route::post('/{account_group}/toggle', [AccountGroupController::class, 'toggle'])->name('toggle');
    Route::post('/bulk-action', [AccountGroupController::class, 'bulkAction'])->name('bulk-action');

    // Export/Import routes
    Route::get('/export/template', [AccountGroupController::class, 'downloadTemplate'])->name('export.template');
    Route::post('/import', [AccountGroupController::class, 'import'])->name('import');
    Route::get('/export/all', [AccountGroupController::class, 'export'])->name('export');

    // Hierarchy management
    Route::post('/{account_group}/move', [AccountGroupController::class, 'move'])->name('move');
    Route::get('/hierarchy', [AccountGroupController::class, 'hierarchy'])->name('hierarchy');
    Route::post('/reorder', [AccountGroupController::class, 'reorder'])->name('reorder');

    //Route [tenant.accounting.account-groups.bulk] not defined.
    Route::post('/bulk', [AccountGroupController::class, 'bulk'])->name('bulk');
});

            // Voucher Types
            Route::prefix('voucher-types')->name('voucher-types.')->group(function () {
                Route::get('/', [VoucherTypeController::class, 'index'])->name('index');
                Route::get('/create', [VoucherTypeController::class, 'create'])->name('create');
                Route::post('/', [VoucherTypeController::class, 'store'])->name('store');
                Route::get('/{voucherType}', [VoucherTypeController::class, 'show'])->name('show');
                Route::get('/{voucherType}/edit', [VoucherTypeController::class, 'edit'])->name('edit');
                Route::put('/{voucherType}', [VoucherTypeController::class, 'update'])->name('update');
                Route::delete('/{voucherType}', [VoucherTypeController::class, 'destroy'])->name('destroy');
                Route::post('/{voucherType}/reset-numbering', [VoucherTypeController::class, 'resetNumbering'])->name('reset-numbering');

                Route::post('/bulk-action', [VoucherTypeController::class, 'bulkAction'])->name('bulk-action');
                Route::post('/toggle/{voucherType}', [VoucherTypeController::class, 'toggle'])->name('toggle');

            });

          // Vouchers
Route::prefix('vouchers')->name('vouchers.')->group(function () {
    Route::get('/', [VoucherController::class, 'index'])->name('index');
    Route::get('/create', [VoucherController::class, 'create'])->name('create');
    Route::get('/create/{type}', [VoucherController::class, 'create'])->name('create.type'); // Add this line
    Route::post('/', [VoucherController::class, 'store'])->name('store');
    Route::get('/{voucher}', [VoucherController::class, 'show'])->name('show');
    Route::get('/{voucher}/edit', [VoucherController::class, 'edit'])->name('edit');
    Route::put('/{voucher}', [VoucherController::class, 'update'])->name('update');
    Route::delete('/{voucher}', [VoucherController::class, 'destroy'])->name('destroy');

    // Voucher actions
    Route::post('/{voucher}/post', [VoucherController::class, 'post'])->name('post');
    Route::post('/{voucher}/unpost', [VoucherController::class, 'unpost'])->name('unpost');
    Route::get('/{voucher}/duplicate', [VoucherController::class, 'duplicate'])->name('duplicate');
    Route::get('/{voucher}/pdf', [VoucherController::class, 'pdf'])->name('pdf');
    Route::get('/{voucher}/print', [VoucherController::class, 'print'])->name('print');

    // Bulk actions
    Route::post('/bulk/post', [VoucherController::class, 'bulkPost'])->name('bulk.post');
    Route::delete('/bulk/delete', [VoucherController::class, 'bulkDelete'])->name('bulk.delete');
    Route::get('/export', [VoucherController::class, 'export'])->name('export');
    Route::post('/bulk-action', [VoucherController::class, 'bulkAction'])->name('bulk.action');
});


// Ledger Accounts
Route::prefix('ledger-accounts')->name('ledger-accounts.')->group(function () {
     Route::get('/', [LedgerAccountController::class, 'index'])->name('index');
    Route::get('/create', [LedgerAccountController::class, 'create'])->name('create');
       Route::get('/template', [LedgerAccountController::class, 'downloadTemplate'])->name('template');

    // Search API - MUST be before parameterized routes
    Route::get('/search', [LedgerAccountController::class, 'search'])->name('search');

    Route::post('/', [LedgerAccountController::class, 'store'])->name('store');
    Route::get('/{ledgerAccount}', [LedgerAccountController::class, 'show'])->name('show');
    Route::get('/{ledgerAccount}/edit', [LedgerAccountController::class, 'edit'])->name('edit');
    Route::put('/{ledgerAccount}', [LedgerAccountController::class, 'update'])->name('update');
    Route::delete('/{ledgerAccount}', [LedgerAccountController::class, 'destroy'])->name('destroy');

    // Export/Import routes
    Route::get('/export/template', [LedgerAccountController::class, 'downloadTemplate'])->name('export.template');
    Route::post('/import', [LedgerAccountController::class, 'import'])->name('import');
    Route::get('/export/all', [LedgerAccountController::class, 'export'])->name('export');

    // Individual account actions
    Route::get('/{ledgerAccount}/export-ledger', [LedgerAccountController::class, 'exportLedger'])->name('export-ledger');
    Route::get('/{ledgerAccount}/print-ledger', [LedgerAccountController::class, 'printLedger'])->name('print-ledger');
    Route::get('/{ledgerAccount}/balance', [LedgerAccountController::class, 'getBalance'])->name('balance');

    // Bulk actions
    Route::post('/bulk-delete', [LedgerAccountController::class, 'bulkDelete'])->name('bulk-delete');
    Route::post('/bulk-activate', [LedgerAccountController::class, 'bulkActivate'])->name('bulk-activate');
    Route::post('/bulk-deactivate', [LedgerAccountController::class, 'bulkDeactivate'])->name('bulk-deactivate');

    Route::patch('/{ledgerAccount}/toggle-status', [LedgerAccountController::class, 'toggleStatus'])->name('toggle-status');


});




            // Expenses (add if not exists)
            Route::prefix('expenses')->name('expenses.')->group(function () {
                Route::get('/', [ExpenseController::class, 'index'])->name('index');
                Route::get('/create', [ExpenseController::class, 'create'])->name('create');
                Route::post('/', [ExpenseController::class, 'store'])->name('store');
                Route::get('/{expense}', [ExpenseController::class, 'show'])->name('show');
                Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->name('edit');
                Route::put('/{expense}', [ExpenseController::class, 'update'])->name('update');
                Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
            });

            // Payments (add if not exists)
            Route::prefix('payments')->name('payments.')->group(function () {
                Route::get('/', [PaymentController::class, 'index'])->name('index');
                Route::get('/create', [PaymentController::class, 'create'])->name('create');
                Route::post('/', [PaymentController::class, 'store'])->name('store');
                Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
                Route::get('/{payment}/edit', [PaymentController::class, 'edit'])->name('edit');
                Route::put('/{payment}', [PaymentController::class, 'update'])->name('update');
                Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('destroy');
            });

            // Chart of Accounts (add if not exists)
            Route::prefix('chart-of-accounts')->name('chart-of-accounts.')->group(function () {
                Route::get('/', [ChartOfAccountsController::class, 'index'])->name('index');
                Route::get('/create', [ChartOfAccountsController::class, 'create'])->name('create');
                Route::post('/', [ChartOfAccountsController::class, 'store'])->name('store');
                Route::get('/{account}', [ChartOfAccountsController::class, 'show'])->name('show');
                Route::get('/{account}/edit', [ChartOfAccountsController::class, 'edit'])->name('edit');
                Route::put('/{account}', [ChartOfAccountsController::class, 'update'])->name('update');
                Route::delete('/{account}', [ChartOfAccountsController::class, 'destroy'])->name('destroy');
            });

            // Trial Balance
            Route::get('/trial-balance', [ReportsController::class, 'trialBalance'])->name('trial-balance');

            // Balance Sheet
            Route::get('/balance-sheet', [ReportsController::class, 'balanceSheet'])->name('balance-sheet');



            Route::get('/profit-loss', [ReportsController::class, 'profitLoss'])->name('profit-loss');

            // Cash Flow
            Route::get('/cash-flow', [ReportsController::class, 'cashFlow'])->name('cash-flow');
        });

        // Inventory Module
        Route::prefix('inventory')->name('tenant.inventory.')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('index');

            // Products
            Route::prefix('products')->name('products.')->group(function () {
                Route::get('/', [ProductController::class, 'index'])->name('index');
                Route::get('/create', [ProductController::class, 'create'])->name('create');
                Route::post('/', [ProductController::class, 'store'])->name('store');
                Route::get('/{product}', [ProductController::class, 'show'])->name('show');
                Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
                Route::put('/{product}', [ProductController::class, 'update'])->name('update');
                Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
                Route::patch('/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');
                Route::post('/bulk-action', [ProductController::class, 'bulkAction'])->name('bulk-action');
                Route::get('/export', [ProductController::class, 'export'])->name('export');
            });

            // Product Categories
            Route::prefix('categories')->name('categories.')->group(function () {
                Route::get('/', [ProductCategoryController::class, 'index'])->name('index');
                Route::get('/create', [ProductCategoryController::class, 'create'])->name('create');
                Route::post('/', [ProductCategoryController::class, 'store'])->name('store');
                Route::get('/{category}', [ProductCategoryController::class, 'show'])->name('show');
                Route::get('/{category}/edit', [ProductCategoryController::class, 'edit'])->name('edit');
                Route::put('/{category}', [ProductCategoryController::class, 'update'])->name('update');
                Route::delete('/{category}', [ProductCategoryController::class, 'destroy'])->name('destroy');
                Route::patch('/{category}/toggle-status', [ProductCategoryController::class, 'toggleStatus'])->name('toggle-status');
            });

            // Units
            Route::prefix('units')->name('units.')->group(function () {
                Route::get('/', [UnitController::class, 'index'])->name('index');
                Route::get('/create', [UnitController::class, 'create'])->name('create');
                Route::post('/', [UnitController::class, 'store'])->name('store');
                Route::get('/{unit}', [UnitController::class, 'show'])->name('show');
                Route::get('/{unit}/edit', [UnitController::class, 'edit'])->name('edit');
                Route::put('/{unit}', [UnitController::class, 'update'])->name('update');
                Route::delete('/{unit}', [UnitController::class, 'destroy'])->name('destroy');
                Route::patch('/{unit}/toggle-status', [UnitController::class, 'toggleStatus'])->name('toggle-status');
            });

            // Stock Adjustments
            Route::prefix('stock-adjustments')->name('stock-adjustments.')->group(function () {
                Route::get('/', [StockAdjustmentController::class, 'index'])->name('index');
                Route::get('/create', [StockAdjustmentController::class, 'create'])->name('create');
                Route::post('/', [StockAdjustmentController::class, 'store'])->name('store');
                Route::get('/{adjustment}', [StockAdjustmentController::class, 'show'])->name('show');
                Route::get('/{adjustment}/edit', [StockAdjustmentController::class, 'edit'])->name('edit');
                Route::put('/{adjustment}', [StockAdjustmentController::class, 'update'])->name('update');
                Route::delete('/{adjustment}', [StockAdjustmentController::class, 'destroy'])->name('destroy');
            });

            // Stock Reports
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/stock-summary', [InventoryController::class, 'stockSummary'])->name('stock-summary');
                Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('low-stock');
                Route::get('/stock-movement', [InventoryController::class, 'stockMovement'])->name('stock-movement');
                Route::get('/valuation', [InventoryController::class, 'valuation'])->name('valuation');
            });
        });

        // CRM Module
        Route::prefix('crm')->name('tenant.crm.')->group(function () {
            Route::get('/', [CrmController::class, 'index'])->name('index');

            // Customers
            Route::prefix('customers')->name('customers.')->group(function () {
                Route::get('/', [CustomerController::class, 'index'])->name('index');
                Route::get('/statements', [CustomerController::class, 'statements'])->name('statements');
                Route::get('/create', [CustomerController::class, 'create'])->name('create');
                Route::post('/', [CustomerController::class, 'store'])->name('store');
                Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
                Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
                Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
                Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
                Route::patch('/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('toggle-status');
                Route::post('/bulk-action', [CustomerController::class, 'bulkAction'])->name('bulk-action');
                Route::get('/export', [CustomerController::class, 'export'])->name('export');
            });

            // Vendors/Suppliers
            Route::prefix('vendors')->name('vendors.')->group(function () {
                Route::get('/', [VendorController::class, 'index'])->name('index');
                Route::get('/create', [VendorController::class, 'create'])->name('create');
                Route::post('/', [VendorController::class, 'store'])->name('store');
                Route::get('/{vendor}', [VendorController::class, 'show'])->name('show');
                Route::get('/{vendor}/edit', [VendorController::class, 'edit'])->name('edit');
                Route::put('/{vendor}', [VendorController::class, 'update'])->name('update');
                Route::delete('/{vendor}', [VendorController::class, 'destroy'])->name('destroy');
                Route::patch('/{vendor}/toggle-status', [VendorController::class, 'toggleStatus'])->name('toggle-status');
                Route::post('/bulk-action', [VendorController::class, 'bulkAction'])->name('bulk-action');
                Route::get('/export', [VendorController::class, 'export'])->name('export');
            });

            // Leads
            Route::prefix('leads')->name('leads.')->group(function () {
                Route::get('/', [LeadController::class, 'index'])->name('index');
                Route::get('/create', [LeadController::class, 'create'])->name('create');
                Route::post('/', [LeadController::class, 'store'])->name('store');
                Route::get('/{lead}', [LeadController::class, 'show'])->name('show');
                Route::get('/{lead}/edit', [LeadController::class, 'edit'])->name('edit');
                Route::put('/{lead}', [LeadController::class, 'update'])->name('update');
                Route::delete('/{lead}', [LeadController::class, 'destroy'])->name('destroy');
                Route::post('/{lead}/convert', [LeadController::class, 'convert'])->name('convert');
            });

            // Opportunities
            Route::prefix('opportunities')->name('opportunities.')->group(function () {
                Route::get('/', [OpportunityController::class, 'index'])->name('index');
                Route::get('/create', [OpportunityController::class, 'create'])->name('create');
                Route::post('/', [OpportunityController::class, 'store'])->name('store');
                Route::get('/{opportunity}', [OpportunityController::class, 'show'])->name('show');
                Route::get('/{opportunity}/edit', [OpportunityController::class, 'edit'])->name('edit');
                Route::put('/{opportunity}', [OpportunityController::class, 'update'])->name('update');
                Route::delete('/{opportunity}', [OpportunityController::class, 'destroy'])->name('destroy');
            });
        });

        // POS Module
        Route::prefix('pos')->name('tenant.pos.')->group(function () {
            Route::get('/', [PosController::class, 'index'])->name('index');
            Route::get('/create', [PosController::class, 'create'])->name('create');
            Route::post('/', [PosController::class, 'store'])->name('store');
            Route::get('/{sale}', [PosController::class, 'show'])->name('show');
            Route::get('/{sale}/receipt', [PosController::class, 'receipt'])->name('receipt');
            Route::post('/{sale}/refund', [PosController::class, 'refund'])->name('refund');
            Route::get('/reports', [PosController::class, 'reports'])->name('reports');
        });

        // Payroll Module
        Route::prefix('payroll')->name('tenant.payroll.')->group(function () {
            Route::get('/', [PayrollController::class, 'index'])->name('index');

            // Employees
            Route::prefix('employees')->name('employees.')->group(function () {
                Route::get('/', [PayrollController::class, 'employees'])->name('index');
                Route::get('/create', [PayrollController::class, 'createEmployee'])->name('create');
                Route::post('/', [PayrollController::class, 'storeEmployee'])->name('store');
                Route::get('/{employee}', [PayrollController::class, 'showEmployee'])->name('show');
                Route::get('/{employee}/edit', [PayrollController::class, 'editEmployee'])->name('edit');
                Route::put('/{employee}', [PayrollController::class, 'updateEmployee'])->name('update');
                Route::delete('/{employee}', [PayrollController::class, 'destroyEmployee'])->name('destroy');
            });

            // Payroll Processing
            Route::prefix('processing')->name('processing.')->group(function () {
                Route::get('/', [PayrollController::class, 'processing'])->name('index');
                Route::get('/create', [PayrollController::class, 'createPayroll'])->name('create');
                Route::post('/', [PayrollController::class, 'storePayroll'])->name('store');
                Route::get('/{payroll}', [PayrollController::class, 'showPayroll'])->name('show');
                Route::post('/{payroll}/approve', [PayrollController::class, 'approvePayroll'])->name('approve');
                Route::post('/{payroll}/process', [PayrollController::class, 'processPayroll'])->name('process');
            });

            // Payroll Reports
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/summary', [PayrollController::class, 'payrollSummary'])->name('summary');
                Route::get('/detailed', [PayrollController::class, 'detailedReport'])->name('detailed');
                Route::get('/tax-report', [PayrollController::class, 'taxReport'])->name('tax-report');
            });
        });

        // Reports Module
        Route::prefix('reports')->name('tenant.reports.')->group(function () {
            Route::get('/', [ReportsController::class, 'index'])->name('index');

            // Financial Reports
            Route::prefix('financial')->name('financial.')->group(function () {
                Route::get('/profit-loss', [ReportsController::class, 'profitLoss'])->name('profit-loss');
                Route::get('/balance-sheet', [ReportsController::class, 'balanceSheet'])->name('balance-sheet');
                Route::get('/cash-flow', [ReportsController::class, 'cashFlow'])->name('cash-flow');
                Route::get('/trial-balance', [ReportsController::class, 'trialBalance'])->name('trial-balance');
                Route::get('/ledger', [ReportsController::class, 'ledger'])->name('ledger');
            });

            // Sales Reports
            Route::prefix('sales')->name('sales.')->group(function () {
                Route::get('/summary', [ReportsController::class, 'salesSummary'])->name('summary');
                Route::get('/detailed', [ReportsController::class, 'salesDetailed'])->name('detailed');
                Route::get('/by-customer', [ReportsController::class, 'salesByCustomer'])->name('by-customer');
                Route::get('/by-product', [ReportsController::class, 'salesByProduct'])->name('by-product');
            });

            // Purchase Reports
            Route::prefix('purchases')->name('purchases.')->group(function () {
                Route::get('/summary', [ReportsController::class, 'purchaseSummary'])->name('summary');
                Route::get('/detailed', [ReportsController::class, 'purchaseDetailed'])->name('detailed');
                Route::get('/by-vendor', [ReportsController::class, 'purchaseByVendor'])->name('by-vendor');
            });

            // Inventory Reports
            Route::prefix('inventory')->name('inventory.')->group(function () {
                Route::get('/stock-summary', [ReportsController::class, 'stockSummary'])->name('stock-summary');
                Route::get('/low-stock', [ReportsController::class, 'lowStock'])->name('low-stock');
                Route::get('/stock-movement', [ReportsController::class, 'stockMovement'])->name('stock-movement');
                Route::get('/valuation', [ReportsController::class, 'stockValuation'])->name('valuation');
            });

            // Tax Reports
            Route::prefix('tax')->name('tax.')->group(function () {
                Route::get('/vat-return', [ReportsController::class, 'vatReturn'])->name('vat-return');
                Route::get('/tax-summary', [ReportsController::class, 'taxSummary'])->name('tax-summary');
            });
        });

        // Documents Module
        Route::prefix('documents')->name('tenant.documents.')->group(function () {
            Route::get('/', [DocumentsController::class, 'index'])->name('index');
            Route::get('/create', [DocumentsController::class, 'create'])->name('create');
            Route::post('/', [DocumentsController::class, 'store'])->name('store');
            Route::get('/{document}', [DocumentsController::class, 'show'])->name('show');
            Route::get('/{document}/edit', [DocumentsController::class, 'edit'])->name('edit');
            Route::put('/{document}', [DocumentsController::class, 'update'])->name('update');
            Route::delete('/{document}', [DocumentsController::class, 'destroy'])->name('destroy');
            Route::get('/{document}/download', [DocumentsController::class, 'download'])->name('download');
            Route::post('/bulk-delete', [DocumentsController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('/bulk-move', [DocumentsController::class, 'bulkMove'])->name('bulk-move');
        });

        // Activity Log
        Route::prefix('activity')->name('tenant.activity.')->group(function () {
            Route::get('/', [ActivityController::class, 'index'])->name('index');
            Route::get('/{activity}', [ActivityController::class, 'show'])->name('show');
            Route::delete('/{activity}', [ActivityController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-delete', [ActivityController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('/clear-old', [ActivityController::class, 'clearOld'])->name('clear-old');
        });

        // Settings
        Route::prefix('settings')->name('tenant.settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');

            // General Settings
            Route::prefix('general')->name('general.')->group(function () {
                Route::get('/', [SettingsController::class, 'general'])->name('index');
                Route::put('/', [SettingsController::class, 'updateGeneral'])->name('update');
            });

            // Company Settings
            Route::prefix('company')->name('company.')->group(function () {
                Route::get('/', [SettingsController::class, 'company'])->name('index');
                Route::put('/', [SettingsController::class, 'updateCompany'])->name('update');
            });

            // Financial Settings
            Route::prefix('financial')->name('financial.')->group(function () {
                Route::get('/', [SettingsController::class, 'financial'])->name('index');
                Route::put('/', [SettingsController::class, 'updateFinancial'])->name('update');
            });

            // Tax Settings
            Route::prefix('tax')->name('tax.')->group(function () {
                Route::get('/', [SettingsController::class, 'tax'])->name('index');
                Route::put('/', [SettingsController::class, 'updateTax'])->name('update');
            });

            // Email Settings
            Route::prefix('email')->name('email.')->group(function () {
                Route::get('/', [SettingsController::class, 'email'])->name('index');
                Route::put('/', [SettingsController::class, 'updateEmail'])->name('update');
                Route::post('/test', [SettingsController::class, 'testEmail'])->name('test');
            });

            // Notification Settings
            Route::prefix('notifications')->name('notifications.')->group(function () {
                Route::get('/', [SettingsController::class, 'notifications'])->name('index');
                Route::put('/', [SettingsController::class, 'updateNotifications'])->name('update');
            });

            // User Management
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [SettingsController::class, 'users'])->name('index');
                Route::get('/create', [SettingsController::class, 'createUser'])->name('create');
                Route::post('/', [SettingsController::class, 'storeUser'])->name('store');
                Route::get('/{user}', [SettingsController::class, 'showUser'])->name('show');
                Route::get('/{user}/edit', [SettingsController::class, 'editUser'])->name('edit');
                Route::put('/{user}', [SettingsController::class, 'updateUser'])->name('update');
                Route::delete('/{user}', [SettingsController::class, 'destroyUser'])->name('destroy');
                Route::post('/{user}/toggle-status', [SettingsController::class, 'toggleUserStatus'])->name('toggle-status');
            });

            // Roles & Permissions
            Route::prefix('roles')->name('roles.')->group(function () {
                Route::get('/', [SettingsController::class, 'roles'])->name('index');
                Route::get('/create', [SettingsController::class, 'createRole'])->name('create');
                Route::post('/', [SettingsController::class, 'storeRole'])->name('store');
                Route::get('/{role}', [SettingsController::class, 'showRole'])->name('show');
                Route::get('/{role}/edit', [SettingsController::class, 'editRole'])->name('edit');
                Route::put('/{role}', [SettingsController::class, 'updateRole'])->name('update');
                Route::delete('/{role}', [SettingsController::class, 'destroyRole'])->name('destroy');
            });

            // Backup & Restore
            Route::prefix('backup')->name('backup.')->group(function () {
                Route::get('/', [SettingsController::class, 'backup'])->name('index');
                Route::post('/create', [SettingsController::class, 'createBackup'])->name('create');
                Route::get('/{backup}/download', [SettingsController::class, 'downloadBackup'])->name('download');
                Route::delete('/{backup}', [SettingsController::class, 'deleteBackup'])->name('delete');
                Route::post('/restore', [SettingsController::class, 'restore'])->name('restore');
            });

            // Import/Export
            Route::prefix('import-export')->name('import-export.')->group(function () {
                Route::get('/', [SettingsController::class, 'importExport'])->name('index');
                Route::post('/import', [SettingsController::class, 'import'])->name('import');
                Route::get('/export', [SettingsController::class, 'export'])->name('export');
                Route::get('/templates/{type}', [SettingsController::class, 'downloadTemplate'])->name('template');
            });
        });

        // Help & Support
        Route::prefix('help')->name('tenant.help.')->group(function () {
            Route::get('/', [HelpController::class, 'index'])->name('index');
            Route::get('/getting-started', [HelpController::class, 'gettingStarted'])->name('getting-started');
            Route::get('/tutorials', [HelpController::class, 'tutorials'])->name('tutorials');
            Route::get('/tutorials/{tutorial}', [HelpController::class, 'showTutorial'])->name('tutorials.show');
            Route::get('/faq', [HelpController::class, 'faq'])->name('faq');
            Route::get('/documentation', [HelpController::class, 'documentation'])->name('documentation');
            Route::get('/keyboard-shortcuts', [HelpController::class, 'keyboardShortcuts'])->name('keyboard-shortcuts');
        });

        // Support
        Route::prefix('support')->name('tenant.support.')->group(function () {
            Route::get('/', [SupportController::class, 'index'])->name('index');
            Route::get('/tickets', [SupportController::class, 'tickets'])->name('tickets');
            Route::get('/tickets/create', [SupportController::class, 'createTicket'])->name('tickets.create');
            Route::post('/tickets', [SupportController::class, 'storeTicket'])->name('tickets.store');
            Route::get('/tickets/{ticket}', [SupportController::class, 'showTicket'])->name('tickets.show');
            Route::post('/tickets/{ticket}/reply', [SupportController::class, 'replyTicket'])->name('tickets.reply');
            Route::post('/tickets/{ticket}/close', [SupportController::class, 'closeTicket'])->name('tickets.close');
            Route::get('/contact', [SupportController::class, 'contact'])->name('contact');
            Route::post('/contact', [SupportController::class, 'sendContact'])->name('contact.send');
        });

        // Community
        Route::prefix('community')->name('tenant.community.')->group(function () {
            Route::get('/', [CommunityController::class, 'index'])->name('index');
            Route::get('/forums', [CommunityController::class, 'forums'])->name('forums');
            Route::get('/forums/{forum}', [CommunityController::class, 'showForum'])->name('forums.show');
            Route::get('/topics/{topic}', [CommunityController::class, 'showTopic'])->name('topics.show');
            Route::post('/topics/{topic}/reply', [CommunityController::class, 'replyTopic'])->name('topics.reply');
            Route::get('/announcements', [CommunityController::class, 'announcements'])->name('announcements');
            Route::get('/announcements/{announcement}', [CommunityController::class, 'showAnnouncement'])->name('announcements.show');
        });

        // API Routes for AJAX calls
        Route::prefix('api')->name('tenant.api.')->group(function () {
            // Account Groups API
            Route::prefix('account-groups')->name('account-groups.')->group(function () {
                Route::get('/', [AccountGroupController::class, 'apiIndex'])->name('index');
                Route::get('/{accountGroup}', [AccountGroupController::class, 'apiShow'])->name('show');
                Route::get('/by-nature/{nature}', [AccountGroupController::class, 'apiByNature'])->name('by-nature');
                Route::get('/hierarchy/tree', [AccountGroupController::class, 'apiHierarchy'])->name('hierarchy');
            });

            // Ledger Accounts API
            Route::prefix('ledger-accounts')->name('ledger-accounts.')->group(function () {
                Route::get('/', [LedgerAccountController::class, 'apiIndex'])->name('index');
                Route::get('/{ledgerAccount}', [LedgerAccountController::class, 'apiShow'])->name('show');
                Route::get('/by-group/{groupId}', [LedgerAccountController::class, 'apiByGroup'])->name('by-group');
                Route::get('/search', [LedgerAccountController::class, 'apiSearch'])->name('search');
                Route::post('/', [LedgerAccountController::class, 'apiStore'])->name('store');
            });

            // Voucher Types API
            Route::prefix('voucher-types')->name('voucher-types.')->group(function () {
                Route::get('/', [VoucherTypeController::class, 'apiIndex'])->name('index');
                Route::get('/{voucherType}', [VoucherTypeController::class, 'apiShow'])->name('show');
            });

            // Products API
            Route::prefix('products')->name('products.')->group(function () {
                Route::get('/', [ProductController::class, 'apiIndex'])->name('index');
                Route::get('/{product}', [ProductController::class, 'apiShow'])->name('show');
                Route::get('/search', [ProductController::class, 'apiSearch'])->name('search');
                Route::get('/by-category/{categoryId}', [ProductController::class, 'apiByCategory'])->name('by-category');
            });

            // Customers API
            Route::prefix('customers')->name('customers.')->group(function () {
                Route::get('/', [CustomerController::class, 'apiIndex'])->name('index');
                Route::get('/{customer}', [CustomerController::class, 'apiShow'])->name('show');
                Route::get('/search', [CustomerController::class, 'apiSearch'])->name('search');
            });

            // Vendors API
            Route::prefix('vendors')->name('vendors.')->group(function () {
                Route::get('/', [VendorController::class, 'apiIndex'])->name('index');
                Route::get('/{vendor}', [VendorController::class, 'apiShow'])->name('show');
                Route::get('/search', [VendorController::class, 'apiSearch'])->name('search');
            });

            // Dashboard API
            Route::prefix('dashboard')->name('dashboard.')->group(function () {
                Route::get('/stats', [DashboardController::class, 'apiStats'])->name('stats');
                Route::get('/charts', [DashboardController::class, 'apiCharts'])->name('charts');
                Route::get('/recent-activities', [DashboardController::class, 'apiRecentActivities'])->name('recent-activities');
            });

            // Reports API
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::post('/generate', [ReportsController::class, 'apiGenerate'])->name('generate');
                Route::get('/data/{reportType}', [ReportsController::class, 'apiReportData'])->name('data');
            });
        });

        // Fallback route for the root tenant URL
        Route::get('/', function () {
            return redirect()->route('tenant.dashboard');
        })->name('tenant.home');
    });
});

// Additional API routes that might be needed for specific functionalities
Route::middleware(['auth', 'tenant'])->group(function () {
    // Quick search across all modules
    Route::get('/search', [SearchController::class, 'search'])->name('tenant.search');

    // Global notifications
    Route::prefix('notifications')->name('tenant.notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // File uploads
    Route::prefix('uploads')->name('tenant.uploads.')->group(function () {
        Route::post('/image', [UploadController::class, 'image'])->name('image');
        Route::post('/document', [UploadController::class, 'document'])->name('document');
        Route::delete('/{file}', [UploadController::class, 'destroy'])->name('destroy');
    });

    // Export routes
    Route::prefix('exports')->name('tenant.exports.')->group(function () {
        Route::get('/download/{export}', [ExportController::class, 'download'])->name('download');
        Route::get('/status/{export}', [ExportController::class, 'status'])->name('status');
    });
     // Profit & Loss
            Route::get('/profit-loss', [ReportsController::class, 'profitLoss'])->name('tenant.reports.profit-loss');

             Route::get('/balance-sheet', [ReportsController::class, 'balanceSheet'])->name('tenant.reports.balance-sheet');

 Route::get('/cash-flow', [ReportsController::class, 'cashFlow'])->name('tenant.reports.cash-flow');

               Route::get('/trial-balance', [ReportsController::class, 'trialBalance'])->name('tenant.reports.trial-balance');

});
