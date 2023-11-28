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
if ($m["disputes"] !== "1") {
    $redirect = $settings['url']."index.php?a=account&b=summary";
    header("Location: $redirect");
}
if(isset($_GET['c'])) {
$c = protect($_GET['c']);
}
switch($c) {
    case "open": include("disputes/open.php"); break;
    case "dispute": include("disputes/dispute.php"); break;
    case "escalate": include("disputes/escalate.php"); break;
    case "close": include("disputes/close.php"); break;
    default: include("disputes/disputes.php");
}
?>
</div>