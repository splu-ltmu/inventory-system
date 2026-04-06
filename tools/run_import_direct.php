<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Category;
use App\Models\Stock;
use App\Models\Inbound;

$path = storage_path('app/test-inbound.xlsx');
if (!file_exists($path)) {
    echo "Test file not found: $path\n";
    exit(1);
}

$spreadsheet = IOFactory::load($path);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, true, true);

$imported = 0;
$createdStocks = 0;
$errors = [];

foreach ($rows as $index => $row) {
    if ($index === 1) continue; // header

    $description = trim((string) ($row['A'] ?? ''));
    $quantityRaw = trim((string) ($row['B'] ?? ''));
    $quantityClean = str_replace([',', ' ', "\xc2\xa0"], ['', '', ''], $quantityRaw);
    $categoryName = trim((string) ($row['C'] ?? ''));

    if ($description === '' && $quantityClean === '' && $categoryName === '') continue;
    if ($quantityClean === '' || !is_numeric($quantityClean) || (int)$quantityClean <= 0) {
        $errors[] = "Row $index: invalid quantity '" . ($row['B'] ?? '') . "'";
        continue;
    }
    $quantity = (int)$quantityClean;

    // Resolve category or default to Unknown
    $category = null;
    if ($categoryName) {
        $category = Category::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->first();
    }
    if (!$category) {
        $category = Category::firstOrCreate(['name' => 'Unknown'], ['code' => 'UK']);
        if (empty($category->code)) {
            if (!Category::where('code', 'UK')->exists()) {
                $category->code = 'UK';
                $category->save();
            }
        }
    }

    $stock = Stock::where('description', $description)->where('category_id', $category->id)->first();
    if (!$stock) {
        // generate id_no
        $lastStock = Stock::where('id_no', 'like', $category->code . '-%')->orderBy('id_no','desc')->first();
        if ($lastStock) {
            $lastNumber = (int) explode('-', $lastStock->id_no)[1];
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        $newId = $category->code . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $stock = Stock::create([
            'category_id' => $category->id,
            'id_no' => $newId,
            'description' => $description ?: 'Imported item',
            'unit' => 'pcs',
            'total' => 0,
            'stock' => 0,
            'hidden' => false,
        ]);
        $createdStocks++;
    }

    Inbound::create(['stock_id' => $stock->id, 'total' => $quantity]);
    $stock->total += $quantity;
    $stock->stock += $quantity;
    $stock->save();

    $imported++;
}

echo "Imported: $imported, Stocks created: $createdStocks" . (count($errors) ? ", Errors: " . implode('; ', $errors) : '') . "\n";
