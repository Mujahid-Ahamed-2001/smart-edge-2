<?php 
session_start();
unset($_SESSION);
session_destroy(); 
setcookie("remember_me_Smart_edge_shop", "", time() - 3600, "/");
setcookie("remember_me_Smart_edge", "", time() - 3600, "/");
setcookie("remember_meS", "", time() - 3600, "/");
header("Location:./login.php");
?>