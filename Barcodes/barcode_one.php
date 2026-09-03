<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../Includes/includes.php";
require '../vendor/autoload.php';

session_start();

use Dompdf\Dompdf;
use Dompdf\Options;
use Picqer\Barcode\BarcodeGeneratorPNG;

$shop_id = $_SESSION['shop_id'] ?? null;
if (!$shop_id) {
    die("Session expired or invalid shop ID.");
}

if (!isset($_GET['label'])) {
    header("Location: ../Public/product.php");
    exit;
}

$label = $_GET['label'];
$lbl_data = explode("_", $label);
$product_id = $lbl_data[0];
$product_price = $lbl_data[1];

// === Database Section === //
$dbObj = new DBTransactions();

// Product data
$sql_1 = "SELECT * FROM `pricehistory`
          INNER JOIN products ON products.PDID = pricehistory.ProductID
          WHERE ProductID = " . intval($product_id);
$prodData = $dbObj->getData($sql_1);

if (empty($prodData)) {
    die("Product not found.");
}

$barcode = $prodData[0]['Barcode'];
$item_name = $prodData[0]['ItemName'];

// === Label configuration === //
$dpi = 300; // Recommended for printing

// Set label/sticker size in mm
$lbl_width_mm = 30;
$lbl_height_mm = 20;

// Sticker area (single label)
$stk_width_mm = 30;
$stk_height_mm = 20;

// Margins (adjust if needed)
$stk_margin_left_mm = 0;
$stk_margin_right_mm = 0;
$stk_margin_top_mm = 0;
$stk_margin_bottom_mm = 0;

// Convert mm to pixels for layout
function toPixel($mm, $dpi) {
    return ($dpi / 25.4) * $mm;
}

$stk_width = toPixel($stk_width_mm, $dpi);
$stk_height = toPixel($stk_height_mm, $dpi);
$stk_margin_left = toPixel($stk_margin_left_mm, $dpi);
$stk_margin_right = toPixel($stk_margin_right_mm, $dpi);
$stk_margin_top = toPixel($stk_margin_top_mm, $dpi);
$stk_margin_bottom = toPixel($stk_margin_bottom_mm, $dpi);

// === Paper size in points (1 inch = 25.4 mm, 1 inch = 72 points) === //
$width_points = ($lbl_width_mm / 25.4) * 72;
$height_points = ($lbl_height_mm / 25.4) * 72;

// === Generate barcode === //
$generator = new BarcodeGeneratorPNG();
$barcode_image = "data:image/png;base64," . base64_encode(
    $generator->getBarcode($barcode, $generator::TYPE_CODE_128)
);

// === Build HTML === //
$html = "
<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<title>Barcode Label</title>
<style>
    @page { margin: 0; }
    body { margin: 0; padding: 0; }

    * {
        font-family: Arial, Helvetica, sans-serif;
        box-sizing: border-box;
    }

    .label {
        width: {$stk_width}px;
        max-width: {$stk_width}px;
        height: {$stk_height}px;
        max-height: {$stk_height}px;
        margin: {$stk_margin_top}px {$stk_margin_right}px {$stk_margin_bottom}px {$stk_margin_left}px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .barcode {
        width: 80%;
        height: 50%;
        margin-top: 2px;
    }

    .barcode-text {
        font-size: 25px;
        margin: 0;
    }
    .mt-5{
        margin-top:10px;    
    }
    .name {
        font-size: 25px;
        line-height: 25px;
        font-weight: bold;
    }
    .price {
        font-size: 20px;
        margin: 0;
        font-weight: bold;
    }
</style>
</head>
<body>
    <div class='label'>
        <p class='name mt-5'>Next Edge Solutions</p>
        <img src='{$barcode_image}' class='barcode'>
        <p class='barcode-text'>{$barcode}</p>
        <p class='price'>Rs: {$product_price}</p>
    </div>
</body>
</html>
";

// === Generate PDF === //
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('dpi', $dpi);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper([0, 0, $width_points, $height_points], 'portrait');
$dompdf->render();

// Stream to browser
$dompdf->stream("Barcode_Label", ["Attachment" => false]);
?>
