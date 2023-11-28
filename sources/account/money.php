<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

if(!checkSession()) {
    $redirect = $settings['url']."index.php?a=login";
    header("Location: $redirect");
}

$redirect_summary = $settings['url']."index.php?a=account&b=summary";

$c = protect($_GET['c']);
switch($c) {
    case "send": include("money/send.php"); break;
    case "request": include("money/request.php"); break;
    case "request_pay": include("money/request_pay.php"); break;
    case "request_cancel": include("money/request_cancel.php"); break;
    case "deposit": include("money/deposit.php"); break;
    case "withdrawal": include("money/withdrawal.php"); break;
    case "link": include("money/link.php"); break;
    case "converter": include("money/converter.php"); break;
    case "evoucher": include("money/evoucher.php"); break;
    case "fixed_deposit": include("money/fixed_deposit.php"); break;
    default: header("Location: $redirect_summary");
}
?>