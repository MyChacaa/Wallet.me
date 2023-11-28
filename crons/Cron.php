<?php
define('PWV1_INSTALLED',TRUE);
ob_start();
session_start();
include("../configs/bootstrap.php");
include("../includes/bootstrap.php");
$a = protect($_GET['a']);
if($a == "FixDeposit") { include("Fix_Deposit.php"); }
else {
	echo 'Error! Unknown cron runs.';
}
?>