<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = app(App\Http\Controllers\ReportController::class);

foreach (['sales', 'staff', 'purchase', 'expenses'] as $tab) {
    request()->merge(['tab' => $tab]);
    $response = $controller->exportExcel(request());
    echo "Tab $tab export status: " . $response->getStatusCode() . " - bytes: " . strlen($response->getContent()) . PHP_EOL;
}
