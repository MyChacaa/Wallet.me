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
    case "open": include("escrow/open.php"); break;
    default: header("Location: $redirect_summary");
}
?>