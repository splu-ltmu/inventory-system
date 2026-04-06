<?php
// Bootstrap Laravel and call Admin\InboundController::import with a test XLSX file
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\InboundController;

$path = storage_path('app/test-inbound.xlsx');
if (!file_exists($path)) {
    echo "Test file not found: $path\n";
    exit(1);
}

// Create a Symfony UploadedFile (the last parameter true marks it as test)
$sym = new Symfony\Component\HttpFoundation\File\UploadedFile(
    $path,
    'test-inbound.xlsx',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    null,
    true
);
// Wrap into Laravel's UploadedFile so validation 'file' passes
$file = Illuminate\Http\UploadedFile::createFromBase($sym);

$request = Request::create('/admin/inbound/import', 'POST');
$request->files->set('file', $file);

$controller = new InboundController();
$response = $controller->import($request);

if ($response instanceof Illuminate\Http\RedirectResponse) {
    echo "Redirected to: " . $response->getTargetUrl() . "\n";
    $session = session()->all();
    print_r($session);
} else {
    var_dump($response);
}
