<?php 
include "../../Includes/config.php";
include "../../Model/transfer_class.php";

$transfer_detail_id = $_GET['transfer_detail_id'];

$tranObj = new Transfer();
$tranObj->deleteTransferDetail($transfer_detail_id);