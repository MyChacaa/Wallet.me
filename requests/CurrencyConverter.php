<?php
// eWallet - PHP Script
// Author: DeluxeScript
define('PWV1_INSTALLED',TRUE);
ob_start();
session_start();
include("../configs/bootstrap.php");
include("../includes/bootstrap.php");


$amount = protect($_GET['amount']);
$from = protect($_GET['from']);
$to = protect($_GET['to']);
$cc = currencyConvertor($amount,$from,$to);
if ($cc !== "0.00") {
    $data['status'] = 'success';
    $data['convert'] = $cc;
} else {
    $data['status'] = 'error server down!';
}
echo json_encode($data);

?>