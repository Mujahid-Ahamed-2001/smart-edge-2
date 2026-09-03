<?php
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";


$phone_number_id = "YOUR_PHONE_NUMBER_ID";
$access_token = "YOUR_ACCESS_TOKEN";

$to = "9477XXXXXXX";

$pdf_url = "https://yourdomain.com/temp/Quotation-Q17042026-007.pdf";

$url = "https://graph.facebook.com/v18.0/$phone_number_id/messages";

$data = [
    "messaging_product" => "whatsapp",
    "to" => $to,
    "type" => "document",
    "document" => [
        "link" => $pdf_url,
        "filename" => "Quotation.pdf"
    ]
];

$options = [
    "http" => [
        "header"  => "Content-type: application/json\r\nAuthorization: Bearer $access_token\r\n",
        "method"  => "POST",
        "content" => json_encode($data),
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo $result;