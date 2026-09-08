<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$title = isset($title) ? $title : "Smart Edge | Powered By Next Edge";
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $title ?></title>
<link rel="shortcut icon" type="image/png" href="../Assets/Images/SystemLogo/favicon.png" />
<link rel="stylesheet" href="../Assets/css/styles.min.css" />

<script src="../JQuery_361.js"></script>

<script src="../Assets/jquery/jquery.min.js"></script>
<!-- Toastr CSS -->
<link rel="stylesheet" href="../Assets/css/toastr.min.css">
<!-- jQuery (Required for Toastr) -->
<script src="../Assets/js/jquery-3.6.0.min.js"></script>
<!-- Toastr JS -->
<script src="../Assets/jquery/toastr.min.js"></script>


<script src="../Assets/ckeditor/ckeditor.js"></script>

<link rel="stylesheet" href="../Assets/css/styles.css">
<link rel="stylesheet" href="../Assets/css/header.css">
<link rel="stylesheet" href="../Assets/css/head.css">

<link rel="stylesheet" href="../vendor/select2-develop/dist/css/select2.min.css">
<script src="../vendor/select2-develop/dist/js/select2.min.js"></script>

<link rel="stylesheet" href="../Assets/css/datatables.min.css">
<script src="../Assets/js/datatables.min.js"></script>
<?php 
$app = isset($_GET["app_status"]) ? 1 : 0;
?>

<link rel="stylesheet" href="../Assets/izimodal/iziModal.min.css">