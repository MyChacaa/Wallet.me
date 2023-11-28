<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(file_exists("./install.php")) {
	header("Location: ./install.php");
} 
define('PWV1_INSTALLED',TRUE);
ob_start();
session_start();
include("configs/bootstrap.php");
include("includes/bootstrap.php");
include(getLanguage($settings['url'],null,null));


if(isset($_GET['ref'])){
$ref = $_GET['ref'];
$check = $db->query("SELECT * FROM users WHERE id=$ref");
$chk_ref = $check->fetch_assoc();

if($chk_ref['email_verified'] > 0){
setcookie("ref", $chk_ref['id']);
}else{
setcookie("ref", "0");
}
}
$bck_color = "8e0e3d";

if (isset($_GET['a'])){
$a = protect($_GET['a']);
} else {
$a = "";
}
switch($a) {
	case "account": include("sources/account.php"); break;
	case "login": include("sources/login.php"); break;
	case "password": include("sources/password.php"); break;
	case "email_verify": include("sources/email_verify.php"); break;
	case "merchant": include("sources/merchant.php"); break;
	case "payment": include("sources/payment.php"); break;
	case "link": include("sources/link.php"); break;
	case "deposit": include("sources/deposit.php"); break;
	case "page": include("sources/page.php"); break;
	case "logout": 
		unset($_SESSION['pw_uid']);
		unset($_COOKIE['prowall_uid']);
		setcookie("prowall_uid", "", time() - (86400 * 30), '/'); // 86400 = 1 day
		session_unset();
		session_destroy();
		header("Location: $settings[url]index.php");
	break;
	default: include("sources/home.php");
}
mysqli_close($db);
?>