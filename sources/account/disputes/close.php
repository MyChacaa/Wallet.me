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
$query = $db->query("SELECT * FROM disputes WHERE hash='$id' and sender='$_SESSION[pw_uid]' and status='1'");
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
                            <h3><?php echo filter_var($lang['head_close_dispute'], FILTER_SANITIZE_STRING); ?></h2>
                            <hr/>
                            <?php
                            $hide_form=0;
							if(isset($_POST['pw_close'])) {
                            $FormBTN = protect($_POST['pw_close']);
                            if($FormBTN == "yes") {
                                $update = $db->query("UPDATE disputes SET status='4' WHERE id='$row[id]'");
                                $update = $db->query("UPDATE transactions SET status='1' WHERE txid='$row[txid]'");
                                $update = $db->query("UPDATE activity SET status='1' WHERE txid='$row[txid]'");
                                $hide_form=1;
                                $success_1 = str_ireplace("%hash%",$row['hash'],'$lang_success_1');
                                echo success($success_1);
                                PW_EmailSys_DisputeClosed(idinfo($row['recipient'],"email"),$row['hash'],"");
                            }
							}

                            if($hide_form==0) {
                            ?>
                            
                            <form class="user-connected-from user-login-form" action="" method="POST">
                                <div class="alert alert-info">
                                    <?= $lang['info_1']; ?> <b><?= $row['hash']; ?></b>
                                </div>
                                <button type="submit" name="pw_close" value="yes" class="btn btn-success"><?php echo filter_var($lang['btn_1'], FILTER_SANITIZE_STRING); ?></button> <a href="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>index.php?a=account&b=dispute&c=<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>" class="btn btn-danger"><?php echo filter_var($lang['btn_2'], FILTER_SANITIZE_STRING); ?></a>
                            </form>
                            <?php
                            }
                            ?>
                        </div><!-- create-account-block -->
                    </div>
                </div>
            </div><!-- user-login-signup-form-wrap -->
</div>