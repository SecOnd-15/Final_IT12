<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class MoreDataSeeder extends Seeder
{
    private array $costMap = [
        'FSTNR' => ['min' =>    0.50, 'max' =>    3.00, 'qty' => [200, 1500]],
        'HNDTL' => ['min' =>   80.00, 'max' =>  350.00, 'qty' => [5,  40]],
        'PWRTL' => ['min' =>  800.00, 'max' => 5000.00, 'qty' => [2,  10]],
        'PLMB'  => ['min' =>   15.00, 'max' =>  250.00, 'qty' => [20, 100]],
        'ELEC'  => ['min' =>    5.00, 'max' =>  300.00, 'qty' => [30, 150]],
        'LMBR'  => ['min' =>  120.00, 'max' =>  800.00, 'qty' => [10, 60]],
        'PNT'   => ['min' =>   50.00, 'max' =>  600.00, 'qty' => [10, 40]],
        'HARDW' => ['min' =>   10.00, 'max' =>  200.00, 'qty' => [20, 100]],
        'SAFE'  => ['min' =>   30.00, 'max' =>  500.00, 'qty' => [10, 50]],
        'CHEM'  => ['min' =>   40.00, 'max' =>  350.00, 'qty' => [15, 60]],
        'MISC'  => ['min' =>    5.00, 'max' =>  150.00, 'qty' => [20, 80]],
    ];

    public function run(): void
    {
        $now      = Carbon::now();
        $adminId  = DB::table('users')->where('username', 'admin')->value('id');
        $products = DB::table('products')->get();
        $supplierIds = DB::table('suppliers')->pluck('id')->toArray();

        // ─────────────────────────────────────────
        // 1. MORE STOCK-IN RECORDS  (target: +20)
        // ─────────────────────────────────────────
        $this->command->info('Adding more stock-in records...');

        $references = [
            'SI-202310-001','SI-202310-002','SI-202311-001','SI-202311-002',
            'SI-202311-003','SI-202312-003','SI-202312-004','SI-202401-003',
            'SI-202401-004','SI-202401-005','SI-202402-003','SI-202402-004',
            'SI-202402-005','SI-202403-003','SI-202403-004','SI-202403-005',
            'SI-202403-006','SI-202403-007','SI-202403-008','SI-202403-009',
        ];

        // Start from ~5 months ago, spread evenly up to present
        $startDate = $now->copy()->subMonths(5);
        $daysSpan  = $startDate->diffInDays($now);

        foreach ($references as $i => $ref) {
            // Skip if reference already exists
            if (DB::table('stock_ins')->where('reference_no', $ref)->exists()) {
                continue;
            }

            $daysOffset  = (int) round(($i / count($references)) * $daysSpan);
            $stockInDate = $startDate->copy()->addDays($daysOffset)->addHours(rand(8, 16));
            $supplierId  = $supplierIds[array_rand($supplierIds)];

            // Pick a random subset of products (10–20 products per batch)
            $productList = $products->shuffle()->take(rand(10, 20));

            $subtotal = 0;
            $items    = [];
            foreach ($productList as $product) {
                $prefix   = explode('-', $product->sku)[0];
                $costs    = $this->costMap[$prefix] ?? ['min' => 10, 'max' => 200, 'qty' => [10, 50]];
                $qty      = rand($costs['qty'][0], $costs['qty'][1]);
                $unitCost = round(mt_rand((int)($costs['min'] * 100), (int)($costs['max'] * 100)) / 100, 2);
                $line     = $qty * $unitCost;

                $items[]   = ['product' => $product, 'qty' => $qty, 'cost' => $unitCost, 'line' => $line];
                $subtotal += $line;
            }

            $taxRate    = rand(0, 1) ? 12 : 0;
            $taxAmount  = round($subtotal * $taxRate / 100, 2);
            $discount   = rand(0, 1) ? round(rand(50, 500), 2) : 0;
            $totalCost  = round($subtotal + $taxAmount - $discount, 2);

            $stockInId = DB::table('stock_ins')->insertGetId([
                'supplier_id'         => $supplierId,
                'stock_in_date'       => $stockInDate,
                'reference_no'        => $ref,
                'received_by_user_id' => $adminId,
                'subtotal'            => round($subtotal, 2),
                'tax_amount'          => $taxAmount,
                'discount_amount'     => $discount,
                'total_cost'          => $totalCost,
                'created_at'          => $stockInDate,
                'updated_at'          => $stockInDate,
            ]);

            foreach ($items as $item) {
                DB::table('stock_in_items')->insert([
                    'stock_in_id'        => $stockInId,
                    'product_id'         => $item['product']->id,
                    'quantity_received'  => $item['qty'],
                    'actual_unit_cost'   => $item['cost'],
                    'created_at'         => $stockInDate,
                    'updated_at'         => $stockInDate,
                ]);

                DB::table('products')
                    ->where('id', $item['product']->id)
                    ->increment('quantity_in_stock', $item['qty']);
            }
        }
        $this->command->info('  Done. Added up to ' . count($references) . ' stock-in batches.');

        // ─────────────────────────────────────────
        // 2. MORE STOCK ADJUSTMENTS  (target: +20)
        // ─────────────────────────────────────────
        $this->command->info('Adding more stock adjustments...');

        $adjustmentTypes = ['Physical Count', 'Damage/Scrap', 'Internal Use', 'Error Correction', 'Found Stock'];
        $adjustmentNotes = [
            'Physical Count'   => [
                'Monthly physical count — minor variance.',
                'Quarterly inventory audit, adjustments applied.',
                'Spot-check found discrepancy in fasteners bin.',
                'Year-end physical count reconciliation.',
                'Cycle count — aisle 3 verified.',
            ],
            'Damage/Scrap'     => [
                'Items damaged during transit from supplier.',
                'Water seepage in storage area, affected stock written off.',
                'Broken items discovered during shelf arrangement.',
                'Faulty batch of products returned to scrap.',
            ],
            'Internal Use'     => [
                'Materials used for store display fixtures.',
                'Items consumed for internal store repairs.',
                'Samples given to contractor clients for evaluation.',
            ],
            'Error Correction' => [
                'Data entry error corrected — quantity over-counted.',
                'Duplicate stock-in entry reversed.',
                'Correcting wrong product mapped during receiving.',
            ],
            'Found Stock'      => [
                'Misplaced items recovered from storage room.',
                'Found excess inventory in overflow shelf.',
                'Previously unaccounted items found during reorganization.',
            ],
        ];

        $approvers = DB::table('users')->where('role', 'Administrator')->pluck('id')->toArray();

        $startDate = $now->copy()->subMonths(5);

        for ($i = 0; $i < 20; $i++) {
            $adjType   = $adjustmentTypes[array_rand($adjustmentTypes)];
            $notes     = $adjustmentNotes[$adjType][array_rand($adjustmentNotes[$adjType])];
            $daysOff   = rand(0, (int)$startDate->diffInDays($now));
            $adjDate   = $startDate->copy()->addDays($daysOff)->addHours(rand(8, 17));
            $status    = $i < 14 ? 'Approved' : ($i < 18 ? 'Pending' : 'Rejected');
            $approverId = $status === 'Approved' ? ($approvers[array_rand($approvers)] ?? null) : null;
            $approvedAt = $status === 'Approved' ? $adjDate->copy()->addHours(rand(1, 4)) : null;

            $adjustmentId = DB::table('stock_adjustments')->insertGetId([
                'adjustment_date'      => $adjDate,
                'adjustment_type'      => $adjType,
                'reason_notes'         => $notes,
                'processed_by_user_id' => $adminId,
                'approval_status'      => $status,
                'approved_by_user_id'  => $approverId,
                'approved_at'          => $approvedAt,
                'created_at'           => $adjDate,
                'updated_at'           => $adjDate,
            ]);

            // 1–5 products per adjustment
            $productsSample = $products->shuffle()->take(rand(1, 5));
            foreach ($productsSample as $product) {
                $beforeQty = $product->quantity_in_stock;

                // Delta: mostly small negative (damage/count) or positive (found)
                if (in_array($adjType, ['Found Stock', 'Physical Count'])) {
                    $delta = rand(1, 20);
                } elseif ($adjType === 'Error Correction') {
                    $delta = rand(-15, 15);
                    if ($delta === 0) $delta = 1;
                } else {
                    $delta = -rand(1, 10);
                }

                $afterQty = max(0, $beforeQty + $delta);
                $unitCost = $product->latest_unit_cost ?? 50.00;

                DB::table('stock_adjustment_items')->insert([
                    'stock_adjustment_id'    => $adjustmentId,
                    'product_id'             => $product->id,
                    'before_quantity'        => $beforeQty,
                    'adjusted_quantity'      => $delta,
                    'after_quantity'         => $afterQty,
                    'unit_cost_at_adjustment'=> $unitCost,
                    'created_at'             => $adjDate,
                    'updated_at'             => $adjDate,
                ]);

                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['quantity_in_stock' => $afterQty]);

                // Update our in-memory product quantity for subsequent iterations
                $product->quantity_in_stock = $afterQty;
            }
        }
        $this->command->info('  Done. Added 20 stock adjustments.');

        // ─────────────────────────────────────────
        // 3. MORE PRODUCT RETURNS  (target: +20)
        // ─────────────────────────────────────────
        $this->command->info('Adding more product returns...');

        // Get sale_items grouped by sale (only sales with items)
        $saleItems = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->select(
                'sale_items.id as sale_item_id',
                'sales.id as sale_id',
                'sale_items.product_id',
                'sale_items.quantity_sold',
                'sale_items.unit_price'
            )
            ->orderByRaw('RAND()')
            ->limit(200)
            ->get();

        $bySale     = $saleItems->groupBy('sale_id');
        $salesList  = $bySale->keys()->values();

        $returnReasons = ['Defective', 'Wrong Item', 'Customer Change Mind', 'Other'];
        $returnNotes   = [
            'Defective'            => ['Item arrived with manufacturing defect.', 'Product stopped working after one use.', 'Cracked casing on arrival.'],
            'Wrong Item'           => ['Cashier scanned wrong barcode.', 'Customer received different model than ordered.', 'Mix-up during packing.'],
            'Customer Change Mind' => ['Customer no longer needs the item.', 'Project was cancelled.', 'Bought duplicate by mistake.'],
            'Other'                => ['Excess quantity ordered.', 'Item did not fit requirements.', 'Returned as part of bulk exchange.'],
        ];

        $usedSaleIds = DB::table('product_returns')->pluck('sale_id')->toArray();
        $availableSales = $salesList->filter(fn($id) => !in_array($id, $usedSaleIds))->values();

        $added = 0;
        foreach ($availableSales->take(20) as $saleId) {
            $items      = $bySale->get($saleId)->take(rand(1, 3));
            $reason     = $returnReasons[array_rand($returnReasons)];
            $note       = $returnNotes[$reason][array_rand($returnNotes[$reason])];
            $daysAgo    = rand(1, 90);
            $returnDate = $now->copy()->subDays($daysAgo)->addHours(rand(9, 16));

            $totalRefund = 0;
            $itemDetails = [];
            foreach ($items as $item) {
                $returnQty    = max(1, (int) ceil($item->quantity_sold * (rand(3, 10) / 10)));
                $returnQty    = min($returnQty, $item->quantity_sold);
                $refundAmount = round($returnQty * $item->unit_price, 2);
                $totalRefund += $refundAmount;
                $itemDetails[] = [
                    'sale_item_id'  => $item->sale_item_id,
                    'product_id'    => $item->product_id,
                    'qty'           => $returnQty,
                    'unit_price'    => $item->unit_price,
                    'line_refunded' => $refundAmount,
                ];
            }

            $returnId = DB::table('product_returns')->insertGetId([
                'sale_id'             => $saleId,
                'user_id'             => $adminId,
                'refund_payment_id'   => null,
                'total_refund_amount' => round($totalRefund, 2),
                'return_reason'       => $reason,
                'notes'               => $note,
                'created_at'          => $returnDate,
                'updated_at'          => $returnDate,
            ]);

            foreach ($itemDetails as $detail) {
                DB::table('return_items')->insert([
                    'product_return_id'       => $returnId,
                    'product_id'              => $detail['product_id'],
                    'sale_item_id'            => $detail['sale_item_id'],
                    'quantity_returned'       => $detail['qty'],
                    'refunded_price_per_unit' => $detail['unit_price'],
                    'total_line_refunded'     => $detail['line_refunded'],
                    'inventory_adjusted'      => true,
                    'created_at'              => $returnDate,
                    'updated_at'              => $returnDate,
                ]);

                DB::table('products')
                    ->where('id', $detail['product_id'])
                    ->increment('quantity_in_stock', $detail['qty']);
            }

            $added++;
        }
        $this->command->info("  Done. Added {$added} product returns.");

        // ─────────────────────────────────────────
        // SUMMARY
        // ─────────────────────────────────────────
        $this->command->info('');
        $this->command->info('✅ MoreDataSeeder complete!');
        $this->command->info('  stock_ins:             ' . DB::table('stock_ins')->count());
        $this->command->info('  stock_in_items:        ' . DB::table('stock_in_items')->count());
        $this->command->info('  stock_adjustments:     ' . DB::table('stock_adjustments')->count());
        $this->command->info('  stock_adjustment_items:' . DB::table('stock_adjustment_items')->count());
        $this->command->info('  product_returns:       ' . DB::table('product_returns')->count());
        $this->command->info('  return_items:          ' . DB::table('return_items')->count());
    }
}
