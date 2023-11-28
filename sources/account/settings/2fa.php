<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
error_reporting(0);
if(!checkSession()) {
    $redirect = $settings['url']."login";
    header("Location: $redirect");
}   
?>
<h3><?php echo $lang['head_2fa']; ?></h3>
<p><?php echo $lang['head_2fa_info']; ?></p>
<hr/>
<?php
$ga = new GoogleAuthenticator();
if(isset($_POST['pw_2fa'])) {
$FormBTN = protect($_POST['pw_2fa']);
if($FormBTN == "enable") {
    if(idinfo($_SESSION['ew_uid'],"googlecode")) {
        $secret = idinfo($_SESSION['pw_uid'],"googlecode");
        $_SESSION['pw_secret'] = $secret;
    } else {
        $secret = $ga->createSecret();
        $_SESSION['pw_secret'] = $secret;
    }
    $update = $db->query("UPDATE users SET 2fa_auth='1',2fa_auth_login='1',2fa_auth_send='1',2fa_auth_withdrawal='1',googlecode='$secret' WHERE id='$_SESSION[pw_uid]'");    
}

if($FormBTN == "disable") {
    $update = $db->query("UPDATE users SET 2fa_auth='0' WHERE id='$_SESSION[pw_uid]'");
}

if($FormBTN == "save") {
    
    
    if(isset($_POST['2fa_auth_send'])) { $fa_auth_send = 1; } else { $fa_auth_send = 0; }
    if(isset($_POST['2fa_auth_withdrawal'])) { $fa_auth_withdrawal = 1; } else { $fa_auth_withdrawal = 0; }
    
    $update = $db->query("UPDATE users SET 2fa_auth_send='$fa_auth_send',2fa_auth_withdrawal='$fa_auth_withdrawal' WHERE id='$_SESSION[pw_uid]'");
    echo success('Setting Updated...');
}
}
?>
<?php if(idinfo($_SESSION['pw_uid'],"2fa_auth") == "1") { ?>
<form class="user-connected-from user-signup-form" action="" method="POST">
    <?php echo $lang['currency_status']; ?>: <span class="badge badge-success"><?php echo $lang['enabled']; ?></span>
    <button type="submit" name="pw_2fa" value="disable" class="btn btn-danger float-right"><?php echo $lang['btn_14']; ?></button>
</form>
<br>
<form action="" method="POST">
<div class="form-group">
        <div class="custom-control custom-checkbox">
            <div class="custom-checkbox-wrap">
                <input type="checkbox" class="custom-control-input" id="2fa_auth_login" name="2fa_auth_login" <?php if(idinfo($_SESSION['pw_uid'],"2fa_auth_login") == "1") { echo 'checked'; } ?> value="yes">
                <label class="custom-control-label" for="2fa_auth_login">Require Google Authenticator code when login</label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <div class="custom-checkbox-wrap">
                <input type="checkbox" class="custom-control-input" id="2fa_auth_send" name="2fa_auth_send" <?php if(idinfo($_SESSION['pw_uid'],"2fa_auth_send") == "1") { echo 'checked'; } ?> value="yes">
                <label class="custom-control-label" for="2fa_auth_send">Require Google Authenticator code when send funds from your wallet</label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <div class="custom-checkbox-wrap">
                <input type="checkbox" class="custom-control-input" id="2fa_auth_withdrawal" name="2fa_auth_withdrawal" <?php if(idinfo($_SESSION['pw_uid'],"2fa_auth_withdrawal") == "1") { echo 'checked'; } ?> value="yes">
                <label class="custom-control-label" for="2fa_auth_withdrawal">Require Google Authenticator code when withdrawal funds from your wallet</label>
            </div>
        </div>
    </div>
    <button type="submit" name="pw_2fa" value="save"  class="btn btn-primary" style="padding:12px;">Save Changes</button>
</form>
<br>
<h3>Configurate your device</h3>
<p>Download Google Authenticator and Scan QR Code below or enter your token manually.</p>
<hr/>
<?php
$qrCodeUrl 	= $ga->getQRCodeGoogleUrl(idinfo($_SESSION['pw_uid'],"email"), $_SESSION['pw_secret'], $settings['name']); 
?>
    Token: <span class="float-right"><?php echo idinfo($_SESSION['pw_uid'],"googlecode"); ?></span><br><br>
    <center><img src='<?php echo $qrCodeUrl; ?>'><br>
            <a class="" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en" target="_blank">
                    <img src="<?php echo $settings['url']; ?>assets/images/android.png" width="150px">
            </a> 
            <a class="" href="https://itunes.apple.com/us/app/google-authenticator/id388497605?mt=8" target="_blank">
                <img src="<?php echo $settings['url']; ?>assets/images/iphone.png" width="150px">
            </a>
            </center>
<?php } else { ?>
    <form class="user-connected-from user-signup-form" action="" method="POST">
    <?php echo $lang['currency_status']; ?>: <span class="badge badge-danger"><?php echo $lang['disabled']; ?></span>
    <button type="submit" name="pw_2fa" value="enable" class="btn btn-success float-right"><?php echo $lang['btn_15']; ?></button>
    </form>
<?php } ?>