<?php
define('PWV1_INSTALLED',TRUE);
ob_start();
session_start();
include("../configs/bootstrap.php");
include("../includes/bootstrap.php");

$a = protect($_GET['a']);
$b = protect($_GET['b']);

$query_1 = $db->query("SELECT * FROM merchant_gateways WHERE name='Perfect Money'");
$row_1 = $query_1->fetch_assoc();

$query_2 = $db->query("SELECT * FROM merchant_gateways WHERE name='Payeer'");
$row_2 = $query_2->fetch_assoc();

$query_3 = $db->query("SELECT * FROM merchant_gateways WHERE name='Stripe'");
$row_3 = $query_3->fetch_assoc();

$query_4 = $db->query("SELECT * FROM merchant_gateways WHERE name='Flutterwave'");
$row_4 = $query_4->fetch_assoc();

if($a == "Payeer") { if ($row_2['status'] == "1")  { include("Payeer.php"); } else { echo 'Error! Unknown merchant.'; } }
elseif($a == "PerfectMoney") { if ($row_1['status'] == "1") { include("PerfectMoney.php"); } else { echo 'Error! Unknown merchant.'; } }
elseif($a == "Stripe") { if ($row_3['status'] == "1") { include("Stripe.php"); } else { echo 'Error! Unknown merchant.'; } }
elseif($a == "Flutterwave") { if ($row_4['status'] == "1") { include("Flutterwave.php"); } else { echo 'Error! Unknown merchant.'; } }
else {
	echo 'Error! Unknown merchant.';
}
?>