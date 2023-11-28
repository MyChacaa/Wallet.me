<?php
// eWallet - PHP Script
// Author: DeluxeScript
define('PWV1_INSTALLED',TRUE);
ob_start();
session_start();
include("../configs/bootstrap.php");
include("../includes/bootstrap.php");

$merchant_key = protect($_GET['merchant_key']);
$merchant_account = protect($_GET['merchant_account']);

$id = PW_GetUserID($merchant_account);
if(idinfo($id,"merchant_api_key") == $merchant_key) {
$query = $db->query("SELECT * FROM users_wallets WHERE uid='$id'");

         
if($query->num_rows>0) {
    while($row = $query->fetch_assoc()) {
        $data[$row[currency]] = "$row[amount] $row[currency]";
    }
    $data['status'] = 'success';
}

} else {
    $data['status'] = 'error';
    $data['message'] = 'Merchant Api key wrong passes.';
}
echo json_encode($data);

?>