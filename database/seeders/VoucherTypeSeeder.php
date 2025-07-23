<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VoucherType;

class VoucherTypeSeeder extends Seeder
{
    public static function seedForTenant($tenantId)
    {
        // Check if voucher types already exist for this tenant
        $existingTypes = VoucherType::where('tenant_id', $tenantId)->count();
        if ($existingTypes > 0) {
            return; // Skip seeding if types already exist
        }

        $defaultVoucherTypes = [
            [
                'name' => 'Journal',
                'code' => 'JV',
                'abbreviation' => 'J',
                'description' => 'General journal entries for adjustments and corrections',
                'numbering_method' => 'auto',
                'prefix' => 'JV-',
                'starting_number' => 1,
                'current_number' => 0,
                'has_reference' => false,
                'affects_inventory' => false,
                'affects_cashbank' => false,
                'is_system_defined' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Payment',
                'code' => 'PV',
                'abbreviation' => 'P',
                'description' => 'Payment vouchers for cash and bank payments',
                'numbering_method' => 'auto',
                'prefix' => 'PV-',
                'starting_number' => 1,
                'current_number' => 0,
                'has_reference' => true,
                'affects_inventory' => false,
                'affects_cashbank' => true,
                'is_system_defined' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Receipt',
                'code' => 'RV',
                'abbreviation' => 'R',
                'description' => 'Receipt vouchers for cash and bank receipts',
                'numbering_method' => 'auto',
                'prefix' => 'RV-',
                'starting_number' => 1,
                'current_number' => 0,
                'has_reference' => true,
                'affects_inventory' => false,
                'affects_cashbank' => true,
                'is_system_defined' => true,
                'is_active' => true,
            ],
              [
                'name' => 'Sales',
                'code' => 'SV',
                'abbreviation' => 'S',
                'description' => 'Sales vouchers for recording sales transactions',
                'numbering_method' => 'auto',
                'prefix' => 'SV-',
                'starting_number' => 1,
                'current_number' => 0,
                'has_reference' => true,
                'affects_inventory' => true,
                'affects_cashbank' => false,
                'is_system_defined' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Purchase',
                'code' => 'PUR',
                'abbreviation' => 'PU',
                'description' => 'Purchase vouchers for recording purchase transactions',
                'numbering_method' => 'auto',
                'prefix' => 'PUR-',
                'starting_number' => 1,
                'current_number' => 0,
                'has_reference' => true,
                'affects_inventory' => true,
                'affects_cashbank' => false,
                'is_system_defined' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Contra',
                'code' => 'CV',
                'abbreviation' => 'C',
                'description' => 'Contra vouchers for bank to cash or cash to bank transfers',
                'numbering_method' => 'auto',
                'prefix' => 'CV-',
                'starting_number' => 1,
                'current_number' => 0,
                'has_reference' => true,
                'affects_inventory' => false,
                'affects_cashbank' => true,
                'is_system_defined' => true,
                'is_active' => true,
            ],
        ];

        foreach ($defaultVoucherTypes as $voucherType) {
            $voucherType['tenant_id'] = $tenantId;
            $voucherType['created_at'] = now();
            $voucherType['updated_at'] = now();

            VoucherType::create($voucherType);
        }
    }

    public function run()
    {
        // This method can be used for standalone seeding if needed
        $tenantId = $this->command->option('tenant-id');

        if ($tenantId) {
            self::seedForTenant($tenantId);
            $this->command->info("Voucher types seeded for tenant ID: {$tenantId}");
        } else {
            $this->command->error('Please provide --tenant-id option');
        }
    }
}