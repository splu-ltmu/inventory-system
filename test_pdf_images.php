<?php
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;

// Test different image approaches
$pdf = new Dompdf();
$pdf->set_option('isRemoteEnabled', true);
$pdf->set_option('isHtml5ParserEnabled', true);
$pdf->set_option('chroot', __DIR__);

$html = '<html><head><style>body{font-family:Arial;} img{width:60px;height:auto;}</style></head><body>';
$html .= '<h1>PDF Image Test</h1>';

// Method 1: Base64
$bagongPath = __DIR__ . '/public/images/Bagong-Pilipinas.png';
if (file_exists($bagongPath)) {
    $bagongBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($bagongPath));
    $html .= '<h2>Method 1: Base64</h2><img src="' . $bagongBase64 . '" alt="Bagong Pilipinas">';
}

// Method 2: File path
$bagongFile = str_replace('\\', '/', $bagongPath);
$html .= '<h2>Method 2: File Path</h2><img src="file:///' . $bagongFile . '" alt="Bagong Pilipinas">';

// Method 3: Relative path
$html .= '<h2>Method 3: Relative Path</h2><img src="public/images/Bagong-Pilipinas.png" alt="Bagong Pilipinas">';

$html .= '</body></html>';

$pdf->loadHtml($html);
$pdf->setPaper('a4', 'portrait');
$pdf->render();

file_put_contents('test_images.pdf', $pdf->output());
echo "PDF generated: test_images.pdf\n";
?>
