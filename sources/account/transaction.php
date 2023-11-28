<div class="container-fluid py-4">
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


$id = protect($_GET['id']);
$query = $db->query('SELECT * FROM transactions WHERE txid=\'' . $id . '\' and sender=\'' . $_SESSION['pw_uid'] . '\' or txid=\'' . $id . '\' and recipient=\'' . $_SESSION['pw_uid'] . '\'');
if($query->num_rows==0) {
    $redirect = $settings['url']."index.php?a=account&b=summary";
    header("Location: $redirect");
}
$row = $query->fetch_assoc();
$type = $row['type'];
switch($type) {
    case "1": include("transaction/payment.php"); break;
    case "2": include("transaction/payment.php"); break;
    case "3": include("transaction/deposit.php"); break;
    case "4": include("transaction/withdrawal.php"); break;
    case "7": include("transaction/fee_reversal.php"); break;
    case "8": include("transaction/convert.php"); break;
    case "9": include("transaction/convert.php"); break;
    case "28": include("transaction/AdminTransfer.php"); break;
    case "29": include("transaction/AdminTransfer.php"); break;
    case "30": include("transaction/PerfectMoney_P.php"); break;
    case "31": include("transaction/Payeer_P.php"); break;
    case "32": include("transaction/Stripe_P.php"); break;
    case "33": include("transaction/Flutter_P.php"); break;
    case "41": include("transaction/evoucher.php"); break;
    case "42": include("transaction/evoucher.php"); break;
    case "43": include("transaction/evoucher.php"); break;
    case "44": include("transaction/evoucher.php"); break;
    case "45": include("transaction/evoucher.php"); break;
    case "51": include("transaction/fixed_deposit.php"); break;
    case "52": include("transaction/fixed_deposit.php"); break;
    case "53": include("transaction/fixed_deposit.php"); break;
    case "61": include("transaction/escrow.php"); break;
    case "62": include("transaction/escrow.php"); break;
    default: include("transaction/payment.php");
}
?>
</div>