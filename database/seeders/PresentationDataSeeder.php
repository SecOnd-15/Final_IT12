<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PresentationDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $adminId = DB::table('users')->where('username', 'admin')->value('id');
        $cashierId = DB::table('users')->where('role', 'Employee')->value('id') ?? $adminId;

        // Get products with prices
        $products = DB::table('products')
            ->join('product_prices', 'products.id', '=', 'product_prices.product_id')
            ->select('products.id', 'products.sku', 'products.name', 'product_prices.retail_price')
            ->get();

        if ($products->isEmpty()) {
            $this->command->error('No products found! Run ProductSeeder first.');
            return;
        }

        $customerNames = [
            'Juan Dela Cruz', 'Maria Santos', 'Pedro Reyes', 'Ana Lim', 'Robert Garcia',
            'Elizabeth Tan', 'Michael Sy', 'Susan Ong', 'James Chua', 'Jennifer Lee',
            'Contractor Pro Builders', 'Home Improvement Co.', 'Master Builders Inc.',
            'Lolo Fernando', 'Lola Remedios', 'Tatay Ben', 'Engineer Cruz',
        ];

        $customerContacts = [
            '09171234567', '09221234567', '09331234567', '09441234567', '09551234567',
            '09661234567', '09771234567', '09881234567', '09991234567',
        ];

        $paymentMethods = ['Cash', 'Cash', 'Cash', 'Cash', 'GCash', 'GCash', 'Card'];

        $this->command->info('Adding presentation sales data (last 7 days)...');

        $salesAdded = 0;

        // Generate sales for the last 7 days including today
        for ($daysAgo = 6; $daysAgo >= 0; $daysAgo--) {
            $date = $now->copy()->subDays($daysAgo);

            // Skip Sundays
            if ($date->dayOfWeek === Carbon::SUNDAY) continue;

            // 8–15 sales per day
            $salesCount = rand(8, 15);

            for ($s = 0; $s < $salesCount; $s++) {
                $saleDateTime = $date->copy()->hour(rand(8, 17))->minute(rand(0, 59))->second(rand(0, 59));

                // Don't create future sales
                if ($saleDateTime->isFuture()) continue;

                // Pick customer type: 70% Regular, 15% Senior, 15% PWD
                $roll = rand(1, 100);
                if ($roll <= 70) {
                    $customerType = 'Regular';
                    $pwdSeniorId = null;
                } elseif ($roll <= 85) {
                    $customerType = 'Senior Citizen';
                    $pwdSeniorId = 'SC-' . rand(100000, 999999);
                } else {
                    $customerType = 'PWD';
                    $pwdSeniorId = 'PWD-' . rand(100000, 999999);
                }

                $hasCustomerInfo = rand(1, 100) <= 75;
                $customerName = $hasCustomerInfo ? $customerNames[array_rand($customerNames)] : null;
                $customerContact = $hasCustomerInfo ? $customerContacts[array_rand($customerContacts)] : null;

                // Pick 1–6 items
                $itemsCount = rand(1, 6);
                $selectedProducts = $products->shuffle()->take($itemsCount);

                $rawTotal = 0;
                $itemRows = [];

                foreach ($selectedProducts as $product) {
                    $qty = $this->getQty($product->sku);
                    $unitPrice = $product->retail_price;
                    $lineTotal = $qty * $unitPrice;
                    $rawTotal += $lineTotal;

                    $itemRows[] = [
                        'product_id' => $product->id,
                        'quantity_sold' => $qty,
                        'unit_price' => $unitPrice,
                    ];
                }

                // Calculate financials
                if ($customerType === 'Regular') {
                    $subtotal = round($rawTotal / 1.12, 2);
                    $taxAmount = round($rawTotal - $subtotal, 2);
                    $discountAmount = 0;
                    $totalAmount = $rawTotal;
                    $taxPercentage = 12;
                } else {
                    // Senior/PWD: VAT Exempt + 20% Discount
                    $subtotal = round($rawTotal / 1.12, 2);
                    $taxAmount = 0;
                    $discountAmount = round($subtotal * 0.20, 2);
                    $totalAmount = round($subtotal - $discountAmount, 2);
                    $taxPercentage = 0;
                }

                // Insert sale
                $saleId = DB::table('sales')->insertGetId([
                    'user_id' => rand(1, 100) <= 60 ? $cashierId : $adminId,
                    'sale_date' => $saleDateTime,
                    'customer_name' => $customerName,
                    'customer_contact' => $customerContact,
                    'customer_type' => $customerType,
                    'pwd_senior_id' => $pwdSeniorId,
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'tax_percentage' => $taxPercentage,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $totalAmount,
                    'created_at' => $saleDateTime,
                    'updated_at' => $saleDateTime,
                ]);

                // Insert sale items
                foreach ($itemRows as $item) {
                    DB::table('sale_items')->insert([
                        'sale_id' => $saleId,
                        'product_id' => $item['product_id'],
                        'quantity_sold' => $item['quantity_sold'],
                        'unit_price' => $item['unit_price'],
                        'created_at' => $saleDateTime,
                        'updated_at' => $saleDateTime,
                    ]);
                }

                // Insert payment
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                $amountTendered = $paymentMethod === 'Cash'
                    ? ceil($totalAmount / 100) * 100
                    : $totalAmount;
                $changeGiven = $paymentMethod === 'Cash' ? round($amountTendered - $totalAmount, 2) : 0;

                DB::table('payments')->insert([
                    'sale_id' => $saleId,
                    'payment_date' => $saleDateTime,
                    'payment_method' => $paymentMethod,
                    'amount_tendered' => $amountTendered,
                    'change_given' => max(0, $changeGiven),
                    'reference_no' => $paymentMethod !== 'Cash' ? strtoupper($paymentMethod) . '-' . rand(100000, 999999) : null,
                    'created_at' => $saleDateTime,
                    'updated_at' => $saleDateTime,
                ]);

                $salesAdded++;
            }
        }

        $this->command->info("✅ Added {$salesAdded} presentation sales (last 7 days)");

        // Summary
        $regularCount = DB::table('sales')->where('customer_type', 'Regular')->count();
        $seniorCount = DB::table('sales')->where('customer_type', 'Senior Citizen')->count();
        $pwdCount = DB::table('sales')->where('customer_type', 'PWD')->count();

        $this->command->info("  Regular: {$regularCount} | Senior: {$seniorCount} | PWD: {$pwdCount}");
        $this->command->info("  Total Sales in DB: " . DB::table('sales')->count());
    }

    private function getQty(string $sku): int
    {
        $prefix = explode('-', $sku)[0];
        return match ($prefix) {
            'FSTNR' => rand(5, 50),
            'PWRTL' => 1,
            'HNDTL' => rand(1, 3),
            'LMBR'  => rand(1, 8),
            'PNT'   => rand(1, 4),
            'CHEM'  => rand(1, 3),
            'SAFE'  => rand(1, 3),
            default => rand(1, 5),
        };
    }
}
