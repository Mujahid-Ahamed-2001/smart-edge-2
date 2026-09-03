<?php 
session_start();

if(isset($_SESSION["shop_id"]))
{
    
    // setcookie("remember_me_synnex_shop", "", time() - 3600, "/");
    unset($_SESSION["shop_id"]);
}

header("Location: ../Public/dashboard.php")

?>