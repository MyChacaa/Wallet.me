<?php
if(!defined("PWV1_INSTALLED")){
header("HTTP/1.0 404 Not Found");
exit;
}
                
$m = array();
$m["deposit"] = "1"; // Deposit
$m["withdrawal"] = "1"; // Withdrawal
$m["send_money"] = "1"; // Send Money
$m["request_money"] = "1"; // Request Money
$m["currency_convert"] = "1"; // Currency Convert
$m["disputes"] = "1"; // Dispute
$m["merchants"] = "1"; // Merchants
$m["payment_link"] = "1"; // Payment Link
$m["referral_system"] = "1"; // Referral System
$m["support_ticket"] = "1"; // Support Ticket
$m["live_chat"] = "1"; // Live Chat
$m["google_analytics"] = "1"; // Google Analytics
$m["registration"] = "1"; // User Registration
$m["forget_password"] = "1"; // Forget Password
$m["fixed_deposit"] = "1"; // Fixed Deposit
$m["escrow"] = "0"; // Escrow
?>
            