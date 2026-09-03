<?php

require_once('../tcpdf/tcpdf.php');

session_start();
include "../Includes/config.php";
include "../Model/DB_Class.php";
$top_banner = $_SERVER['DOCUMENT_ROOT'].'/smartEdge/Assets/pdf/bg-top.png';
$bottom_banner = $_SERVER['DOCUMENT_ROOT'].'/smartEdge/Assets/pdf/bg-bottom.png';
$logo = $_SERVER['DOCUMENT_ROOT'].'/smartEdge/Assets/pdf/logo.png';
$cover = $_SERVER['DOCUMENT_ROOT'].'/smartEdge/Assets/pdf/cover-image.jpg';
class MYPDF extends TCPDF {
    public function Header() {
        $html = '<table border="0" cellspacing="6" cellpadding="0" style="font-size: 0.9em; color: #666; line-height: 10px;">'
            . '<tr>'
                .'<td align="left">'
                    .'<img src="'.$GLOBALS['top_banner'].'" width="100" />'
                .'</td>'
                .'<td align="right">'
                    .'<img src="'.$GLOBALS['logo'].'" width="100" />'
                .'</td>'
            . '</tr>'
            . '</table>';
        $this->writeHTMLCell($w = 0, $h = 15, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'M', $autopadding = FALSE);
    }
    public function Footer() {
        $this->SetY(-40);

        $html = '
        <table border="0" cellspacing="6" cellpadding="0" style="font-size:10px;">
            <tr>
                <td align="center" style="font-size:9px;">
                    Page '.$this->getAliasNumPage().' / '.$this->getAliasNbPages().'
                </td>
            </tr>
        </table>';

        // 🔥 render HTML footer
        $this->writeHTMLCell(0, 15, 10, '', $html, 0, 1, 0, true, '', true);
    }
}

$quotation_id = $_GET['quotation_id'];

$dbObj = new DBTransactions();

// 🔥 FETCH DATA
$q = $dbObj->getData("SELECT q.*, c.CustName, c.CustContact 
FROM quotations q
LEFT JOIN customers c ON c.CTID = q.customer_id
WHERE q.id = '$quotation_id'");

$quotation = $q[0];

$options_q = $dbObj->getData("SELECT * FROM quotation_options WHERE quotation_id = '$quotation_id'");

// 🔥 CREATE PDF
$pdf = new MYPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Next Edge');
$pdf->SetMargins(0, 35, 0);
$pdf->AddPage();

// 🔥 IMAGE PATHS


// =======================
// 🔥 COVER PAGE
// =======================

// Background images

// Logo

// Center image
$pdf->Image($cover, 40, 60, 130);

// Title box
$pdf->SetTitle('Quotation - ' . $quotation['quotation_no']);
$pdf->SetXY(50, 80);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(255,255,255);
$pdf->MultiCell(110, 10, "QUOTATION FOR THE POINT-OF-SALE\nHARDWARE & SOFTWARE", 0, 'C', 1);

// Footer left
$pdf->SetXY(10, 250);
$pdf->SetFont('helvetica', '', 8);
$pdf->MultiCell(80, 5, "Next Edge Solutions (Pvt) Ltd\n127, Wattalpola, Panadura\n+94-770-206-960 / +94-766-033-389\nsales@next-edge.lk");

// Footer right
$pdf->SetXY(130, 250);
$pdf->MultiCell(60, 5, "To :- ".$quotation['CustName']."\n".$quotation['CustContact'], 0, 'R');

// =======================
// 🔥 SECOND PAGE
// =======================
$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'QUOTATION', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, 'Quotation No: '.$quotation['quotation_no'], 0, 1);
$pdf->Cell(0, 6, 'Date: '.date('d-m-Y', strtotime($quotation['created_at'])), 0, 1);

$pdf->Ln(5);

foreach ($options_q as $opt) {

    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(0, 6, $opt['option_name'], 0, 1);

    // Table header
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(10, 6, '#', 1);
    $pdf->Cell(80, 6, 'Description', 1);
    $pdf->Cell(20, 6, 'Qty', 1);
    $pdf->Cell(30, 6, 'Rate', 1);
    $pdf->Cell(30, 6, 'Total', 1);
    $pdf->Ln();

    $items_q = $dbObj->getData("SELECT * FROM quotation_option_items WHERE option_id = '".$opt['id']."'");

    $i = 1;

    $pdf->SetFont('helvetica', '', 9);

    foreach ($items_q as $item) {

        $pdf->Cell(10, 6, $i++, 1);
        $pdf->Cell(80, 6, $item['item_name'], 1);
        $pdf->Cell(20, 6, $item['quantity'], 1);
        $pdf->Cell(30, 6, number_format($item['original_price'],2), 1);
        $pdf->Cell(30, 6, number_format($item['total'],2), 1);
        $pdf->Ln();
    }

    // Totals
    $pdf->Ln(2);
    $pdf->Cell(140, 6, 'Subtotal', 0, 0, 'R');
    $pdf->Cell(30, 6, number_format($opt['subtotal'],2), 0, 1, 'R');

    $pdf->Cell(140, 6, 'Discount', 0, 0, 'R');
    $pdf->Cell(30, 6, number_format($opt['discount_amount'],2), 0, 1, 'R');

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(140, 6, 'Grand Total', 0, 0, 'R');
    $pdf->Cell(30, 6, number_format($opt['total'],2), 0, 1, 'R');

    $pdf->Ln(5);
}

// 🔥 OUTPUT
$pdf->Output('Quotation_'.$quotation['quotation_no'].'.pdf', 'I');