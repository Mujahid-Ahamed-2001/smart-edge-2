<?php

require_once('../tcpdf/tcpdf.php');

session_start();
include "../Includes/config.php";
include "../Model/DB_Class.php";
$top_banner = __DIR__.'/../Assets/pdf/bg-top.png';
$bottom_banner = __DIR__.'/../Assets/pdf/bg-bottom.png';
$logo = __DIR__.'/../Assets/pdf/logo.png';


$quotation_no = $_GET['quotation_no'];
if (empty($quotation_no)) {

    if (isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=Quotation No is required");
    } else {
        header("Location: ../Public/index.php?error=Quotation No is required");
    }
    exit();
}

$dbObj = new DBTransactions();

// 🔥 FETCH DATA
$q = $dbObj->getData("SELECT q.*, c.CustName, c.CustContact,c.CustAddress 
FROM quotations q
LEFT JOIN customers c ON c.CTID = q.customer_id
WHERE q.quotation_no = '$quotation_no'");


$quotation = $q[0];
if (empty($quotation) || empty($q)) {

    if (isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=Quotation ID is required");
    } else {
        header("Location: ../Public/index.php?error=Quotation ID is required");
    }
    exit();
}
// $quotation_no = $quotation['quotation_no'];
$quotation_id = $quotation['id'];
$q_type = $quotation['q_type'];
if($q_type==1)
{
    $cover = __DIR__.'/../Assets/pdf/cover-image.png';
}
else
{
    $cover = __DIR__.'/../Assets/pdf/cover-image-web.png';
}
class MYPDF extends TCPDF {
    public function Header() {
        $html = '<table border="0" cellspacing="6" cellpadding="0" style="font-size: 0.9em; color: #666; line-height: 10px;">'
            . '<tr>'
                .'<td align="left">'
                    .'<img src="'.$GLOBALS['top_banner'].'" width="100" />'
                .'</td>'
                .'<td align="center" style=" width:150px;">'
                    . '<p style="line-height: 20px; font-size: 12px; color:#000;">'
                    .'Quotation No: <strong>'.$GLOBALS['quotation_no'].'</strong><br>'
                    . '</p>'
                .'</td>'
                .'<td align="right">'
                    .'<img src="'.$GLOBALS['logo'].'" width="100" />'
                .'</td>'
            . '</tr>'
            . '</table>';
        $this->writeHTMLCell($w = 0, $h = 15, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'M', $autopadding = FALSE);
    }
    public function Footer() {
        $this->SetY(-15);

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

// 🔥 CREATE PDF
$pdf = new MYPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Next Edge');
$pdf->SetMargins(0, 45, 0);
$pdf->AddPage();

// 🔥 IMAGE PATHS


// =======================
// 🔥 COVER PAGE
// =======================

// Background images

// Logo
$html ='';
$html .= '
<table border="0" cellspacing="0" cellpadding="0" style="width:100%;">
    <tr>
        <td align="center" valign="middle">
            <img src="'.$cover.'" width="400" />
        </td>
    </tr>

    <!-- 🔥 INVISIBLE SPACE -->
    <tr>
        <td height="180"></td>
    </tr>

    <tr>
        <td>
            <table border="0" cellspacing="0" cellpadding="5" style="width:100%;">
                <tr>
                    <td align="left">
                        <b style="font-size:12px;">Next Edge Solutions (Pvt) Ltd</b><br>
                        <span style="font-size:10px;">127, Wattalpola, Panadura</span><br>
                        <span style="font-size:10px;"><a href="tel:+94770206960" style="font-size:10px; color:#000; text-decoration: none;">+94-770-206-960</a> / <a href="tel:+94766033389" style="font-size:10px; color:#000; text-decoration: none;">+94-766-033-389</a></span><br>
                        <span style="font-size:10px;"><a href="mailto:sales@next-edge.lk" style="font-size:10px; color:#000; text-decoration: none;">Sales@Next-Edge.lk</a></span>
                    </td>

                    <td align="right">
                        <b style="font-size:12px;">Mr.'.$quotation["CustName"].'</b><br>
                        <span style="font-size:10px;">'.$quotation["CustAddress"].'</span><br>
                        <span style="font-size:10px;"><a href="tel:'.$quotation["CustContact"].'" style="font-size:10px; color:#000; text-decoration: none;">'.$quotation["CustContact"].'</a></span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>';
// Center image
$pdf->writeHTML($html, true, false, true, false, '');

// =======================
// 🔥 SECOND PAGE
// =======================
$pdf->AddPage();
$my_signature = '';
$signature_path = __DIR__.'/../Assets/pdf/my signature.jpeg';
if (file_exists($signature_path)) {
    $my_signature = '<img src="'.$signature_path.'" width="100" height="50" />';
}
$html ='';
$text = 'QUOTATION FOR THE SUPPLY OF POS HARDWARE';
if ($q_type==1)
{
    $text = 'QUOTATION FOR THE SUPPLY OF POS HARDWARE';
    $text2 = '<p style="line-height: 15px; padding:0 50px;">'
                .'We sincerely appreciate the opportunity extended to Next Edge Solutions to submit this proposal for your consideration regarding the implementation of POS hardware and software solutions for your organization'
                .'</p>'
                .'<p style="line-height: 15px; padding:0 50px;">'
                .'Next Edge Solutions is a trusted Sri Lankan technology partner specializing in advanced POS systems, integrated billing solutions, and custom business software including Smart Edge and Smart Chef. We deliver comprehensive, scalable, and performance-driven solutions designed to optimize operational efficiency across retail, restaurant, and service-oriented businesses'
                .'</p>'
                .'<p style="line-height: 15px; padding:0 50px;">'
                .'Our proposed solution has been carefully structured to enhance transaction accuracy, strengthen inventory control, improve financial reporting, and support data-driven decision-making. We are committed to empowering your organization with reliable technology that not only streamlines operations but also supports long-term growth and sustainability'
                .'</p>'
                .'<p style="line-height: 15px; padding:0 50px;">'
                .'We hereby affirm that all information and representations contained within this proposal are true, accurate, and complete to the best of our knowledge.'
                .'</p>'
                .'<p style="line-height: 15px; padding:0 50px;">'
                .'We trust that our proposal aligns with your operational requirements and strategic objectives. We look forward to your favorable consideration and assure you of our highest level of professionalism, ongoing support, and dedicated partnership at all times'
                .'</p>';
}
else
{
    $text = 'QUOTATION FOR Web Development Services';
    $text2 = '<p style="line-height: 15px; padding:0 50px;">'
                .'We sincerely appreciate the opportunity extended to Next Edge Solutions to submit this proposal for your consideration regarding the design, development, and implementation of a Website / Web Application for your organization.'
                .'</p>'
                .'<p style="line-height: 15px; padding:0 50px;">'
                .'Next Edge Solutions is a trusted Sri Lankan technology partner specializing in web development, custom web applications, digital transformation solutions, and enterprise software development. We deliver innovative, scalable, secure, and performance-driven digital solutions designed to enhance business operations, improve customer engagement, and support organizational growth across various industries.'
                .'</p>'
                .'<p style="line-height: 15px; padding:0 50px;">'
                .'Our proposed solution has been carefully structured to provide a modern, user-friendly, and responsive digital platform that strengthens your online presence, streamlines business processes, improves accessibility, and enables data-driven decision-making. By leveraging the latest web technologies and development best practices, we are committed to delivering a solution that meets your current requirements while remaining adaptable to future business needs.'
                .'</p>'
                .'<p style="line-height: 15px; padding:0 50px;">'
                .'We hereby affirm that all information and representations contained within this proposal are true, accurate, and complete to the best of our knowledge.'
                .'</p>'
                .'<p style="line-height: 15px; padding:0 50px;">'
                .'We trust that our proposal aligns with your business objectives and digital transformation goals. We look forward to your favorable consideration and assure you of our highest level of professionalism, ongoing technical support, and dedicated partnership throughout the project lifecycle and beyond.'
                .'</p>';
}
$html .= ''
    .'<table border="0" cellspacing="0" cellpadding="10" style="width:100%; font-size:10px;">'
        .'<tr>'
            .'<td align="left" style="padding-left:25px; padding-right:25px;">'
                .date('jS \of F Y', strtotime($quotation['created_at'])).''
            .'</td>'
        .'</tr>'
        .'<tr>'
            .'<td height="15">'
                .''
            .'</td>'
        .'</tr>'
        .'<tr>'
            .'<td align="left" style="padding-left:25px; padding-right:25px;">'
                .'Dear Mr/Mrs. '.$quotation["CustName"].',<br><br>'
            .'</td>'
        .'</tr>'
        .'<tr>'
            .'<td align="left" style="padding-left:25px; padding-right:25px;">'
                .'<h2 style="font-size:16px; color:#000; text-decoration:underline; text-align:center; line-height:40px;">'
                .$text
                .'</h2>'
            .'</td>'
        .'</tr>'
        .'<tr>'
            .'<td align="left" style="padding-left:25px; padding-right:25px;">'
                .$text2
            .'</td>'
        .'</tr>'
        .'<tr>'
            .'<td align="left">'
                .'<p>'
                .'yours sincerely,<br><br>'
                .'</p>'
            .'</td>'
        .'</tr>'
        .'<tr>'
            .'<td align="left" style="width:200px; ">'
                .'<div style="width:50px; border-bottom: 1px dashed #000; text-align:center;">'
                . $my_signature
                .'</div>'
                .'<p>'
                . 'Mujahid Ahamed<br>Director & Co-Founder<br><strong>Next Edge Solutions</strong>'
                .'</p>'
            .'</td>'
        .'</tr>'
    .'<table>'
    .''
    .'';
$pdf->writeHTML($html, true, false, true, false, '');
// =======================
// 🔥 THIRD PAGE
// =======================
$options_q = $dbObj->getData("SELECT * FROM quotation_options WHERE quotation_id = '$quotation_id'");
$html ='';
if(empty($options_q)) 
{
    $pdf->AddPage();
    $html .= '<p style="text-align:center; font-size:12px;">No options added to this quotation.</p>';
    $pdf->writeHTML($html, true, false, true, false, '');
} 
else 
{
    $first = true;
    foreach ($options_q as $opt) 
    {
        if (!$first) {
            $pdf->AddPage();
        }
        $first = false;
        $html = '';
        $html .= '<table border="0" cellspacing="0" cellpadding="5" style="width:100%; font-size:10px;">'
                .'<tr>'
                    .'<td align="left" style="padding-left:25px; padding-right:25px;">'
                        .'<h2 style="font-size:14px; color:#000;text-align:left; line-height:20px;font-weight:bold;">'
                        .'POS Hardware & Software Prroducts – '.$opt['option_name']
                        .'</h2>'
                    .'</td>'
                .'</tr>';
                $html .='<tr>'
                    .'<td align="left" style="padding-left:25px; padding-right:25px;">';
                    $html .='<table cellspacing="0" cellpadding="5" style="width:100%; font-size:10px; border-color:#ccc;">'
                            .'<tr>'
                                .'<th align="center" style="background-color:#58b0e0; border:1px solid #000; width:50px;">NO</th>'
                                .'<th align="center" style="background-color:#58b0e0; border:1px solid #000; width:250px;">Description</th>'
                                .'<th align="center" style="background-color:#58b0e0; border:1px solid #000; width:50px;">qty</th>'
                                .'<th align="center" style="background-color:#58b0e0; border:1px solid #000; width:75px;">Unit Price (LKR)</th>'
                                .'<th align="center" style="background-color:#58b0e0; border:1px solid #000; width:75px;">Disc Per Unit (LKR)</th>'
                                .'<th align="center" style="background-color:#58b0e0; border:1px solid #000; width:75px;">Total Price (LKR)</th>'
                            .'</tr>';
        $items_q = $dbObj->getData("SELECT * FROM quotation_option_items qoi LEFT JOIN products p ON p.PDID=qoi.item_id WHERE qoi.option_id = '".$opt['id']."'");
        $i = 1;
        foreach ($items_q as $item) {
                    $total = $item['original_price'] * $item['original_price'];
                    $html .='<tr>'
                            .'<td align="center" style="border-left: 1px solid #000; border-right: 1px solid #000;  width:50px;">'.$i++.'</td>'
                            .'<td style="border-left: 1px solid #000; border-right: 1px solid #000; width:250px;"><strong>'.$item['ItemName'].'</strong><br>'.$item['item_name'].'</td>'
                            .'<td style="border-left: 1px solid #000; border-right: 1px solid #000; width:50px;" align="center">'.$item['quantity'].'</td>'
                            .'<td style="border-left: 1px solid #000; border-right: 1px solid #000; width:75px;" align="right">'.number_format($item['original_price'], 2).'</td>'
                            .'<td style="border-left: 1px solid #000; border-right: 1px solid #000; width:75px;" align="right">'.number_format($item['discount_value'], 2).'</td>'
                            .'<td style="border-left: 1px solid #000; border-right: 1px solid #000; width:75px;" align="right">'.number_format($item['total'], 2).'</td>'
                        .'</tr>';
                    $html .='<tr>'
                            .'<td align="right" style="border-bottom: 1px solid #000;border-left: 1px solid #000; border-right: 1px solid #000;  width:50px;" align="right"></td>'
                            .'<td align="right" style="border-bottom: 1px solid #000;border-left: 1px solid #000; border-right: 1px solid #000; width:250px;" align="right"></td>'
                            .'<td align="right" style="border-bottom: 1px solid #000;border-left: 1px solid #000; border-right: 1px solid #000; width:50px;" align="right"></td>'
                            .'<td align="right" style="border-bottom: 1px solid #000;border-left: 1px solid #000; border-right: 1px solid #000; width:75px;" align="right"></td>'
                            .'<td align="right" style="border-bottom: 1px solid #000;border-left: 1px solid #000; border-right: 1px solid #000; width:75px;" align="right"></td>'
                            .'<td align="right" style="border-bottom: 1px solid #000;border-left: 1px solid #000; border-right: 1px solid #000; width:75px;" align="right"></td>'
                        .'</tr>';
        }
                    $html .='</table>';
                    $html .='</td>';
                $html .='</tr>';
        $html .='<tr>'
                .'<td align="right" style=" width:500px;">'
                    .'<h3 style="font-size:12px; color:#000; line-height:20px; font-weight:bold;">'
                    .'Subtotal Total: '
                    .'</h3>'
                .'</td>'
                .'<td align="right" style=" width:75px;">'
                    .'<span>'
                    .number_format($opt['subtotal'], 2).''
                    .'</span>'
                .'</td>';
        $html .='</tr>';
        if($opt['discount_amount'] > 0) 
        {
            $html .='<tr>'
                    .'<td align="right" style=" width:500px;">'
                        .'<h3 style="font-size:12px; color:#000; line-height:20px; font-weight:bold;">'
                        .'Total Discount: '
                        .'</h3>'
                    .'</td>'
                    .'<td align="right" style=" width:75px;">'
                        .'<span>'
                        .number_format($opt['discount_amount'], 2).''
                        .'</span>'
                    .'</td>';
            $html .='</tr>';    
        }
        
        $html .='<tr>'
                .'<td align="right" style=" width:500px;">'
                    .'<h3 style="font-size:12px; color:#000; line-height:20px; font-weight:bold;">'
                    .'Grand Total: '
                    .'</h3>'
                .'</td>'
                .'<td align="right" style=" width:75px;">'
                    .'<span>'
                    .number_format($opt['total'], 2).''
                    .'</span>'
                .'</td>';
        $html .='</tr>';
        $html .='</table>'
                .'';    
        $pdf->writeHTML($html, true, false, true, false, '');
    }
}
$pdf->AddPage();
$html = '';
if($q_type ==1)
{
    $terms_condition = '<table border="0" cellspacing="0" cellpadding="10" style="width:100%; font-size:10px;">'

        .'<tr><td style="padding-left:25px; padding-right:25px;"><h1 style="line-height: 15px; padding:0 50px; font-weight:bold; font-size:18px;">Terms and Conditions – Quotation</h1></td></tr>'

        .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">1. Validity of the Quotation</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">This quotation is valid for a period of Five 5 calendar days from the date of issuance.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">Next Edge Solutions reserves the right to revise pricing, availability, and specifications after the expiry of the stated validity period.</td></tr>'

        .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">2. Pricing</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">All prices quoted are in Sri Lankan Rupees unless otherwise stated. Prices are subject to change without prior notice after the validity period.</td></tr>'

        .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">3. Service Warranty and Frequency</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">3.1 Definition of Service Warranty</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">Service warranty refers to a contractual agreement provided for a specific duration to carry out repair or replacement of products due to operational or structural failure resulting from defects in materials or workmanship, subject to the terms and conditions stated herein.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">The warranty may cover repair or replacement only and does not include indemnification, incidental damage, or compensation unless explicitly stated.</td></tr>'

        .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">3.2 Warranty Coverage – Brand New Items</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">• Brand new hardware products supplied by Next Edge Solutions carry a One 1-year warranty unless otherwise specified.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">• For recognized brands such as HP, Dell, Lenovo, and Asus, spare parts warranty is valid for Three 3 years, subject to manufacturer policies and terms.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">• For Servers, a One 1-year service warranty is applicable.</td></tr>'

        .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">3.3 Warranty Coverage – Used / Refurbished Items</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">• Used or refurbished items carry a maximum warranty period of Three 3 months from the date of delivery, unless otherwise specified in writing.</td></tr>'

        .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">3.4 Free Service Frequency</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">During the applicable warranty period:</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">• Point of Sale Systems, Personal Computers, and Server Hardware Three 3 free services will be provided within the one-year service warranty period.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">• Electronic Cash Registers ECR Two 2 free services will be provided within the one-year service warranty period.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">Free services cover system checkups, performance inspection, and standard maintenance.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">Any replacement of parts outside manufacturer warranty will be charged separately.</td></tr>'

        .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">4. Warranty Exclusions</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">The warranty does not cover:</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">• Damages caused by misuse, negligence, accidents, liquid damage, fire, power surges, or unauthorized modifications</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">• Normal wear and tear</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">• External electrical fluctuations without proper stabilizers or UPS</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">• Third-party software issues not supplied by Next Edge Solutions</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">• Consumables such as printer heads, cables, adapters, paper rolls, batteries, and similar accessories unless explicitly covered</td></tr>'

        .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">5. Limitation of Liability</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">Next Edge Solutions shall not be liable for any indirect, incidental, or consequential damages, including loss of profit, business interruption, or data loss.</td></tr>'

        .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">6. Acceptance</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">Confirmation of this quotation through written approval, purchase order, advance payment, or issuance of LPO shall be deemed as acceptance of the above terms and conditions.</td></tr>'

    .'</table>';
}
else
{
    $terms_condition = '<table border="0" cellspacing="0" cellpadding="10" style="width:100%; font-size:10px;">'
                            .'<tr><td style="padding-left:25px; padding-right:25px;"><h1 style="line-height:15px; padding:0 50px; font-weight:bold; font-size:18px;">TERMS AND CONDITIONS</h1></td></tr>'

                            .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">1. Validity of the Quotation</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">This quotation is valid for a period of Five (5) calendar days from the date of issuance.</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">Next Edge Solutions reserves the right to revise pricing, project scope, timelines, and specifications after the expiry of the stated validity period.</td></tr>'

                            .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">2. Payment Terms</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">• A non-refundable advance payment of Fifty Percent (50%) of the total project value is required before commencement of the project.</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">• Thirty Percent (30%) of the project value shall be payable upon completion of the design and development phase or agreed project milestone.</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">• The remaining Twenty Percent (20%) shall be payable prior to final deployment, project handover, or go-live release.</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">• Delays in payment may result in suspension of development activities until outstanding amounts are settled.</td></tr>'

                            .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">3. Project Scope</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">This quotation covers only the features, modules, integrations, and deliverables specifically mentioned within the proposal.</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">Any additional functionality, modifications, enhancements, or integrations requested after project approval shall be treated as a change request and may incur additional charges and timeline adjustments.</td></tr>'

                            .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">4. Project Timeline</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">Project timelines are estimates based on the requirements available at the time of quotation.</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">Any delays in approvals, content submission, feedback, or provision of required information by the client may result in corresponding extensions to the project schedule.</td></tr>'

                            .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">5. Client Responsibilities</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">The client shall provide all necessary content, images, branding materials, access credentials, and approvals required for project execution.</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">The client shall designate an authorized representative to review and approve deliverables throughout the project lifecycle.</td></tr>'

                            .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">6. Design Approval and Revisions</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">The quotation includes up to Two (2) rounds of reasonable design and content revisions unless otherwise specified.</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">Additional revisions, redesign requests, or modifications beyond the approved scope may be charged separately.</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">Once a design or development phase has been approved, any subsequent changes may be treated as additional work.</td></tr>'

                            .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">7. Warranty and Support</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">Next Edge Solutions shall provide a Ninety (90) day warranty period from the date of final project handover.</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">During the warranty period, software defects, coding errors, and functionality issues directly related to the delivered solution will be rectified at no additional cost.</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">The warranty does not include new features, content updates, design changes, third-party service failures, hosting issues, or modifications made by parties other than Next Edge Solutions.</td></tr>'

                            .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">8. Hosting and Third-Party Services</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">Domain registration, web hosting, SSL certificates, payment gateways, SMS gateways, APIs, email services, and other third-party services are subject to the terms and conditions of their respective providers.</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">Unless explicitly included in this quotation, all subscription fees, license fees, renewal charges, and third-party service costs shall be borne by the client.</td></tr>'

                            .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">9. Intellectual Property Rights</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">Ownership of the completed website or web application shall transfer to the client only upon full settlement of all outstanding payments.</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">Next Edge Solutions retains ownership of proprietary frameworks, reusable code libraries, development methodologies, and third-party licensed components used during development.</td></tr>'

                            .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">10. Confidentiality</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">Both parties agree to maintain the confidentiality of all business, technical, financial, and operational information exchanged during the course of the project.</td></tr>'

                            .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">11. Limitation of Liability</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">Next Edge Solutions shall not be liable for any indirect, incidental, consequential, or special damages including loss of profits, loss of business opportunities, interruption of operations, or data loss arising from the use of the delivered solution.</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">The maximum liability of Next Edge Solutions shall not exceed the total amount paid by the client for the specific project.</td></tr>'

                            .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">12. Project Cancellation</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">If the client cancels the project after commencement, all payments made up to the cancellation date, including the 50% advance payment, shall be non-refundable.</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">The client shall be responsible for payment of all completed work and expenses incurred up to the date of cancellation.</td></tr>'

                            .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;">13. Acceptance</td></tr>'
                            .'<tr><td style="padding-left:25px; padding-right:25px;">Confirmation of this quotation through written approval, email confirmation, purchase order, LPO, signed agreement, or payment of the required 50% advance shall constitute acceptance of these terms and conditions.</td></tr>'
.'</table>';

}
$html .=''
    .$terms_condition
.'';
$pdf->writeHTML($html, true, false, true, false, '');
$hardware_terms = '<table border="0" cellspacing="0" cellpadding="10" style="width:100%; font-size:10px;">'

        .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold;"><h1 style="line-height: 15px; font-weight:bold; font-size:25px; text-align:left;">Hardware Terms and Conditions</h1></td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold; font-size:18px;">1. Hardware Services</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">1.1 The service agreement includes preventive maintenance for the hardware equipment covered under the contract.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">1.2 Preventive maintenance will be conducted at regular four-month intervals, totaling three 3 visits per year.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">1.3 Preventive maintenance services will be carried out at the customer’s site during normal working hours.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">1.4 Any hardware faults or service issues must be reported promptly to the Next Edge Solutions Service Center.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">1.5 Normal working hours are from 8:30 AM to 5:30 PM, Monday to Friday, excluding Mercantile Holidays.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">1.6 All maintenance services will be performed at the customer’s installed hardware location.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">1.7 While normal service operations are conducted during standard working hours, our Customer Service Unit operates from 8:30 AM to 8:00 PM, Monday to Sunday, for support coordination and assistance.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">1.8 Any additional hardware products procured from Next Edge Solutions will be covered under the same service conditions within the applicable warranty period at no additional service charge, unless otherwise stated.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">1.9 Service charges cover only the equipment specified in the quotation and are installed at the agreed POS hardware location.</td></tr>'

        .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold; font-size:18px;">2. Exclusions from Annual Maintenance Charges</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">The annual maintenance or service charges do not include the following:</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">2.1 Supply of consumable items or accessories including but not limited to paper rolls, printer heads, cables, adapters, batteries, and similar items.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">2.2 Replacement or repair of spare parts unless covered under manufacturer warranty.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">2.3 Electrical work external to the equipment or maintenance of devices not supplied by Next Edge Solutions.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">2.4 Maintenance services required due to:</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">• Negligence, misuse, or improper handling by the customer, its agents, servants, or employees</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">• Damage caused by fire, riots, sabotage, or any act of God</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">• Electrical fluctuations or voltage beyond specified operating limits</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">• Equipment transportation without supervision of Next Edge Solutions personnel</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">• Repairs, alterations, or modifications performed by persons other than authorized Next Edge Solutions personnel</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">• Addition, alteration, or modification of the existing equipment setup</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">2.5 In the event of non-availability of spare parts, a later release or upgraded model of the faulty component may be supplied. Any additional cost arising from such replacement shall be borne by the customer.</td></tr>'

        .'<tr><td style="padding-left:25px; padding-right:25px; font-weight:bold; font-size:18px;">3. Payment Terms</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">3.1 Hardware</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">100 percent payment is required upon confirmation of order.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">3.2 Software</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">50 percent payment is required upon confirmation of order.</td></tr>'
        .'<tr><td style="padding-left:25px; padding-right:25px;">The remaining 50 percent is payable upon completion and installation</td></tr>'

    .'</table>';
if($q_type ==1)
{
    $pdf->addPage();
    $html = '';
    $html .=''
            .''
            .''
            .''
            .''
            .'';
        $html .=''
        .$hardware_terms
    .'';
    $pdf->writeHTML($html, true, false, true, false, '');
}
else
{
    $hardware_terms ='';
}



// 🔥 OUTPUT
$pdf->SetTitle('Quotation-'.$quotation['quotation_no'].'_'.$quotation["CustName"].'_'.$quotation["CustAddress"]);
if(isset($_GET['whatsapp'])) 
{
    $filename = 'Quotation-'.$quotation['quotation_no'].'_'.$quotation["CustName"].'_'.$quotation["CustAddress"].'.pdf';
    $file_path = __DIR__ . '/../Attachment/' . $filename;

    $pdf->Output($file_path, 'F');
} else 
{
    $pdf->Output('Quotation-'.$quotation['quotation_no'].'_'.$quotation["CustName"].'_'.$quotation["CustAddress"].'.pdf', 'I');
}

