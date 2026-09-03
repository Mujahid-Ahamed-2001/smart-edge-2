<?php 
// define('ENVIRONMENT', 'p/roduction');
define('ENVIRONMENT', 'live');

if (ENVIRONMENT === 'live') {
    header("Location: ./Public/login.php");
    exit;
} else {
    header("Location: ./install/");
    exit;
}
?>
