<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_id',
        'ledger_account_id',
        'debit_amount',
        'credit_amount',
        'particulars',
    ];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
    ];
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
    // Relationships
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function ledgerAccount()
    {
        return $this->belongsTo(LedgerAccount::class);
    }

    // Methods
    public function getAmount()
    {
        return $this->debit_amount > 0 ? $this->debit_amount : $this->credit_amount;
    }

    public function getType()
    {
        return $this->debit_amount > 0 ? 'dr' : 'cr';
    }

    public function isDebit()
    {
        return $this->debit_amount > 0;
    }

    public function isCredit()
    {
        return $this->credit_amount > 0;
    }
    public function account()
    {
        return $this->belongsTo(LedgerAccount::class, 'ledger_account_id');
    }
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

}
