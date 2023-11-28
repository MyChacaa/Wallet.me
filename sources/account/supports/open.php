<?php
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
   ?>
<div class="content mt-3">
		<div class="container">
		    <div class="row">
				<div class="container">
					<div class="row flex-lg-nowrap">
						<div class="col-md-12">
							<div class="row flex-lg-nowrap">
								<div class="col mb-3">
									<div class="e-panel card" style="border-color: blue;border-top-width:initial;">
										<div class="card-body">
											<div class="e-table">
												<div class=" table-lg mt-3">
													<div class="col-md-12">
														<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
															<br>
															<h2>Contact Form</h2>
															<h6 class="text-left"><small>Describe your issue.</small></h6>
															<hr>
															<?php
																if(isset($_POST['pw_open'])) {
                                                                $FormBTN = protect($_POST['pw_open']);
                                                                if($FormBTN == "open") {
                                                                    $comment = addslashes($_POST['comment']);
                                                                    if(empty($comment)) {
                                                                        echo error($lang['error_4']);
                                                                    } else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $comment)) {
                                                                        echo error("Invalid Characters are not allowed.");
                                                                    } else {
                                                                        $time = time();
                                                                        $hash = 'M-'.strtoupper(randomHash(5)).'-'.strtoupper(randomHash(10)).'-'.strtoupper(randomHash(7));
                                                                        $insert = $db->query("INSERT support (hash,sender,created_by,created,updated,status) VALUES ('$hash','$_SESSION[pw_uid]','$_SESSION[pw_uid]','$time','0','1')");
                                                                        $GetDispute = $db->query("SELECT * FROM support WHERE created_by='$_SESSION[pw_uid]' ORDER BY id DESC LIMIT 1");
                                                                        $dispute = $GetDispute->fetch_assoc();
                                                                        $insert = $db->query("INSERT support_messages (uid,dispute_id,comment,attachment,time,is_admin,status) VALUES ('$_SESSION[pw_uid]','$dispute[id]','$comment','','$time','0','1')");
                                                                        $redirect = $settings['url']."index.php?a=account&b=support&id=".$hash;
                                                                        header("Location: $redirect");
                                                                    }
                                                                }
																}
                                                              
                                                            ?>
															<form class="user-connected-from user-login-form" action="" method="POST">
    															<div class="form-group">
    																<textarea class="form-control" id="exampleFormControlTextarea1" name="comment" rows="7" placeholder="Describe the problem..." style="background-color: aliceblue;height:250px;"></textarea>
    															</div>
    															<button type="submit" name="pw_open" value="open" class="btn btn-primary" style="float:right;"> Submit Ticket</button>
															</form>
														</div>
														
													</div>
												</div>
											</div>
											<br>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>   
            </div>
		</div>
    </div> <!-- .content -->
</div><!-- /#right-panel -->


<?php
}else {
$row = $CheckTx->fetch_assoc();
?>
<?php } ?>