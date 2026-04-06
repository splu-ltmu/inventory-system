<?php
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->fromArray(['Description','Quantity','Category'], null, 'A1');
$sheet->fromArray([['Bato',10,'Computer Supplies']], null, 'A2');
$writer = new Xlsx($spreadsheet);
$file = __DIR__ . '/../storage/app/test-inbound.xlsx';
if (!is_dir(dirname($file))) mkdir(dirname($file), 0777, true);
$writer->save($file);
echo "SAVED: $file\n";

$loaded = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
$arr = $loaded->getActiveSheet()->toArray(null, true, true, true);
print_r(array_slice($arr, 0, 4));
