<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$startDate = Carbon::now()->startOfMonth();
$endDate = Carbon::now()->endOfMonth();

$grossSales = DB::table('sale_items')
    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
    ->whereBetween('sales.sale_date', [$startDate, $endDate])
    ->sum(DB::raw('sale_items.unit_price * sale_items.quantity_sold'));

$returnsAmount = DB::table('product_returns')
    ->whereBetween('created_at', [$startDate, $endDate])
    ->sum('total_refund_amount');

echo "Gross Sales: " . $grossSales . "\n";
echo "Returns Amount: " . $returnsAmount . "\n";
echo "Net Revenue: " . ($grossSales - $returnsAmount) . "\n";
