<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

if(!checkSession()) {
    $redirect = $settings['url']."login";
    header("Location: $redirect");
}   
if(idinfo($_SESSION['pw_uid'],"account_type") == "1") {
    $redirect = $settings['url']."account/profile";
    header("Location: $redirect");
}
if ($m["merchants"] !== "1") {
    $redirect = $settings['url']."account/summary";
    header("Location: $redirect");
}
if(idinfo($_SESSION['pw_uid'],"business_status") == "1") {
?>
<h3><?Php echo $lang['head_merchant_api_key']; ?></h3>
<hr/>
<?php
$FormBTN = protect($_POST['pw_reset']);
if($FormBTN == "key") {
    if(idinfo($_SESSION['pw_uid'],"business_status") == "1") {
    $api_key = strtoupper(randomHash(5)).'-'.strtoupper(randomHash(5)).'-'.strtoupper(randomHash(5)).'-'.strtoupper(randomHash(5)).'-'.strtoupper(randomHash(5));
    $update = $db->query("UPDATE users SET merchant_api_key='$api_key' WHERE id='$_SESSION[pw_uid]'");
    }
}
?>

    <form class="user-connected-from user-signup-form" action="" method="POST">
    <?php echo $lang['your_merchant_api_key']; ?>: <span class="label label-success"><b><?php echo idinfo($_SESSION['pw_uid'],"merchant_api_key"); ?></b></span>
    <button type="submit" name="pw_reset" value="key" class="btn btn-success float-right"><?php echo $lang['btn_16']; ?></button>
    </form>

<?php 
    
} else {
echo error("Your Merchant Shoudl be approved before using this.");
}

?>