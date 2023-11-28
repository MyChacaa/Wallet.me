<?php
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

if (isset($_GET['b'])){
	$b = protect($_GET['b']);
} else {
	$b = "";
}
if($b == "view") {
	$id = protect($_GET['id']);
	$query = $db->query("SELECT * FROM disputes WHERE hash='$id'");
	if($query->num_rows==0) { header("Location: ./?a=disputes"); }
	$row = $query->fetch_assoc();
	?>
	
        <div class="row">  
           <div class="col-md-8">
					<div class="card">
                        <div class="card-body">
                        <?php
                                            if($row['escalate_review'] == "1") {
                                                echo info("This dispute was escalated for $settings[name] review.");
                                            }

                                            $FormBTN = protect($_POST['pw_submit']);
                                            if($FormBTN == "message") {
                                                $message = protect($_POST['message']);
                                                $extensions = array('jpg','jpeg','png','pdf'); 
                                                $fileextension = end(explode('.',$_FILES['uploadFile']['name'])); 
                                                $fileextension = strtolower($fileextension); 
                                                if(empty($message)) { echo error("Please enter a message."); }
                                                elseif(!empty($_FILES['uploadFile']['name']) && !in_array($fileextension,$extensions)) { echo error("Allowed file types: jpg,png and pdf."); }
                                                else {
                                                    $time = time();
                                                    $path = '';
                                                    if($_FILES['uploadFile']['name']) {
                                                        $path = 'uploads/disputes/'.$row['hash'].'_'.$_SESSION['pw_uid'].'_'.time().'_attachment.'.$fileextension;
                                                        @move_uploaded_file($_FILES['uploadFile']['tmp_name'], '../'.$path); 
                                                    }
                                                    $insert = $db->query("INSERT disputes_messages (dispute_id,uid,comment,attachment,time,status,is_admin) VALUES ('$row[id]','0','$message','$path','$time','1','1')");
                                                    $update = $db->query("UPDATE disputes SET status='1' WHERE id='$row[id]'");
                                                    echo success("Your message has been sent successfully!");
                                                }
                                            }

                                            if($FormBTN == "refund") {
                                                $amount = $row['amount'];
                                                $currency= $row['currency'];
                                                $amount = number_format($amount, 2, '.', '');
                                                $fee = ($amount * $settings['payfee_percentage']) / 100;
                                                $amount_with_fee = $amount - $fee;
                                                PW_UpdateUserWallet($row['sender'],$amount,$currency,1);
                                                PW_UpdateUserWallet($row['recipient'],$amount,$currency,2);
                                                PW_UpdateUserWallet($row['recipient'],$fee,$currency,2);
                                                $txid = strtoupper(randomHash(30));
                                                $time = time();
                                                $create_transaction = $db->query("INSERT transactions (txid,type,sender,recipient,description,amount,currency,fee,status,created) VALUES ('$txid','5','$row[sender]','$row[recipient]','$description','$amount','$currency','$fee','1','$time')");
                                                $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) VALUES ('$txid','5','$row[sender]','$row[recipient]','$amount','$currency','1','$time')");
                                                $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) VALUES ('$txid','6','$row[recipient]','$row[sender]','$amount','$currency','1','$time')");
                                                $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) VALUES ('$txid','7','$row[recipient]','$row[sender]','$fee','$currency','1','$time')");
                                                PW_UpdateAdminWallet($fee,$currency);
                                                $insert_admin_log = $db->query("INSERT admin_logs (type,time,u_field_1,u_field_2,u_field_3) VALUES ('5','$time','$fee','$currency','$txid')");
                                                $update = $db->query("UPDATE disputes SET status='3' WHERE id='$row[id]'");
                                                $update = $db->query("UPDATE transactions SET status='5' WHERE txid='$row[txid]'");
                                                $update = $db->query("UPDATE activity SET status='5' WHERE txid='$row[txid]'");
                                                $row['status'] = '3'; 
                                                echo success("Dispute was closed and amount was refunded.");
                                                PW_EmailSys_DisputeClosed(idinfo($row['sender'],"email"),$row['hash'],"Your amount has been refunded.","../");
                                                PW_EmailSys_DisputeClosed(idinfo($row['recipient'],"email"),$row['hash'],"The amount was refunded to the sender.","../");              
                                            }

                                            if($FormBTN == "close") {
                                                $update = $db->query("UPDATE disputes SET status='4' WHERE id='$row[id]'");
                                                $update = $db->query("UPDATE transactions SET status='1' WHERE txid='$row[txid]'");
                                                $update = $db->query("UPDATE activity SET status='1' WHERE txid='$row[txid]'");
                                                $row['status'] = '4';
                                                echo success("Dispute was closed.");
                                                PW_EmailSys_DisputeClosed(idinfo($row['sender'],"email"),$row['hash'],"We found your claim unreasonable, the amount was not refunded.","../");
                                                PW_EmailSys_DisputeClosed(idinfo($row['recipient'],"email"),$row['hash'],"The dispute has been convicted in your favor, you have not been charged.","../");
                                            }


                                            $GetMessages = $db->query("SELECT * FROM disputes_messages WHERE dispute_id='$row[id]' ORDER BY id");
                                            if($GetMessages->num_rows>0) {
                                                while($get = $GetMessages->fetch_assoc()) {
                                                    ?>
                                                    <div id="message_<?php echo filter_var($get['id'], FILTER_SANITIZE_STRING); ?>">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <b><?php if($get['is_admin'] == "1") { echo filter_var($settings['name'], FILTER_SANITIZE_STRING); } else { if(idinfo($get['uid'],"account_type") == "1") { echo idinfo($get['uid'],"first_name")." ".idinfo($get['uid'],"last_name"); } else { echo idinfo($get['uid'],"business_name"); } } ?></b> says:
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
                                            
                                            <?php if($row['status']<3) { ?>
                                            <hr/>
                                            
                                            <div id="submit_message">
                                                <form action="" method="POST" enctype="multipart/form-data">
                                                    <div class="form-group">
                                                        <label>Message</label>
                                                        <textarea class="form-control" name="message" rows="3"></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Attach a file</label>
                                                        <input type="file" name="uploadFile">
                                                    </div>
                                                    <button type="submit" class="btn btn-primary" name="pw_submit" value="message">Submit message</button>
                                                </form>
                                            </div>
                                            <hr/>
                                            <form action="" method="POST">
                                                <button type="submit" class="btn btn-primary" name="pw_submit" value="refund">Refund & Close</button> 
                                                <button type="submit" class="btn btn-primary" name="pw_submit" value="close">Cancel & Close</button>
                                            </form>
                                            <?php } ?>

                                            
		</div>
        </div>
        
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <b>Dispute ID:</b><br/>
                                            <?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?><br/><br/>
                                            <b>Transaction ID:</b><br/>
                                            <a href="./?a=transactions&b=view&txid=<?php echo filter_var($row['txid'], FILTER_SANITIZE_STRING); ?>"><?php echo filter_var($row['txid'], FILTER_SANITIZE_STRING); ?></a><br/><br/>
                                            <b>Amount:</b><br/>
                                            <?php echo filter_var($row['amount'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($row['currency'], FILTER_SANITIZE_STRING); ?><br/><br/>
                                            <b>Created on:</b><br/>
                                            <?php echo date("d M Y H:i",$row['created']); ?><br/>
            </div>
        </div>
    </div>
    
                </div>
	<?php
} else {
?>


            <div class="col-md-12">
				<div class="card">
                    <div class="card-body">
                        <form action="" method="POST">
                        <div class="row">
                        <div class="col-md-3" style="padding:10px;">
                                <input type="text" class="form-control" name="hash" placeholder="Dispute ID" value="<?php if(isset($_POST['hash'])) { echo filter_var($_POST['hash'], FILTER_SANITIZE_STRING); } ?>">
                            </div>
                            <div class="col-md-3" style="padding:10px;">
                                <input type="text" class="form-control" name="txid" placeholder="Transaction ID" value="<?php if(isset($_POST['txid'])) { echo filter_var($_POST['txid'], FILTER_SANITIZE_STRING); } ?>">
                            </div>
                            <div class="col-md-3" style="padding:10px;">
                                <input type="text" class="form-control" name="email" placeholder="Email address" value="<?php if(isset($_POST['email'])) { echo filter_var($_POST['email'], FILTER_SANITIZE_STRING); } ?>">
                            </div>
                            <div class="col-md-3" style="padding:10px;">
                                <button type="submit" class="btn btn-primary btn-block" name="btn_search" value="disputes">Search</button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

           <div class="col-md-12">
					<div class="card">
                        <div class="card-body table-responsive">
                            
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="30%">#</th>
                                    <th width="15%">Sender</th>
                                    <th width="15%">Recipient</th>
                                    <th width="15%">Status</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $searching=0;
                                if (isset($_POST['btn_search'])){
								$FormBTN = protect($_POST['btn_search']);
								} else {
								$FormBTN = "";
								}
                                if($FormBTN == "disputes") {
                                    $searching=1;
                                    $search_query = array();
                                    $s_hash = protect($_POST['hash']);
                                    if(!empty($s_hash)) { $search_query[] = "hash='$s_hash'"; }
                                    $s_txid = protect($_POST['txid']);
                                    if(!empty($s_txid)) { $search_query[] = "txid='$s_txid'"; }
                                    $s_email = protect($_POST['email']);
                                    if(!empty($s_email)) {
                                        if(PW_GetUserID($s_email)) {
                                            $s_uid = PW_GetUserID($s_email);
                                            $search_query[] = "sender='$s_uid' or recipient='$s_uid'";
                                        }
                                    }
                                    $p_query = implode(" and ",$search_query);
                                }
                                $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
                                $limit = 20;
                                $startpoint = ($page * $limit) - $limit;
                                if($page == 1) {
                                    $i = 1;
                                } else {
                                    $i = $page * $limit;
                                }
                                $statement = "disputes";
                                if($searching==1) {
                                    if(empty($p_query)) {
                                        $qry = 'empty query';
                                    }
                                    $query = $db->query("SELECT * FROM {$statement} WHERE $p_query ORDER BY id");
                                } else {
                                    $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                                }
                                if($query->num_rows>0) {
                                    while($row = $query->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td>
                                                Dispute ID:<br/>#<a href="./?a=disputes&b=view&id=<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>"><?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?></a><br/>
                                                Transaction ID:<br/>#<a href="./?a=transactions&b=view&txid=<?php echo filter_var($row['txid'], FILTER_SANITIZE_STRING); ?>"><?php echo filter_var($row['txid'], FILTER_SANITIZE_STRING); ?></a>
                                            </td>
                                            <td><a href="./?a=users&b=edit&id=<?php echo filter_var($row['sender'], FILTER_SANITIZE_STRING); ?>"><?php echo idinfo($row['sender'],"email"); ?></a></td>
                                            <td><a href="./?a=users&b=edit&id=<?php echo filter_var($row['recipient'], FILTER_SANITIZE_STRING); ?>"><?php echo idinfo($row['recipient'],"email"); ?></a></td>
                                            <td>
                                                 <?php
                                                                $status = $row['status'];
                                                                if($row['status'] == "1") {
                                                                    echo '<span class="badge badge-info">Open</span>';
                                                                } elseif($row['status'] == "2") {
                                                                    echo '<span class="badge badge-primary">Under Review</span>';
                                                                } elseif($row['status'] == "3") {
                                                                    echo '<span class="badge badge-success">Finished</span>';
                                                                } elseif($row['status'] == "4") {
                                                                    echo '<span class="badge badge-warning">Closed</span>';
                                                                } else {
                                                                    echo '<span class="badge badge-default">Unknown</span>';
                                                                }
                                                                ?> 
                                            </td>
                                            <td>
                                                <a href="./?a=disputes&b=view&id=<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>" title="View"><span class="badge badge-primary"><i class="fa fa-search"></i> View</span></a> 
                                                </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    if($searching == "1") {
                                        echo '<tr><td colspan="6">No found results.</td></tr>';
                                    } else {
                                        echo '<tr><td colspan="6">No have disputes yet.</td></tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        if($searching == "0") {
                            $ver = "./?a=disputes";
                            if(admin_pagination($statement,$ver,$limit,$page)) {
                                echo admin_pagination($statement,$ver,$limit,$page);
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
<?php
}
?>