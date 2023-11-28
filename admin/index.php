<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(file_exists("./install.php")) {
	header("Location: ./install.php");
}
define('PWV1_INSTALLED',TRUE);
ob_start();
session_start();
include("../configs/bootstrap.php");
include("../includes/bootstrap.php");
if(checkAdminSession()) {
	include("sources/header.php");
	if (isset($_GET['a'])){
		$a = protect($_GET['a']);
	} else {
		$a = "";
	}
	switch($a) {
		case "users": include("sources/users.php"); break;
		case "all_user_activity": include("sources/all_user_activity.php"); break;
		case "manual_transactions": include("sources/manual_transactions.php"); break;
		case "manual_deposit": include("sources/manual_deposit.php"); break;
		case "disputes": include("sources/disputes.php"); break;
		case "deposit_methods": include("sources/deposit_methods.php"); break;
		case "deposits": include("sources/deposits.php"); break;
		case "withdrawal_methods": include("sources/withdrawal_methods.php"); break;
		case "withdrawals": include("sources/withdrawals.php"); break;
		case "transactions": include("sources/transactions.php"); break;
		case "send_request_fee": include("sources/send_request_fee.php"); break;
		case "merchant_payments": include("sources/merchant_payments.php"); break;
		case "languages": include("sources/languages.php"); break;
		case "smtp_settings": include("sources/smtp_settings.php"); break;
		case "support": include("sources/support.php"); break;
		case "logs": include("sources/logs.php"); break;
		case "ref": include("sources/ref.php"); break;
		case "settings": include("sources/settings.php"); break;
		case "link": include("sources/link.php"); break;
		case "admin_profits": include("sources/admin_profits.php"); break;
		case "admin_profits_logs": include("sources/admin_profits_logs.php"); break;
		case "live_chat": include("sources/live_chat.php"); break;
		case "google_analytics": include("sources/google_analytics.php"); break;
		case "currencies": include("sources/currencies.php"); break;
		case "all_currencies": include("sources/all_currencies.php"); break;
		case "curencies_fee": include("sources/curencies_fee.php"); break;
		case "curencies_log": include("sources/curencies_log.php"); break;
		case "merchant_fee": include("sources/merchant_fee.php"); break;
		case "merchant_payments_log": include("sources/merchant_payments_log.php"); break;
		case "all_merchant": include("sources/all_merchant.php"); break;
		case "merchant_gateways": include("sources/merchant_gateways.php"); break;
		case "send_mail": include("sources/send_mail.php"); break;
		case "country": include("sources/country.php"); break;
		case "update_logo": include("sources/update_logo.php"); break;
		case "module": include("sources/module.php"); break;
		case "evoucher_setting": include("sources/evoucher_setting.php"); break;
		case "evoucher_all": include("sources/evoucher_all.php"); break;
		case "pages": include("sources/pages.php"); break;
		case "fixed_deposit": include("sources/fixed_deposit.php"); break;
		case "fixed_deposit_list": include("sources/fixed_deposit_list.php"); break;
		case "escrow": include("sources/escrow.php"); break;
		case "logout": 
			unset($_SESSION['admin_uid']);
			unset($_COOKIE['admin_uid']);
			setcookie("admin_uid", "", time() - (86400 * 30), '/'); // 86400 = 1 day
			session_unset();
			session_destroy();
			header("Location: $settings[url]admin");
		break;
		default: include("sources/dashboard.php");
	}
	include("sources/footer.php");
} else {
	include("sources/login.php");
}
mysqli_close($db);
?>