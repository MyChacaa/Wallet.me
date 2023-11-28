<?php
// eWallet - PHP Script
// Author: DeluxeScript
define('PWV1_INSTALLED',TRUE);
ob_start();
session_start();
include("../configs/bootstrap.php");
include("../includes/bootstrap.php");
$data = array();

    $amount = protect($_GET['amount']);
    $from = strtoupper(protect($_GET['from']));
    $to = strtoupper(protect($_GET['to']));
    $rates = PW_GetRates($from,$to);
    if($rates['status'] == "success") {
        if($rates['rate_from'] < 1) { 
            $receive = $amount / $rates['rate_from'];
        } else { 
            $receive = $amount * $rates['rate_to'];
        }
        $receive = number_format($receive,2,'.','');
        $data['status'] = 'success';
        $data['rate_from'] = $rates['rate_from'];
        $data['rate_to'] = $rates['rate_to'];
        $data['currency_from'] = $rates['currency_from'];
        $data['currency_to'] = $rates['currency_to'];
    }

echo json_encode($data);
?>