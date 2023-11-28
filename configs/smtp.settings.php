<?php
if(!defined("PWV1_INSTALLED")){
header("HTTP/1.0 404 Not Found");
exit;
}

$smtpconf = array();
$smtpconf["host"] = ""; // SMTP SERVER IP/HOST
$smtpconf["user"] = "";	// SMTP AUTH USERNAME if SMTPAuth is true
$smtpconf["pass"] = "";	// SMTP AUTH PASSWORD if SMTPAuth is true
$smtpconf["port"] = "";	// SMTP SERVER PORT
$smtpconf["ssl"] = ""; // 1 -  YES, 0 - NO
$smtpconf["SMTPAuth"] = true; // true / false
?>
                