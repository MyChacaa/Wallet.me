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

$id = protect($_GET['txid']);
$CheckTx = $db->query("SELECT * FROM transactions WHERE txid='$id' and sender='$_SESSION[pw_uid]'");
if($CheckTx->num_rows==0) {
    $redirect = $settings['url']."index.php?a=account&b=summary";
    header("Location: $redirect");
}
$row = $CheckTx->fetch_assoc();
?>
          <div class="col-md-12">
                    
                    <div class="user-wallet-wrap">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="user-connected-form-block">
                                        <h3><?php echo filter_var($lang['head_open_a_dispute'], FILTER_SANITIZE_STRING); ?></h3>
                                        <hr/>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p><b>
                                                <?php
                                                if($_SESSION['pw_uid'] == $row['sender']) {
                                                    if(idinfo($row['recipient'],"account_type") == "1") { $name = idinfo($row['recipient'],"first_name")." ".idinfo($row['recipient'],"last_name"); } else { $name = idinfo($row['recipient'],"business_name"); } 
                                                    echo filter_var($lang['payment_sent_to'].' '.$name, FILTER_SANITIZE_STRING);
                                                } else {
                                                    if(idinfo($row['sender'],"account_type") == "1") { $name = idinfo($row['sender'],"first_name")." ".idinfo($row['sender'],"last_name"); } else { $name = idinfo($row['sender'],"business_name"); } 
                                                    echo filter_var($lang['payment_received_from'].' '.$name, FILTER_SANITIZE_STRING);    
                                                }
                                                ?>
                                                </b>    <span class="float-right"><?php echo filter_var($lang['gross_amount'], FILTER_SANITIZE_STRING); ?></span><p>
                                                <p><?php echo filter_var($lang['payment_status'], FILTER_SANITIZE_STRING); ?>: <?php echo filter_var(PW_DecodeTXStatus($row['status']), FILTER_SANITIZE_STRING); ?> <span class="float-right"><span style="font-size:22px;"><?php echo filter_var($row['amount'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($row['currency'], FILTER_SANITIZE_STRING); ?></span></span></p>
                                                <p><?php echo filter_var($lang['transaction_id'], FILTER_SANITIZE_STRING); ?>: <?php echo filter_var($row['txid'], FILTER_SANITIZE_STRING); ?></p>
                                                <p><?php echo filter_var($lang['payment_date'], FILTER_SANITIZE_STRING); ?>: <?php echo filter_var(date("d M Y H:i",$row['created']), FILTER_SANITIZE_STRING); ?></p>
                                                <?php if($row['description']) { ?>
                                                <p><?php echo filter_var($lang['payment_description'], FILTER_SANITIZE_STRING); ?>: <?php echo filter_var($row['description'], FILTER_SANITIZE_STRING); ?>
                                                <?php } ?>
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <hr/>
                                            </div>
                                            <div class="col-md-12">
                                                <?php
												if(isset($_POST['pw_open'])) {
                                                $FormBTN = protect($_POST['pw_open']);
												
                                                if($FormBTN == "open") {
                                                    $comment = protect($_POST['comment']);
                                                    if(empty($comment)) {
                                                        echo error($lang['error_4']);
                                                    } else {
                                                        $time = time();
                                                        $hash = 'W-'.strtoupper(randomHash(5)).'-'.strtoupper(randomHash(10)).'-'.strtoupper(randomHash(7));
                                                        $insert = $db->query("INSERT disputes (hash,sender,recipient,txid,amount,currency,escalate_review,escalate_message,escalate_issued_by,created_by,created,updated,status) VALUES ('$hash','$row[sender]','$row[recipient]','$row[txid]','$row[amount]','$row[currency]','0','','0','$_SESSION[pw_uid]','$time','0','1')");
                                                        $GetDispute = $db->query("SELECT * FROM disputes WHERE created_by='$_SESSION[pw_uid]' ORDER BY id DESC LIMIT 1");
                                                        $dispute = $GetDispute->fetch_assoc();
                                                        $insert = $db->query("INSERT disputes_messages (uid,dispute_id,comment,attachment,time,is_admin,status) VALUES ('$_SESSION[pw_uid]','$dispute[id]','$comment','','$time','0','1')");
                                                        $update = $db->query("UPDATE transactions SET status='4' WHERE txid='$row[txid]'");
                                                        $update = $db->query("UPDATE activity SET status='4' WHERE txid='$row[txid]'");
                                                        $redirect = $settings['url']."index.php?a=account&b=dispute&c=".$hash;
                                                        PW_EmailSys_NewDisputeMessage(idinfo($row['recipient'],"email"),$hash);
                                                        header("Location: $redirect");
                                                    }
                                                }
												}
                                                $CheckForDispute = $db->query("SELECT * FROM disputes WHERE txid='$row[txid]'");
                                                if($CheckForDispute->num_rows>0) {
                                                    echo error($lang['error_5']);
                                                } else {
                                                ?>
                                                <form class="user-connected-from user-login-form" action="" method="POST">
                                                    <div class="form-group">
                                                        <label><?= $lang['dear']; ?> <?php echo filter_var($name, FILTER_SANITIZE_STRING); ?></label>
                                                        <textarea class="form-control" name="comment" rows="7" placeholder="<?php echo filter_var($lang['placeholder_2'], FILTER_SANITIZE_STRING); ?>"></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary" name="pw_open" value="open"><?php echo filter_var($lang['btn_9'], FILTER_SANITIZE_STRING); ?></button>
                                                </form>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                        </div>  

                                    </div><!-- create-account-block -->
                                </div>
                            </div>
                        </div><!-- user-login-signup-form-wrap -->
            </div>