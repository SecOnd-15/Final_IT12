<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Carbon\Carbon;

$controller = new DashboardController();

// Use Reflection to test private methods
$reflection = new ReflectionClass(DashboardController::class);

$startDate = Carbon::now()->startOfMonth();
$endDate = Carbon::now()->endOfMonth();

$calculateNetRevenue = $reflection->getMethod('calculateNetRevenue');
$calculateNetRevenue->setAccessible(true);
$netRevenue = $calculateNetRevenue->invoke($controller, $startDate, $endDate);

$calculateGrossProfit = $reflection->getMethod('calculateGrossProfit');
$calculateGrossProfit->setAccessible(true);
$grossProfit = $calculateGrossProfit->invoke($controller, $startDate, $endDate);

echo "Net Revenue: " . $netRevenue . "\n";
echo "Gross Profit: " . $grossProfit . "\n";

if ($netRevenue < 0 || $grossProfit < 0) {
    echo "ERROR: Negative values found!\n";
    exit(1);
} else {
    echo "SUCCESS: Values are non-negative.\n";
}
