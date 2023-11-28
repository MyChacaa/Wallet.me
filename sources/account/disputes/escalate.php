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

$id = protect($_GET['id']);
$query = $db->query("SELECT * FROM disputes WHERE hash='$id' and sender='$_SESSION[pw_uid]' and status='1' or hash='$id' and recipient='$_SESSION[pw_uid]' and status='1'");
if($query->num_rows==0) { 
    $redirect = $settings['url']."index.php?a=account&b=disputes";
    header("Location: $redirect");
}
$row = $query->fetch_assoc();
?>

<div class="col-md-12">
<div class="user-login-signup-form-wrap">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="user-connected-form-block">
                            <h3><?php $head_escalate = str_ireplace("%name%",$settings['name'],$lang['head_escalate']); echo filter_var($head_escalate, FILTER_SANITIZE_STRING); ?></h2>
                            <hr/>
                            <?php
                            $hide_form=0;
							if(isset($_POST['pw_submit'])) {
                            $FormBTN = protect($_POST['pw_submit']);
                            if($FormBTN == "submit") {
                                $comment = protect($_POST['comment']);
                                if(empty($comment)) {
                                    echo error($lang['error_3']);
                                } else {
                                    $update = $db->query("UPDATE disputes SET status='2',escalate_review='1',escalate_message='$comment',escalate_issued_by='$_SESSION[pw_uid]' WHERE id='$row[id]'");
                                     $hide_form=1;
                                     $success_3 = str_ireplace("%hash%",$row['hash'],$lang['success_3']);
                                     $success_3 = str_ireplace("%name%",$settings['name'],$success_3);
                                     echo success($success_3);
                                }
                            }
							}
                            if($hide_form==0) {
                            ?>
                            <form class="user-connected-from user-login-form" action="" method="POST">
                                <div class="form-group">
                                    <label><?php echo filter_var($lang['dear'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($settings['name'], FILTER_SANITIZE_STRING); ?></label>
                                    <textarea class="form-control" name="comment" rows="4" placeholder="<?php $placeholder_1 = str_ireplace("%name%",$settings['name'],$lang['placeholder_1']); echo filter_var($placeholder_1, FILTER_SANITIZE_STRING); ?>"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary" name="pw_submit" value="submit"><?php echo filter_var($lang['btn_8'], FILTER_SANITIZE_STRING); ?></button>
                            </form>
                            <?php
                            }
                            ?>
                        </div><!-- create-account-block -->
                    </div>
                </div>
            </div><!-- user-login-signup-form-wrap -->
</div>