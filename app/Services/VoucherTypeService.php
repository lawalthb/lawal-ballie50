<?php

namespace App\Services;

use App\Models\VoucherType;
use App\Models\Voucher;
use Database\Seeders\VoucherTypeSeeder;

class VoucherTypeService
{
    public function initializeSystemVoucherTypes($tenantId)
    {
        // Check if system voucher types already exist
        $existingCount = VoucherType::where('tenant_id', $tenantId)
            ->where('is_system_defined', true)
            ->count();

        if ($existingCount === 0) {
            VoucherTypeSeeder::seedForTenant($tenantId);
            return true;
        }

        return false;
    }

    public function getVoucherTypeStats($tenantId)
    {
        $total = VoucherType::where('tenant_id', $tenantId)->count();
        $active = VoucherType::where('tenant_id', $tenantId)->where('is_active', true)->count();
        $system = VoucherType::where('tenant_id', $tenantId)->where('is_system_defined', true)->count();
        $custom = VoucherType::where('tenant_id', $tenantId)->where('is_system_defined', false)->count();

        return compact('total', 'active', 'system', 'custom');
    }

    public function validateVoucherTypeCode($code, $tenantId, $excludeId = null)
    {
        $query = VoucherType::where('tenant_id', $tenantId)
            ->where('code', strtoupper($code));

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists();
    }

    public function generateSuggestedCode($name)
    {
        // Generate a suggested code based on the name
        $words = explode(' ', strtoupper($name));

        if (count($words) === 1) {
            return substr($words[0], 0, 3);
        }

        $code = '';
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $code .= $word[0];
            }
        }

        return substr($code, 0, 5);
    }

    public function getNextAvailableNumber($voucherTypeId)
    {
        $voucherType = VoucherType::find($voucherTypeId);

        if (!$voucherType) {
            return 1;
        }

        return $voucherType->current_number + 1;
    }

    public function getPrimaryVoucherTypeOptions()
    {
        return [
            'journal' => [
                'name' => 'Journal Voucher',
                'code' => 'JV',
                'abbreviation' => 'J',
                'description' => 'For general journal entries and adjustments',
                'affects_inventory' => false,
                'affects_cashbank' => false,
                'has_reference' => false,
                'prefix' => 'JV-'
            ],
            'payment' => [
                'name' => 'Payment Voucher',
                'code' => 'PV',
                'abbreviation' => 'P',
                'description' => 'For recording payments made',
                'affects_inventory' => false,
                'affects_cashbank' => true,
                'has_reference' => true,
                'prefix' => 'PV-'
            ],
            'receipt' => [
                'name' => 'Receipt Voucher',
                'code' => 'RV',
                'abbreviation' => 'R',
                'description' => 'For recording receipts received',
                'affects_inventory' => false,
                'affects_cashbank' => true,
                'has_reference' => true,
                'prefix' => 'RV-'
            ],
            'contra' => [
                'name' => 'Contra Voucher',
                'code' => 'CV',
                'abbreviation' => 'C',
                'description' => 'For transfers between cash and bank accounts',
                'affects_inventory' => false,
                'affects_cashbank' => true,
                'has_reference' => true,
                'prefix' => 'CV-'
            ],
            'sales' => [
                'name' => 'Sales Voucher',
                'code' => 'SV',
                'abbreviation' => 'S',
                'description' => 'For recording sales transactions',
                'affects_inventory' => true,
                'affects_cashbank' => false,
                'has_reference' => true,
                'prefix' => 'SV-'
            ],
            'purchase' => [
                'name' => 'Purchase Voucher',
                'code' => 'PUR',
                'abbreviation' => 'PU',
                'description' => 'For recording purchase transactions',
                'affects_inventory' => true,
                'affects_cashbank' => false,
                'has_reference' => true,
                'prefix' => 'PUR-'
            ],
            'credit_note' => [
                'name' => 'Credit Note',
                'code' => 'CN',
                'abbreviation' => 'CN',
                'description' => 'For sales returns and allowances',
                'affects_inventory' => true,
                'affects_cashbank' => false,
                'has_reference' => true,
                'prefix' => 'CN-'
            ],
            'debit_note' => [
                'name' => 'Debit Note',
                'code' => 'DN',
                'abbreviation' => 'DN',
                'description' => 'For purchase returns and claims',
                'affects_inventory' => true,
                'affects_cashbank' => false,
                'has_reference' => true,
                'prefix' => 'DN-'
            ]
        ];
    }

    public function canDeleteVoucherType($voucherTypeId)
    {
        $voucherType = VoucherType::find($voucherTypeId);

        if (!$voucherType) {
            return false;
        }

        // Cannot delete system-defined voucher types
        if ($voucherType->is_system_defined) {
            return false;
        }

        // Cannot delete if there are associated vouchers
        if ($voucherType->vouchers()->count() > 0) {
            return false;
        }

        return true;
    }

    public function duplicateVoucherType($voucherTypeId, $newName)
    {
        $originalVoucherType = VoucherType::find($voucherTypeId);

        if (!$originalVoucherType) {
            return null;
        }

        $newVoucherType = $originalVoucherType->replicate();
        $newVoucherType->name = $newName;
        $newVoucherType->code = $this->generateSuggestedCode($newName);
        $newVoucherType->is_system_defined = false;
        $newVoucherType->current_number = 0;
        $newVoucherType->save();

        return $newVoucherType;
    }
}