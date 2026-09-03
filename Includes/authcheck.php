<?php  
error_reporting(E_ALL);
error_reporting(-1);
ini_set('error_reporting', E_ALL);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../Includes/includes.php';

// =======================
// USER CHECK
// =======================

$user_id = isset($_SESSION['user_id']) 
    ? $_SESSION['user_id'] 
    : (isset($_COOKIE['remember_me_Smart_edge']) ? $_COOKIE['remember_me_Smart_edge'] : '');

if(empty($user_id)){
    header("Location: ../Public/logout.php");
    exit;
}

$_SESSION['user_id'] = $user_id;

$userObj = new User();
$user = $userObj->getOneUser($user_id);

if(empty($user) || !isset($user[0])){
    header("Location: ../Public/logout.php");
    exit;
}

$userData = $user[0];

$user_name      = isset($userData['UserName']) ? $userData['UserName'] : '';
$UserProfile    = isset($userData['UserProfile']) ? $userData['UserProfile'] : '';
$UserContactNo  = isset($userData['ContactNo']) ? $userData['ContactNo'] : '';
$UserEmail      = isset($userData['UserEmail']) ? $userData['UserEmail'] : '';
$UserType      = isset($userData['UserType']) ? $userData['UserType'] : '';
if(!empty($UserType)){
    $_SESSION['UserType'] = $UserType;
}

if(empty($UserProfile) || !file_exists("../Assets/Images/user_profile/".$UserProfile)){
    $UserProfile = "avator.svg";
}


// =======================
// SHOP CHECK (FIXED)
// =======================

$shop_id = isset($_SESSION['shop_id']) 
    ? $_SESSION['shop_id'] 
    : (isset($_COOKIE['remember_me_Smart_edge_shop']) ? $_COOKIE['remember_me_Smart_edge_shop'] : '');

if(empty($shop_id)){
    header("Location: ../Public/dashboard.php");
    exit;
}

// ✅ VALIDATE shop_id from DB
$shopObj = new Shop();
$shopData = $shopObj->getOneShop($shop_id);

if(empty($shopData)){
    // invalid shop → clear and restart flow
    unset($_SESSION['shop_id']);
    setcookie("remember_me_Smart_edge_shop", "", time() - 3600, "/");

    header("Location: ../Public/dashboard.php");
    exit;
}

// ✅ VALID shop → set session
$_SESSION['shop_id'] = $shop_id;

?>