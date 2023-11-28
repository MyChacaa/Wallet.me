<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

include("phpmailer/phpmailer.class.php");
include("function.email.php");
include("function.user.php");
include("function.web.php");
include("function.language.php");
include("function.messages.php");
include("function.pagination.php");
include("function.payment_form.php");
include("class.template.php");
include("version.php");
include("GoogleAuthenticator.php");
?>