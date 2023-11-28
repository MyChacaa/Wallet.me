<?php
define('PWV1_INSTALLED',TRUE);
ob_start();
session_start();
include("../configs/bootstrap.php");
include("../includes/bootstrap.php");
$a = protect($_GET['a']);
if($a == "PayPal") { include("PayPal.php"); }
elseif($a == "AdvCash") { include("AdvCash.php"); }
elseif($a == "Payeer") { include("Payeer.php"); }
elseif($a == "Payeer_P") { include("Payeer_P.php"); }
elseif($a == "PerfectMoney") { include("PerfectMoney.php"); }
elseif($a == "PerfectMoney_P") { include("PerfectMoney_P.php"); }
elseif($a == "Skrill") { include("Skrill.php"); }
elseif($a == "Paytm") { include("Paytm.php"); }
elseif($a == "Flutterwave") { include("Flutterwave.php"); }
elseif($a == "Flutterwave_P") { include("Flutterwave_P.php"); }
elseif($a == "Stripe") { include("Stripe.php"); }
elseif($a == "Stripe_P") { include("Stripe_P.php"); }
elseif($a == "Stripe_P_S") { include("Stripe_P_S.php"); }
elseif($a == "2Checkout") { include("2Checkout.php"); }
else {
	echo 'Error! Unknown merchant.';
}
?>