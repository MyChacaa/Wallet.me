<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$id = protect($_GET['id']);
$query = $db->query("SELECT * FROM requests WHERE id='$id' and uid='$_SESSION[pw_uid]' and status='1'");
if($query->num_rows==0) { 
    $redirect = $settings['url']."index.phpp?a=account&b=summary";
    header("Location: $redirect");
}
$row = $query->fetch_assoc();
$update = $db->query("UPDATE requests SET status='2' WHERE id='$row[id]'");
?>
<div class="col-md-12">
    <div class="user-login-signup-form-wrap">
        <div class="modal-content">
            <div class="modal-body">
                <div class="user-connected-form-block">
                    <h3><?php echo filter_var($lang['head_requests'], FILTER_SANITIZE_STRING); ?></h2>
                    <hr/>
                    <?php echo error($lang['success_4']); ?>
                </div>
            </div>
        </div>
    </div>
</div>