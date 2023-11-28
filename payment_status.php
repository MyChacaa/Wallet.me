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


$merchant_key = protect($_GET['merchant_key']);
$merchant_account = protect($_GET['merchant_account']);
$txid = protect($_GET['txid']);

$query = $db->query("SELECT * FROM payments WHERE txid='$txid' and merchant_account='$merchant_account'");
if($query->num_rows>0) {
	$row = $query->fetch_assoc();
	$merchant_id = PW_GetUserID($row['merchant_account']);
	if(idinfo($merchant_id,"merchant_api_key") == $merchant_key) { 
		if($row['payment_status'] == "4") {
			$data['status'] = 'success';
			$data['msg'] = 'Payment was successful.';
		} else {
			$data['status'] = 'error';
			$data['msg'] = 'Payment was failed.';
		}
	} else {
		$data['status'] = 'error';
		$data['msg'] = 'Merchant key is invalid.';
	}
} else {
	$data['status'] = 'error';
	$data['msg'] = 'Unauthorized payment.'; 
}
echo json_encode($data);
mysqli_close($db);
?>