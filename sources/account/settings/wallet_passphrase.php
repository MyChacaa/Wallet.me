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
?>
<h3><?php echo $lang['head_wallet_passphrase']; ?></h3>
<p><?php echo $lang['head_wallet_passphrase_info']; ?></p>
<hr/>
<?php
if(isset($_POST['pw_passphrase'])) {
$FormBTN = protect($_POST['pw_passphrase']);
if($FormBTN == "setup") {
    $passphrase = protect($_POST['passphrase']);
    if(empty($passphrase)) {
        echo error($lang['error_28']);
    } elseif(strlen($passphrase)<6) {
        echo error($lang['error_29']);  
    } else {
        $passphrase = password_hash($passphrase, PASSWORD_DEFAULT);
        $update = $db->query("UPDATE users SET wallet_passphrase='$passphrase' WHERE id='$_SESSION[pw_uid]'");
        echo success($lang['success_13']);
    }
}

if($FormBTN == "change") {
    $cpassphrase = protect($_POST['cpassphrase']);
    $npassphrase = protect($_POST['npassphrase']);
    $cnpassphrase = protect($_POST['cnpassphrase']);
    if(!password_verify($cpassphrase, idinfo($_SESSION['pw_uid'],"wallet_passphrase"))) {
        echo error($lang['error_30']);
    } elseif(empty($npassphrase)) {
        echo error($lang['error_28']);
    } elseif($npassphrase !== $cnpassphrase) {
        echo error($lang['error_31']);
    } else {
        $passphrase = password_hash($npassphrase, PASSWORD_DEFAULT);
        $update = $db->query("UPDATE users SET wallet_passphrase='$passphrase' WHERE id='$_SESSION[pw_uid]'");
        echo success($lang['success_14']);
    }
} 


if($FormBTN == "remove") {
    $cpassphrase = protect($_POST['cpassphrase']);
    if(!password_verify($cpassphrase, idinfo($_SESSION['pw_uid'],"wallet_passphrase"))) {
        echo error($lang['error_32']);
    } else {
        $update = $db->query("UPDATE users SET wallet_passphrase='' WHERE id='$_SESSION[pw_uid]'");
        echo success($lang['success_15']);
    }
}
}
?>

<?php
if(empty(idinfo($_SESSION['pw_uid'],"wallet_passphrase"))) {
?>
<form class="user-connected-from user-signup-form" action="" method="POST">
<div class="form-group">
        <label><?php echo $lang['field_21']; ?></label>
        <input type="password" class="form-control" name="passphrase">
    </div>
    <button type="submit" name="pw_passphrase" value="setup" class="btn btn-primary" style="padding:12px;"><?php echo $lang['btn_20']; ?></button>
</form>
<?php 
} else {
?>
<form class="user-connected-from user-signup-form" action="" method="POST">
<div class="form-group">
        <label><?php echo $lang['field_22']; ?></label>
        <input type="password" class="form-control" name="cpassphrase">
    </div>
    <button type="submit" name="pw_passphrase" value="remove" class="btn btn-danger" style="padding:12px;"><?php echo $lang['btn_21']; ?></button>
    <div class="form-group">
        <label><?php echo $lang['field_21']; ?></label>
        <input type="password" class="form-control" name="npassphrase">
    </div>
    <div class="form-group">
        <label><?php echo $lang['field_23']; ?></label>
        <input type="password" class="form-control" name="cnpassphrase">
    </div>
    <button type="submit" name="pw_passphrase" value="change" class="btn btn-primary" style="padding:12px;"><?php echo $lang['btn_17']; ?></button> 
    
</form>
<?php
}
?>