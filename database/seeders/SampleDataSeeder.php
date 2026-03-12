<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $now        = Carbon::now();
        $adminId    = DB::table('users')->where('username', 'admin')->value('id');
        $products   = DB::table('products')->get()->keyBy('id');
        $supplierIds = DB::table('suppliers')->pluck('id')->toArray();

        $this->seedSuppliers($now);
        $this->seedUsers($now);
        $this->seedStockIns($now, $adminId, $supplierIds, $products);
        $this->seedProductPrices($now, $adminId, $products);
        $this->seedStockAdjustments($now, $adminId);
        $this->seedProductReturns($now, $adminId);

        $this->command->info('');
        $this->command->info('✅ SampleDataSeeder complete!');
        $this->command->info('   + 5 suppliers added');
        $this->command->info('   + 3 cashier users added');
        $this->command->info('   + 8 stock-in batches (all products stocked)');
        $this->command->info('   + 5 stock adjustments');
        $this->command->info('   + 5 product returns');
    }

    private function seedSuppliers(Carbon $now): void
    {
        $this->command->info('Seeding additional suppliers...');
        $newSuppliers = [
            [
                'supplier_name' => 'Manila Tools & Hardware Supply',
                'contactNO'     => '09171234001',
                'address'       => '14 Rizal Ave, Manila City',
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'supplier_name' => 'Visayas Industrial Supply Co.',
                'contactNO'     => '09281234002',
                'address'       => '88 Colon St, Cebu City',
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'supplier_name' => 'Mindanao Hardware Depot',
                'contactNO'     => '09391234003',
                'address'       => '22 Bolton St, Davao City',
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'supplier_name' => 'Philippine Builder\'s Supply Inc.',
                'contactNO'     => '09452341004',
                'address'       => '5 EDSA, Quezon City',
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'supplier_name' => 'National Hardware Distributors',
                'contactNO'     => '09563451005',
                'address'       => '101 Buendia Ave, Makati City',
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        foreach ($newSuppliers as $supplier) {
            $exists = DB::table('suppliers')->where('supplier_name', $supplier['supplier_name'])->exists();
            if (!$exists) {
                DB::table('suppliers')->insert($supplier);
            }
        }
    }

    private function seedUsers(Carbon $now): void
    {
        $this->command->info('Seeding additional users...');
        $newUsers = [
            [
                'username'          => 'maria.santos',
                'f_name'            => 'Maria',
                'm_name'            => 'Cruz',
                'l_name'            => 'Santos',
                'email'             => 'maria.santos@atinhardware.com',
                'role'              => 'cashier',
                'is_active'         => true,
                'password'          => Hash::make('cashier123'),
                'password_changed'  => true,
            ],
            [
                'username'          => 'jose.reyes',
                'f_name'            => 'Jose',
                'm_name'            => 'B.',
                'l_name'            => 'Reyes',
                'email'             => 'jose.reyes@atinhardware.com',
                'role'              => 'cashier',
                'is_active'         => true,
                'password'          => Hash::make('cashier123'),
                'password_changed'  => true,
            ],
            [
                'username'          => 'anna.lim',
                'f_name'            => 'Anna',
                'm_name'            => null,
                'l_name'            => 'Lim',
                'email'             => 'anna.lim@atinhardware.com',
                'role'              => 'cashier',
                'is_active'         => true,
                'password'          => Hash::make('cashier123'),
                'password_changed'  => true,
            ],
        ];

        foreach ($newUsers as $user) {
            $exists = DB::table('users')->where('username', $user['username'])->exists();
            if (!$exists) {
                DB::table('users')->insert(array_merge($user, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }

    private function seedStockIns(Carbon $now, int $adminId, array $supplierIds, $products): void
    {
        $this->command->info('Seeding stock-in records...');

        // Only seed stock-ins if none exist yet
        $existingStockIns = DB::table('stock_ins')->count();
        if ($existingStockIns === 0) {

            // Map: sku prefix => typical unit cost
            $costMap = [
                'FSTNR' => ['min' => 0.50,  'max' => 3.00,   'qty' => [500, 2000]],
                'HNDTL' => ['min' => 80.00, 'max' => 350.00, 'qty' => [10, 50]],
                'PWRTL' => ['min' => 800.00,'max' => 5000.00,'qty' => [3, 15]],
                'PLMB'  => ['min' => 15.00, 'max' => 250.00, 'qty' => [20, 100]],
                'ELEC'  => ['min' => 5.00,  'max' => 300.00, 'qty' => [30, 150]],
                'LMBR'  => ['min' => 120.00,'max' => 800.00, 'qty' => [10, 60]],
                'PNT'   => ['min' => 50.00, 'max' => 600.00, 'qty' => [10, 40]],
                'HARDW' => ['min' => 10.00, 'max' => 200.00, 'qty' => [20, 100]],
                'SAFE'  => ['min' => 30.00, 'max' => 500.00, 'qty' => [10, 50]],
                'CHEM'  => ['min' => 40.00, 'max' => 350.00, 'qty' => [15, 60]],
                'MISC'  => ['min' => 5.00,  'max' => 150.00, 'qty' => [20, 80]],
            ];

            // Batch 1: ~3 months ago (initial stock)
            // Batch 2: ~2 months ago
            // Batch 3: ~1 month ago
            $batches = [
                ['date' => $now->copy()->subMonths(3)->startOfDay()->addHours(9),  'ref' => 'SI-202312-001', 'supplier_idx' => 0],
                ['date' => $now->copy()->subMonths(3)->startOfDay()->addHours(14), 'ref' => 'SI-202312-002', 'supplier_idx' => 1],
                ['date' => $now->copy()->subMonths(2)->startOfDay()->addHours(10), 'ref' => 'SI-202401-001', 'supplier_idx' => 2],
                ['date' => $now->copy()->subMonths(2)->startOfDay()->addHours(15), 'ref' => 'SI-202401-002', 'supplier_idx' => 3],
                ['date' => $now->copy()->subMonths(1)->startOfDay()->addHours(9),  'ref' => 'SI-202402-001', 'supplier_idx' => 0],
                ['date' => $now->copy()->subMonths(1)->startOfDay()->addHours(13), 'ref' => 'SI-202402-002', 'supplier_idx' => 4],
                ['date' => $now->copy()->subDays(15)->addHours(10),               'ref' => 'SI-202403-001', 'supplier_idx' => 1],
                ['date' => $now->copy()->subDays(5)->addHours(9),                 'ref' => 'SI-202403-002', 'supplier_idx' => 2],
            ];

            $productList = $products->values()->toArray();

            foreach ($batches as $batch) {
                $supplierId = $supplierIds[$batch['supplier_idx']] ?? $supplierIds[0];

                // Pick a subset of products for this batch (roughly half)
                shuffle($productList);
                $batchProducts = array_slice($productList, 0, (int) ceil(count($productList) / 2));

                $subtotal       = 0;
                $itemsForBatch  = [];

                foreach ($batchProducts as $product) {
                    $prefix = explode('-', $product->sku)[0];
                    $costs  = $costMap[$prefix] ?? ['min' => 10, 'max' => 200, 'qty' => [10, 50]];

                    $qty      = rand($costs['qty'][0], $costs['qty'][1]);
                    $unitCost = round(mt_rand((int)($costs['min'] * 100), (int)($costs['max'] * 100)) / 100, 2);
                    $lineTotal = $qty * $unitCost;

                    $itemsForBatch[] = [
                        'product_id'       => $product->id,
                        'quantity_received' => $qty,
                        'actual_unit_cost'  => $unitCost,
                        'lineTotal'         => $lineTotal,
                    ];

                    $subtotal += $lineTotal;
                }

                $taxRate    = 12;
                $taxAmount  = round($subtotal * $taxRate / 100, 2);
                $discount   = 0;
                $totalCost  = round($subtotal + $taxAmount - $discount, 2);

                $stockInId = DB::table('stock_ins')->insertGetId([
                    'supplier_id'         => $supplierId,
                    'stock_in_date'       => $batch['date'],
                    'reference_no'        => $batch['ref'],
                    'received_by_user_id' => $adminId,
                    'subtotal'            => round($subtotal, 2),
                    'tax_amount'          => $taxAmount,
                    'discount_amount'     => $discount,
                    'total_cost'          => $totalCost,
                    'created_at'          => $batch['date'],
                    'updated_at'          => $batch['date'],
                ]);

                foreach ($itemsForBatch as $item) {
                    DB::table('stock_in_items')->insert([
                        'stock_in_id'       => $stockInId,
                        'product_id'        => $item['product_id'],
                        'quantity_received' => $item['quantity_received'],
                        'actual_unit_cost'  => $item['actual_unit_cost'],
                        'created_at'        => $batch['date'],
                        'updated_at'        => $batch['date'],
                    ]);

                    // Update product's stock and latest_unit_cost
                    DB::table('products')
                        ->where('id', $item['product_id'])
                        ->increment('quantity_in_stock', $item['quantity_received']);

                    DB::table('products')
                        ->where('id', $item['product_id'])
                        ->update(['latest_unit_cost' => $item['actual_unit_cost']]);
                }

                $this->command->info("  Created stock-in {$batch['ref']} ({$stockInId}) with " . count($itemsForBatch) . " items");
            }
        } else {
            $this->command->warn("  Skipping stock-ins — {$existingStockIns} records already exist.");
        }
    }

    private function seedProductPrices(Carbon $now, int $adminId, $products): void
    {
        $this->command->info('Ensuring product prices...');

        $retailMarkup = [
            'FSTNR' => 1.40,
            'HNDTL' => 1.35,
            'PWRTL' => 1.30,
            'PLMB'  => 1.40,
            'ELEC'  => 1.35,
            'LMBR'  => 1.30,
            'PNT'   => 1.35,
            'HARDW' => 1.40,
            'SAFE'  => 1.35,
            'CHEM'  => 1.30,
            'MISC'  => 1.40,
        ];

        foreach ($products as $product) {
            $hasPrice = DB::table('product_prices')->where('product_id', $product->id)->exists();
            if (!$hasPrice) {
                $prefix     = explode('-', $product->sku)[0];
                $markup     = $retailMarkup[$prefix] ?? 1.35;
                $unitCost   = $product->latest_unit_cost ?? 50.00;
                $retailPrice = round($unitCost * $markup, 2);

                DB::table('product_prices')->insert([
                    'product_id'         => $product->id,
                    'retail_price'       => $retailPrice,
                    'updated_by_user_id' => $adminId,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ]);
            }
        }
    }

    private function seedStockAdjustments(Carbon $now, int $adminId): void
    {
        $this->command->info('Seeding stock adjustments...');

        $existingAdjustments = DB::table('stock_adjustments')->count();

        if ($existingAdjustments === 0) {
            // Refresh products with updated stock
            $refreshedProducts = DB::table('products')->where('quantity_in_stock', '>', 0)->get()->values()->toArray();

            $adjustmentScenarios = [
                [
                    'date'    => $now->copy()->subMonths(2)->addDays(5)->addHours(17),
                    'type'    => 'Physical Count',
                    'notes'   => 'Monthly physical inventory count — minor discrepancies found.',
                    'changes' => [['delta' => -2], ['delta' => 3], ['delta' => -1]],
                    'status'  => 'Approved',
                ],
                [
                    'date'    => $now->copy()->subMonths(1)->addDays(10)->addHours(15),
                    'type'    => 'Damage/Scrap',
                    'notes'   => 'Water damage to storage area — affected items written off.',
                    'changes' => [['delta' => -5], ['delta' => -3]],
                    'status'  => 'Approved',
                ],
                [
                    'date'    => $now->copy()->subDays(20)->addHours(10),
                    'type'    => 'Found Stock',
                    'notes'   => 'Misplaced items found during warehouse reorganization.',
                    'changes' => [['delta' => 10], ['delta' => 6], ['delta' => 4]],
                    'status'  => 'Approved',
                ],
                [
                    'date'    => $now->copy()->subDays(10)->addHours(14),
                    'type'    => 'Internal Use',
                    'notes'   => 'Items used for store renovation and display fixtures.',
                    'changes' => [['delta' => -3], ['delta' => -2]],
                    'status'  => 'Pending',
                ],
                [
                    'date'    => $now->copy()->subDays(3)->addHours(9),
                    'type'    => 'Error Correction',
                    'notes'   => 'Correcting data entry error from previous stock-in.',
                    'changes' => [['delta' => -10], ['delta' => 10]],
                    'status'  => 'Pending',
                ],
            ];

            shuffle($refreshedProducts);

            $productPool = $refreshedProducts;
            $poolIndex   = 0;

            foreach ($adjustmentScenarios as $scenario) {
                $adjustmentId = DB::table('stock_adjustments')->insertGetId([
                    'adjustment_date'      => $scenario['date'],
                    'adjustment_type'      => $scenario['type'],
                    'reason_notes'         => $scenario['notes'],
                    'processed_by_user_id' => $adminId,
                    'approval_status'      => $scenario['status'],
                    'approved_by_user_id'  => $scenario['status'] === 'Approved' ? $adminId : null,
                    'approved_at'          => $scenario['status'] === 'Approved' ? $scenario['date']->copy()->addHours(2) : null,
                    'created_at'           => $scenario['date'],
                    'updated_at'           => $scenario['date'],
                ]);

                foreach ($scenario['changes'] as $change) {
                    if ($poolIndex >= count($productPool)) {
                        $poolIndex = 0;
                    }
                    $product    = $productPool[$poolIndex++];
                    $beforeQty  = $product->quantity_in_stock;
                    $delta      = $change['delta'];
                    $afterQty   = max(0, $beforeQty + $delta);
                    $unitCost   = $product->latest_unit_cost ?? 50.00;

                    DB::table('stock_adjustment_items')->insert([
                        'stock_adjustment_id'    => $adjustmentId,
                        'product_id'             => $product->id,
                        'before_quantity'         => $beforeQty,
                        'adjusted_quantity'       => $delta,
                        'after_quantity'          => $afterQty,
                        'unit_cost_at_adjustment' => $unitCost,
                        'created_at'             => $scenario['date'],
                        'updated_at'             => $scenario['date'],
                    ]);

                    // Apply the stock change
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['quantity_in_stock' => $afterQty]);

                    // Also update the product's quantity_in_stock in our in-memory pool for later
                    $product->quantity_in_stock = $afterQty;
                }

                $this->command->info("  Created adjustment [{$scenario['type']}] ID={$adjustmentId} ({$scenario['status']})");
            }
        } else {
            $this->command->warn("  Skipping stock adjustments — {$existingAdjustments} records already exist.");
        }
    }

    private function seedProductReturns(Carbon $now, int $adminId): void
    {
        $this->command->info('Seeding product returns...');

        $existingReturns = DB::table('product_returns')->count();

        if ($existingReturns === 0) {
            // Pick real sale_items (with their IDs) from distinct sales
            $saleItems = DB::table('sale_items')
                ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->select(
                    'sale_items.id as sale_item_id',
                    'sales.id as sale_id',
                    'sale_items.product_id',
                    'sale_items.quantity_sold',
                    'sale_items.unit_price'
                )
                ->orderBy('sale_items.sale_id')
                ->limit(50)
                ->get();

            if ($saleItems->isNotEmpty()) {
                // Group by sale so each return is against one sale
                $bySale = $saleItems->groupBy('sale_id');
                $salesList = $bySale->keys()->take(5)->values();

                $returnScenarios = [
                    ['reason' => 'Defective',            'notes' => 'Item arrived damaged.',              'days_ago' => 25, 'qty_divisor' => 2],
                    ['reason' => 'Wrong Item',           'notes' => 'Wrong item was picked.',             'days_ago' => 18, 'qty_divisor' => 1],
                    ['reason' => 'Customer Change Mind', 'notes' => 'Customer no longer needs the item.', 'days_ago' => 12, 'qty_divisor' => 2],
                    ['reason' => 'Defective',            'notes' => 'Product stopped working.',           'days_ago' => 7,  'qty_divisor' => 1],
                    ['reason' => 'Other',                'notes' => 'Excess quantity, unused surplus.',   'days_ago' => 3,  'qty_divisor' => 2],
                ];

                foreach ($returnScenarios as $i => $scenario) {
                    if (!isset($salesList[$i])) break;

                    $saleId     = $salesList[$i];
                    $items      = $bySale->get($saleId)->take(3);
                    $returnDate = $now->copy()->subDays($scenario['days_ago'])->addHours(11);

                    // Calculate total refund amount
                    $totalRefund = 0;
                    $itemDetails = [];
                    foreach ($items as $item) {
                        $returnQty    = max(1, (int) floor($item->quantity_sold / $scenario['qty_divisor']));
                        $refundAmount = round($returnQty * $item->unit_price, 2);
                        $totalRefund += $refundAmount;
                        $itemDetails[] = [
                            'sale_item_id'         => $item->sale_item_id,
                            'product_id'           => $item->product_id,
                            'quantity_returned'    => $returnQty,
                            'unit_price'           => $item->unit_price,
                            'total_line_refunded'  => $refundAmount,
                        ];
                    }

                    $returnId = DB::table('product_returns')->insertGetId([
                        'sale_id'             => $saleId,
                        'user_id'             => $adminId,
                        'refund_payment_id'   => null,
                        'total_refund_amount' => round($totalRefund, 2),
                        'return_reason'       => $scenario['reason'],
                        'notes'               => $scenario['notes'],
                        'created_at'          => $returnDate,
                        'updated_at'          => $returnDate,
                    ]);

                    foreach ($itemDetails as $detail) {
                        DB::table('return_items')->insert([
                            'product_return_id'      => $returnId,
                            'product_id'             => $detail['product_id'],
                            'sale_item_id'           => $detail['sale_item_id'],
                            'quantity_returned'      => $detail['quantity_returned'],
                            'refunded_price_per_unit' => $detail['unit_price'],
                            'total_line_refunded'    => $detail['total_line_refunded'],
                            'inventory_adjusted'     => true,
                            'created_at'             => $returnDate,
                            'updated_at'             => $returnDate,
                        ]);

                        // Add returned stock back to product
                        DB::table('products')
                            ->where('id', $detail['product_id'])
                            ->increment('quantity_in_stock', $detail['quantity_returned']);
                    }

                    $this->command->info("  Created return ID={$returnId} for Sale #{$saleId} [{$scenario['reason']}]");
                }
            } else {
                $this->command->warn('  No sales found — skipping returns.');
            }
        } else {
            $this->command->warn("  Skipping returns — {$existingReturns} records already exist.");
        }
    }
}
