<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
error_reporting(0);
if(!checkSession()) {
    $redirect = $settings['url']."index.php?a=login";
    header("Location: $redirect");
}

$id = protect($_GET['id']);
$query = $db->query("SELECT * FROM disputes WHERE hash='$id' and sender='$_SESSION[pw_uid]' or hash='$id' and recipient='$_SESSION[pw_uid]'");
if($query->num_rows==0) { 
    $redirect = $settings['url']."index.php?a=account&b=disputes";
    header("Location: $redirect");
}
$row = $query->fetch_assoc();
?>
<div class="container-fluid py-4">
    <div class="row">          
                <div class="col-md-7">

                        <div class="user-wallet-wrap">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <div class="user-connected-form-block">
                                            
                                            <?php
                                            if($row['escalate_review'] == "1") {
                                                $info_2 = str_ireplace("%name%",$settings['name'],$lang['info_2']);
                                                echo info($info_2);
                                            }
											if(isset($_POST['pw_submit'])) {
                                            $FormBTN = protect($_POST['pw_submit']);
                                            if($FormBTN == "message") {
                                                $message = protect($_POST['message']);
                                                $extensions = array('jpg','jpeg','png','pdf'); 
                                                $fileextension = end(explode('.',$_FILES['uploadFile']['name'])); 
                                                $fileextension = strtolower($fileextension); 
                                                if(empty($message)) { echo error($lang['error_2']); }
                                                elseif(!empty($_FILES['uploadFile']['name']) && !in_array($fileextension,$extensions)) { echo error($lang['error_1']); }
                                                else {
                                                    $time = time();
                                                    $path = '';
                                                    if($_FILES['uploadFile']['name']) {
                                                        $path = 'uploads/disputes/'.$row['hash'].'_'.$_SESSION['pw_uid'].'_'.time().'_attachment.'.$fileextension;
                                                        @move_uploaded_file($_FILES['uploadFile']['tmp_name'], $path); 
                                                    }
                                                    $insert = $db->query("INSERT disputes_messages (dispute_id,uid,comment,attachment,time,status) VALUES ('$row[id]','$_SESSION[pw_uid]','$message','$path','$time','1')");
                                                    echo success($lang['success_2']);
                                                }
                                            }
											}

                                            $GetMessages = $db->query("SELECT * FROM disputes_messages WHERE dispute_id='$row[id]' ORDER BY id");
                                            if($GetMessages->num_rows>0) {
                                                while($get = $GetMessages->fetch_assoc()) {
                                                    ?>
                                                    <div id="message_<?php echo filter_var($get['id'], FILTER_SANITIZE_STRING); ?>">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <b><?php if($get['is_admin'] == "1") { echo filter_var($settings['name'], FILTER_SANITIZE_STRING); } else { if(idinfo($get['uid'],"account_type") == "1") { echo idinfo($get['uid'],"first_name")." ".idinfo($get['uid'],"last_name"); } else { echo idinfo($get['uid'],"business_name"); } } ?></b> <?php echo filter_var($lang['says'], FILTER_SANITIZE_STRING); ?>:
                                                                <span class="float-right"><small><?php echo timeago($get['time']); ?></small></span>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                    <?php echo nl2br($get['comment']); ?><br/>
                                                                    <?php
                                                                    $attachment = '';
                                                                    if(!empty($get['attachment'])) {
                                                                        $attachment = '<br/>
                                                                                                    <small>
                                                                                                        <a href="'.$settings['url'].$get['attachment'].'" target="_blank"><i class="fa fa-file"></i> '.basename($get['attachment']).'</a>
                                                                                                    </small>';
                                                                    }
                                                                    echo $attachment;
                                                                    ?>
                                                                    </div>
                                                                </div>	
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <?php
                                                }  
                                            }
                                            ?>
                                            
                                            <?php if($row['status'] == "1") { ?>
                                            <hr/>
                                            
                                            <div id="submit_message">
                                                <form action="" method="POST" enctype="multipart/form-data">
                                                    <div class="form-group">
                                                        <label><?php echo filter_var($lang['field_1'], FILTER_SANITIZE_STRING); ?></label>
                                                        <textarea class="form-control" name="message" rows="3"></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label><?php echo filter_var($lang['field_2'], FILTER_SANITIZE_STRING); ?></label>
                                                        <input class="btn btn-info" type="file" name="uploadFile">
                                                    </div>
                                                    <button type="submit" class="btn btn-primary" name="pw_submit" value="message"><?php echo filter_var($lang['btn_3'], FILTER_SANITIZE_STRING); ?></button>
                                                </form>
                                            </div>
                                            <br>
                                            <?php } ?>

                                            <?php if($row['status'] == "1") { ?>
                                            <a class="btn btn-secondary" href="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>index.php?a=account&b=escalate&c=dispute&d=<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>"><?php $btn_4 = str_ireplace("%name%",$settings['name'],$lang['btn_4']); echo filter_var($btn_4, FILTER_SANITIZE_STRING); ?></a><br/>
                                            <?php } ?>
                                            <?php if($_SESSION['pw_uid'] == $row['sender']) { ?>
                                            <a class="btn btn-danger" href="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>index.php?a=account&b=close&c=dispute&d=<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>"><?php echo filter_var($lang['btn_5'], FILTER_SANITIZE_STRING); ?></a><br/>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                </div>

        <div class="col-md-4">
            <div class="user-wallet-wrap">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="user-connected-form-block">
                            <b><?php echo filter_var($lang['dispute'], FILTER_SANITIZE_STRING); ?>:</b><br/>
                            <?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?><br/><br/>
                            <b><?php echo filter_var($lang['transaction'], FILTER_SANITIZE_STRING); ?>:</b><br/>
                            <a href="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>index.php?a=account&b=transaction&c=<?php echo filter_var($row['txid'], FILTER_SANITIZE_STRING); ?>"><?php echo filter_var($row['txid'], FILTER_SANITIZE_STRING); ?></a><br/><br/>
                            <b><?php echo filter_var($lang['amount'], FILTER_SANITIZE_STRING); ?>:</b><br/>
                            <?php echo filter_var($row['amount'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($row['currency'], FILTER_SANITIZE_STRING); ?><br/><br/>
                            <b><?php echo filter_var($lang['created_on'], FILTER_SANITIZE_STRING); ?>:</b><br/>
                            <?php echo filter_var(date("d M Y H:i",$row['created']), FILTER_SANITIZE_STRING); ?><br/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>