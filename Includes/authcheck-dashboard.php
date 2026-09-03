<?php  
// session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$designation = 0;
$shop_id = 0;
$user_name = "";

if(isset($_SESSION['user_id']))
{
    $user_id = $_SESSION['user_id'];
    $userObj = new User();
    $user = $userObj->getOneUser($user_id);
    $user_name = $user[0]['UserName'];
}//user logged in
else
{
    header("Location: ../Public/login.php");
}//force to log in

// if(isset($_SESSION['shop_id']))
// {
//     $shop_id = $_SESSION['shop_id'];
// }//if shop set
// else
// {   
//     header("Location: ../Public/dashboard.php");
// }//else goto dashboard

?>