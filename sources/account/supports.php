<div class="container-fluid py-4">
<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
if ($m["support_ticket"] !== "1") {
    $redirect = $settings['url']."index.php?a=account&b=summary";
    header("Location: $redirect");
}
if(!checkSession()) {
    $redirect = $settings['url']."index.php?a=login";
    header("Location: $redirect");
}
if(isset($_GET['c'])) {
$c = protect($_GET['c']);
}
switch($c) {
    case "open": include("supports/open.php"); break;
    case "support": include("supports/support.php"); break;
    case "escalate": include("supports/escalate.php"); break;
    case "close": include("supports/close.php"); break;
    default: include("supports/supports.php");
}

?>
</div>