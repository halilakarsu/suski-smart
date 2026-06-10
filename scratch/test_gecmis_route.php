<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\KesinlesenFatura;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;

// Find a tesisat_no with multiple entries if possible
$sample = KesinlesenFatura::where('odeme_durumu', 'odendi')
    ->select('tesisat_no')
    ->groupBy('tesisat_no')
    ->havingRaw('COUNT(*) > 1')
    ->first();

if ($sample) {
    $tesisat_no = $sample->tesisat_no;
    $donem = KesinlesenFatura::where('tesisat_no', $tesisat_no)
        ->where('odeme_durumu', 'odendi')
        ->orderBy('donem', 'desc')
        ->value('donem');
} else {
    $sample = KesinlesenFatura::where('odeme_durumu', 'odendi')
        ->orderBy('donem', 'desc')
        ->first();
    $tesisat_no = $sample ? $sample->tesisat_no : null;
    $donem = $sample ? $sample->donem : null;
}

if (!$tesisat_no) {
    echo "No paid invoice records found in the database to test with.\n";
    exit(1);
}

echo "Found sample Tesisat No: $tesisat_no (Donem: $donem)\n";

// Instantiate the controller
$controller = new ReportController();
$request = Request::create('/raporlar/endeks/gecmis-6-ay/' . $tesisat_no, 'GET', ['donem' => $donem]);

$response = $controller->gecmis6Ay($tesisat_no, $request);

echo "Response Status Code: " . $response->getStatusCode() . "\n";
echo "Response Body:\n";
$data = json_decode($response->getContent(), true);
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

if ($data && isset($data['success']) && $data['success'] === true && isset($data['records'])) {
    echo "SUCCESS: History retrieved successfully with " . count($data['records']) . " records.\n";
} else {
    echo "FAILURE: History retrieval failed.\n";
    exit(1);
}
