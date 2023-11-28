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
	$query = $db->query("SELECT * FROM support WHERE hash='$id'");
	if($query->num_rows==0) { header("Location: ./?a=support"); }
	$row = $query->fetch_assoc();
	?>
	<div class="row">
            
           <div class="col-md-8">
					<div class="card">
                        <div class="card-body">
                        <?php
											if(isset($_POST['pw_submit'])) {
                                            $FormBTN = protect($_POST['pw_submit']);
                                            if($FormBTN == "message") {
                                                $message = addslashes($_POST['message']);
                                                if(empty($message)) {
                                                    echo error("Please enter message.");
                                                } else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $message)) {
                                                    echo error("Invalid Characters are not allowed.");
                                                } else {
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
														$insert = $db->query("INSERT support_messages (dispute_id,uid,comment,attachment,time,status,is_admin) VALUES ('$row[id]','0','$message','$path','$time','1','1')");
														$update = $db->query("UPDATE support SET status='1' WHERE id='$row[id]'");
														PW_EmailSys_SupportReply(idinfo($row['sender'],"email"),$row['hash'],"Your Support Ticket Has been Replied.","../");
														echo success("Your message has been sent successfully!");
													}
												}
											}
											

                                            

                                            if($FormBTN == "close") {
                                                $update = $db->query("UPDATE support SET status='4' WHERE id='$row[id]'");
                                                $row['status'] = '4';
                                                PW_EmailSys_SupportClosed(idinfo($row['sender'],"email"),$row['hash'],"Your Support Ticket Has been Closed.","../");
                                                echo success("Ticket was closed.");
                                            }
											}

                                            $GetMessages = $db->query("SELECT * FROM support_messages WHERE dispute_id='$row[id]' ORDER BY id");
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
                                                <form action="" method="POST">
                                                    <div class="form-group">
                                                        <label>Message</label>
                                                        <textarea id="tinyMceExample" class="form-control" name="message" rows="3"></textarea>
                                                    </div>
                                                    
                                                    <button type="submit" class="btn btn-primary" name="pw_submit" value="message">Submit message</button>
                                                </form>
                                            </div>
                                            <hr/>
                                            <form action="" method="POST">
                                                <button type="submit" class="btn btn-primary" name="pw_submit" value="close">Close</button>
                                            </form>
                                            <?php } ?>

                                            
		</div>
        </div>
        
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <b>Ticket ID:</b><br/>
                                            <?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?><br/><br/>
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
                                <input type="text" class="form-control" name="hash" placeholder="Ticket ID" value="<?php if(isset($_POST['hash'])) { echo filter_var($_POST['hash'], FILTER_SANITIZE_STRING); } ?>">
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
                                    <th width="30%">Ticket #</th>
                                    <th width="15%">Date</th>
                                    <th width="15%">Sender</th>
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
                                $statement = "support";
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
                                                #<a href="./?a=support&b=view&id=<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>"><?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?></a><br/>
                                                
                                            </td>
                                            <td><?php echo date("d/m/Y H:i:s",$row['created']); ?></td>
                                            <td><a href="./?a=users&b=edit&id=<?php echo filter_var($row['sender'], FILTER_SANITIZE_STRING); ?>"><?php echo idinfo($row['sender'],"email"); ?></a></td>
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
                                                <a href="./?a=support&b=view&id=<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>" title="View"><span class="badge badge-primary"><i class="fa fa-search"></i> View</span></a> 
                                                </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    if($searching == "1") {
                                        echo '<tr><td colspan="6">No found results.</td></tr>';
                                    } else {
                                        echo '<tr><td colspan="6">No have Tickets yet.</td></tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        if($searching == "0") {
                            $ver = "./?a=support";
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
<script>
  $(function () {
    // Summernote
    $('#summernote').summernote()

    // CodeMirror
    CodeMirror.fromTextArea(document.getElementById("codeMirrorDemo"), {
      mode: "htmlmixed",
      theme: "monokai"
    });
  })
</script>