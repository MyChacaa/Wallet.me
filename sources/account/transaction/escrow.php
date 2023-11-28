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
$query_escrow = $db->query("SELECT * FROM escrow_open WHERE txid='$id'");
$escrow = $query_escrow->fetch_assoc();
?>
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Escrow Payment</h6>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php
                    
                    if (isset($_POST['close'])) {
                        $update = $db->query("UPDATE escrow_open SET status='3' WHERE txid='$id'");
                        $update = $db->query("UPDATE escrow SET status='3' WHERE txid='$id'");
                        $query_escrow = $db->query("SELECT * FROM escrow_open WHERE txid='$id'");
                        $escrow = $query_escrow->fetch_assoc();
                        $query = $db->query("SELECT * FROM transactions WHERE txid='$id' and sender='$_SESSION[pw_uid]' or txid='$id' and recipient='$_SESSION[pw_uid]'");
                        $row = $query->fetch_assoc();
                        echo success("Escrow close request has been sended to receiver.");
                    }
                    if (isset($_POST['confirm_close'])) {
                        $update = $db->query("UPDATE escrow_open SET status='2' WHERE txid='$id'");
                        $update = $db->query("UPDATE escrow SET status='2' WHERE txid='$id'");
                        $update = $db->query("UPDATE activity SET status='2' WHERE txid='$id'");
		                $update = $db->query("UPDATE transactions SET status='2' WHERE txid='$id'");
		                PW_UpdateUserWallet($escrow['sender_uid'],$escrow['amount'],$escrow['currency'],1); //Sender will be added by
		                $query_escrow = $db->query("SELECT * FROM escrow_open WHERE txid='$id'");
                        $escrow = $query_escrow->fetch_assoc();
                        $query = $db->query("SELECT * FROM transactions WHERE txid='$id' and sender='$_SESSION[pw_uid]' or txid='$id' and recipient='$_SESSION[pw_uid]'");
                        $row = $query->fetch_assoc();
                        echo success("Escrow has been closed and Amount has been refunded to the Sender.");
                    }
                    if (isset($_POST['confirm_delivery'])) {
                        $update = $db->query("UPDATE escrow_open SET status='5' WHERE txid='$id'");
                        $update = $db->query("UPDATE escrow SET status='5' WHERE txid='$id'");
                        $query_escrow = $db->query("SELECT * FROM escrow_open WHERE txid='$id'");
                        $escrow = $query_escrow->fetch_assoc();
                        $query = $db->query("SELECT * FROM transactions WHERE txid='$id'");
                        $row = $query->fetch_assoc();
                        echo success("You have confirmed delivery.");
                    }
                    if (isset($_POST['release_pay'])) {
                        PW_UpdateUserWallet($escrow['uid'],$escrow['amount'],$escrow['currency'],1); //Receiver will be added by
                        $update = $db->query("UPDATE escrow_open SET status='1' WHERE txid='$id'");
                        $update = $db->query("UPDATE escrow SET status='1' WHERE txid='$id'");
                        $update = $db->query("UPDATE activity SET status='1' WHERE txid='$id'");
		                $update = $db->query("UPDATE transactions SET status='1' WHERE txid='$id'");
                        $query_escrow = $db->query("SELECT * FROM escrow_open WHERE txid='$id'");
                        $escrow = $query_escrow->fetch_assoc();
                        $query = $db->query("SELECT * FROM transactions WHERE txid='$id'");
                        $row = $query->fetch_assoc();
                        echo success("Amount has been released to Seller / Receiver of Payment.");
                    }
                    if (isset($_POST['dispute_seller'])) {
                        $comment_seller = addslashes($_POST['comment_seller']);
                        if(empty($comment_seller)) {
                            echo error($lang['error_4']);
                        } else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $comment_seller)) {
                            echo error("Invalid Characters are not allowed.");
                        } else {
                            $time = time();
                            $update = $db->query("UPDATE escrow_open SET status='6',dispute_seller='$comment_seller' WHERE txid='$id'");
                            $update = $db->query("UPDATE escrow SET status='6',dispute_seller='$comment_seller' WHERE txid='$id'");
                            $update = $db->query("UPDATE activity SET status='3' WHERE txid='$id'");
    		                $update = $db->query("UPDATE transactions SET status='3' WHERE txid='$id'");
                            $query_escrow = $db->query("SELECT * FROM escrow_open WHERE txid='$id'");
                            $escrow = $query_escrow->fetch_assoc();
                            $query = $db->query("SELECT * FROM transactions WHERE txid='$id'");
                            $row = $query->fetch_assoc();
                            echo success("Dispute was Escalated for $settings[name] team review.");
                        }
                    }
                    if (isset($_POST['dispute_buyer_r'])) {
                        $comment_buyer = addslashes($_POST['comment_buyer']);
                        if(empty($comment_buyer)) {
                            echo error($lang['error_4']);
                        } else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $comment_buyer)) {
                            echo error("Invalid Characters are not allowed.");
                        } else {
                            $time = time();
                            $update = $db->query("UPDATE escrow_open SET status='7',dispute_buyer='$comment_buyer' WHERE txid='$id'");
                            $update = $db->query("UPDATE escrow SET status='7',dispute_buyer='$comment_buyer' WHERE txid='$id'");
                            $update = $db->query("UPDATE activity SET status='3' WHERE txid='$id'");
    		                $update = $db->query("UPDATE transactions SET status='3' WHERE txid='$id'");
                            $query_escrow = $db->query("SELECT * FROM escrow_open WHERE txid='$id'");
                            $escrow = $query_escrow->fetch_assoc();
                            $query = $db->query("SELECT * FROM transactions WHERE txid='$id'");
                            $row = $query->fetch_assoc();
                            echo success("Dispute was Escalated for $settings[name] team review.");
                        }
                    }
                    if (isset($_POST['dispute_buyer'])) {
                        $comment_buyer = addslashes($_POST['comment_buyer']);
                        if(empty($comment_buyer)) {
                            echo error($lang['error_4']);
                        } else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $comment_buyer)) {
                            echo error("Invalid Characters are not allowed.");
                        } else {
                            $time = time();
                            $update = $db->query("UPDATE escrow_open SET status='8',dispute_buyer='$comment_buyer' WHERE txid='$id'");
                            $update = $db->query("UPDATE escrow SET status='8',dispute_buyer='$comment_buyer' WHERE txid='$id'");
                            $update = $db->query("UPDATE activity SET status='3' WHERE txid='$id'");
    		                $update = $db->query("UPDATE transactions SET status='3' WHERE txid='$id'");
                            $query_escrow = $db->query("SELECT * FROM escrow_open WHERE txid='$id'");
                            $escrow = $query_escrow->fetch_assoc();
                            $query = $db->query("SELECT * FROM transactions WHERE txid='$id'");
                            $row = $query->fetch_assoc();
                            echo success("Dispute was Escalated for $settings[name] team review.");
                        }
                    }
                    if (isset($_POST['dispute_seller_r'])) {
                        $comment_seller = addslashes($_POST['comment_seller']);
                        if(empty($comment_seller)) {
                            echo error($lang['error_4']);
                        } else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $comment_seller)) {
                            echo error("Invalid Characters are not allowed.");
                        } else {
                            $time = time();
                            $update = $db->query("UPDATE escrow_open SET status='9',dispute_seller='$comment_seller' WHERE txid='$id'");
                            $update = $db->query("UPDATE escrow SET status='9',dispute_seller='$comment_seller' WHERE txid='$id'");
                            $update = $db->query("UPDATE activity SET status='3' WHERE txid='$id'");
    		                $update = $db->query("UPDATE transactions SET status='3' WHERE txid='$id'");
                            $query_escrow = $db->query("SELECT * FROM escrow_open WHERE txid='$id'");
                            $escrow = $query_escrow->fetch_assoc();
                            $query = $db->query("SELECT * FROM transactions WHERE txid='$id'");
                            $row = $query->fetch_assoc();
                            echo success("Dispute was Escalated for $settings[name] team review.");
                        }
                    }
                    ?>
                    <div class="col-md-12">
                        <p><b>
                        <?php
                        if($_SESSION['pw_uid'] == $row['sender']) {
                            if(idinfo($row['recipient'],"account_type") == "1") { $name = idinfo($row['recipient'],"first_name")." ".idinfo($row['recipient'],"last_name"); } else { $name = idinfo($row['recipient'],"business_name"); } 
                            echo $lang['payment_sent_to'].' '.$name;
                        } else {
                            if(idinfo($row['sender'],"account_type") == "1") { $name = idinfo($row['sender'],"first_name")." ".idinfo($row['sender'],"last_name"); } else { $name = idinfo($row['sender'],"business_name"); } 
                            echo $lang['payment_received_from'].' '.$name;    
                        }
                        ?>
                        </b>    <span class="float-right"><?php echo $lang['gross_amount']; ?></span><p>
                        <p><?php echo $lang['payment_status']; ?>: <?php echo PW_DecodeTXStatus($row['status']); ?> <span class="float-right"><span style="font-size:22px;"><?php echo $row['amount']; ?> <?php echo $row['currency']; ?></span></span></p>
                        <p><?php echo $lang['transaction_id']; ?>: <?php echo $row['txid']; ?></p>
                        <p><?php echo $lang['payment_date']; ?>: <?php echo date("d M Y H:i",$row['created']); ?></p>
                        <?php if($row['description']) { ?>
                        <hr/>
                        <p><strong>Payment Terms & Description:</strong> 
                        <br><?php echo $row['description']; ?>
                        <?php } ?>
                        <hr/>
                    </div>
                    <div class="col-md-6">
                        <h4><?php echo $lang['sender']; ?></h4>
                        <div class="card-body">
                            <?php if(idinfo($row['sender'],"account_type") == "1") { echo idinfo($row['sender'],"first_name")." ".idinfo($row['sender'],"last_name"); } else { echo idinfo($row['sender'],"business_name"); } ?> ( <?php if(idinfo($row['sender'],"document_verified") == "1") { echo 'Verified'; } else { echo 'Not Verified'; } ?> )<br/>
                            <?php echo idinfo($row['sender'],"email"); ?><br/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4><?php echo $lang['recipient']; ?></h4>
                        <div class="card-body">
                        <?php if(idinfo($row['recipient'],"account_type") == "1") { echo idinfo($row['recipient'],"first_name")." ".idinfo($row['recipient'],"last_name"); } else { echo idinfo($row['recipient'],"business_name"); } ?> ( <?php if(idinfo($row['sender'],"document_verified") == "1") { echo 'Verified'; } else { echo 'Not Verified'; } ?> )<br/>
                            <?php echo idinfo($row['recipient'],"email"); ?><br/>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <hr/>
                    </div>
                    <div class="col-md-6">
                        <h6><?php echo $lang['payment_details']; ?></h6>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td><?php echo $lang['gross_amount']; ?>: <span class="float-right"><?php echo $row['amount']; ?> <?php echo $row['currency']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $settings['name']; ?> <?php echo $lang['transaction_fee']; ?>: <span class="float-right"><?php echo $row['fee']; ?> <?php echo $row['currency']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lang['net_amount']; ?>: <span class="float-right"><?php echo $row['amount']-$row['fee']; ?> <?php echo $row['currency']; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Escrow Options</h6>
                        <?php if ($escrow['status'] == "1") { ?>
                        <div class="alert alert-success success" style="color:white;">
                            <i class="fa fa-info-circle"></i> Escrow Payment has been Completed.
                        </div>
                        <?php } ?>
                        <?php if ($escrow['status'] == "2") { ?>
                        <div class="alert alert-danger danger" style="color:white;">
                            <i class="fa fa-info-circle"></i> Escrow Payment has been closed.
                        </div>
                        <?php } ?>
                        <form action="" method="POST">
                        <?php if ($escrow['status'] == "4") { ?>
                            <?php if ($_SESSION['pw_uid'] !== $escrow['uid']) { ?>
                                <div class="alert alert-info info" style="color:white;">
                                    <i class="fa fa-info-circle"></i> Awaiting confirmation from seller / receiver of the payment.
                                </div>
                                <button type="submit" class="btn btn-block bg-gradient-danger" name="close">Close Escrow</button>
                            <?php } ?>
                            <?php if ($_SESSION['pw_uid'] == $escrow['uid']) { ?>
                                <button type="submit" class="btn btn-block bg-gradient-success" name="confirm_delivery">Confirm Delivery</button>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($escrow['status'] == "3") { ?>
                            <?php if ($_SESSION['pw_uid'] !== $escrow['uid']) { ?>
                                <div class="alert alert-info info" style="color:white;">
                                    <i class="fa fa-info-circle"></i> Wait some time, If Seller/ Receiver of Payment was not accept the close request then dispute It.
                                </div>
                                <button type="button" class="btn btn-block bg-gradient-warning" data-bs-toggle="collapse" data-bs-target="#Buyer_Dispute" aria-expanded="false" aria-controls="Buyer_Dispute">Open Dispute</button>
                            <?php } ?>
                            <?php if ($_SESSION['pw_uid'] == $escrow['uid']) { ?>
                                <div class="alert alert-info info" style="color:white;">
                                    <i class="fa fa-info-circle"></i> You have received escrow close request from buyer/sender of payment. Accept the Request OR Dispute It.
                                </div>
                                <button type="submit" class="btn btn-block bg-gradient-danger" name="confirm_close">Accept Close Request</button>
                                <button type="button" class="btn btn-block bg-gradient-info" data-bs-toggle="collapse" data-bs-target="#Seller_Dispute" aria-expanded="false" aria-controls="Seller_Dispute">Open Dispute</button>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($escrow['status'] == "5") { ?>
                            <?php if ($_SESSION['pw_uid'] !== $escrow['uid']) { ?>
                                <div class="alert alert-info info" style="color:white;">
                                    <i class="fa fa-info-circle"></i> Seller / Receiver of Payment has confirmed the delivery. If you are satisfied release the payment else dispute It.
                                </div>
                                <button type="submit" class="btn btn-block bg-gradient-success" name="release_pay">Release Payment</button>
                                <button type="button" class="btn btn-block bg-gradient-danger" data-bs-toggle="collapse" data-bs-target="#Buyer_Dispute" aria-expanded="false" aria-controls="Buyer_Dispute">Open Dispute</button>
                            <?php } ?>
                            <?php if ($_SESSION['pw_uid'] == $escrow['uid']) { ?>
                                <div class="alert alert-info info" style="color:white;">
                                    <i class="fa fa-info-circle"></i> Awaiting confirmation from Buyer/ Sender of Payment, If the buyer not confirm you can open dispute.
                                </div>
                                <button type="button" class="btn btn-block bg-gradient-danger" data-bs-toggle="collapse" data-bs-target="#Seller_Dispute" aria-expanded="false" aria-controls="Seller_Dispute">Open Dispute</button>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($escrow['status'] == "6") { ?>
                            <?php if ($_SESSION['pw_uid'] !== $escrow['uid']) { ?>
                                <div class="alert alert-info info" style="color:white;">
                                    <i class="fa fa-info-circle"></i> Seller / Receiver of Payment has disputed the payment. Please response the dispute under 24 hours.
                                </div>
                                <button type="button" class="btn btn-block bg-gradient-danger" data-bs-toggle="collapse" data-bs-target="#Buyer_Dispute" aria-expanded="false" aria-controls="Buyer_Dispute"><?php if ($escrow['dispute_seller']) { ?>Enter Response<?php } else { ?>Open Dispute<?php } ?></button>
                            <?php } ?>
                            <?php if ($_SESSION['pw_uid'] == $escrow['uid']) { ?>
                                <div class="alert alert-info info" style="color:white;">
                                    <i class="fa fa-info-circle"></i> Escalated for review by <?=$settings['name']?> team.
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($escrow['status'] == "7") { ?>
                            <?php if ($_SESSION['pw_uid'] !== $escrow['uid']) { ?>
                                <div class="alert alert-info info" style="color:white;">
                                    <i class="fa fa-info-circle"></i> Escalated for review by <?=$settings['name']?> team. If more info will required team will contact you via Email.
                                </div>
                            <?php } ?>
                            <?php if ($_SESSION['pw_uid'] == $escrow['uid']) { ?>
                                <div class="alert alert-info info" style="color:white;">
                                    <i class="fa fa-info-circle"></i> Escalated for review by <?=$settings['name']?> team. If more info will required team will contact you via Email.
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($escrow['status'] == "8") { ?>
                            <?php if ($_SESSION['pw_uid'] !== $escrow['uid']) { ?>
                                <div class="alert alert-info info" style="color:white;">
                                    <i class="fa fa-info-circle"></i> Escalated for review by <?=$settings['name']?> team.
                                </div>
                            <?php } ?>
                            <?php if ($_SESSION['pw_uid'] == $escrow['uid']) { ?>
                                <div class="alert alert-info info" style="color:white;">
                                    <i class="fa fa-info-circle"></i> Buyer / Sender of Payment has disputed the payment. Please response the dispute under 24 hours.
                                </div>
                                <button type="button" class="btn btn-block bg-gradient-danger" data-bs-toggle="collapse" data-bs-target="#Seller_Dispute" aria-expanded="false" aria-controls="Seller_Dispute"><?php if ($escrow['dispute_buyer']) { ?>Enter Response<?php } else { ?>Open Dispute<?php } ?></button>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($escrow['status'] == "9") { ?>
                            <?php if ($_SESSION['pw_uid'] !== $escrow['uid']) { ?>
                                <div class="alert alert-info info" style="color:white;">
                                    <i class="fa fa-info-circle"></i> Escalated for review by <?=$settings['name']?> team. If more info will required team will contact you via Email.
                                </div>
                            <?php } ?>
                            <?php if ($_SESSION['pw_uid'] == $escrow['uid']) { ?>
                                <div class="alert alert-info info" style="color:white;">
                                    <i class="fa fa-info-circle"></i> Escalated for review by <?=$settings['name']?> team. If more info will required team will contact you via Email.
                                </div>
                            <?php } ?>
                        <?php } ?>
                        </form>
                        <?php if ($escrow['dispute_buyer']) { ?>
                        <div class="collapse" id="Seller_Dispute">
                          <div class="card card-body bg-dark">
                            <label class="text-white">Dispute by Buyer</label>
                            <div class="form-group">
								<textarea class="form-control" disabled><?= $escrow['dispute_buyer']; ?></textarea>
							</div>
                            <form class="user-connected-from user-login-form" action="" method="POST">
								<div class="form-group">
									<textarea class="form-control" id="exampleFormControlTextarea1" name="comment_seller" rows="4" placeholder="Describe the problem..."></textarea>
								</div>
								<button type="submit" name="dispute_seller_r" class="btn btn-info btn-block"> Submit Response</button>
							</form>
                          </div>
                        </div>
                        <?php } else { ?>
                        <div class="collapse" id="Seller_Dispute">
                          <div class="card card-body bg-dark">
                            <form class="user-connected-from user-login-form" action="" method="POST">
								<div class="form-group">
									<textarea class="form-control" id="exampleFormControlTextarea1" name="comment_seller" rows="4" placeholder="Describe the problem..."></textarea>
								</div>
								<button type="submit" name="dispute_seller" class="btn btn-info btn-block"> Submit Dispute</button>
							</form>
                          </div>
                        </div>
                        <?php } ?>
                        <?php if ($escrow['dispute_seller']) { ?>
                        <div class="collapse" id="Buyer_Dispute">
                          <div class="card card-body bg-dark">
                            <label class="text-white">Dispute by Seller</label>
                            <div class="form-group">
								<textarea class="form-control" disabled><?= $escrow['dispute_seller']; ?></textarea>
							</div>
						    <form class="user-connected-from user-login-form" action="" method="POST">
								<div class="form-group">
									<textarea class="form-control" id="exampleFormControlTextarea1" name="comment_buyer" rows="4" placeholder="Response to seller..."></textarea>
								</div>
								<button type="submit" name="dispute_buyer_r" class="btn btn-info btn-block"> Submit Response</button>
							</form>
                          </div>
                        </div>
                        <?php } else { ?>
                        <div class="collapse" id="Buyer_Dispute">
                          <div class="card card-body bg-dark">
                            <form class="user-connected-from user-login-form" action="" method="POST">
								<div class="form-group">
									<textarea class="form-control" id="exampleFormControlTextarea1" name="comment_buyer" rows="4" placeholder="Describe the problem..."></textarea>
								</div>
								<button type="submit" name="dispute_buyer" class="btn btn-info btn-block"> Submit Dispute</button>
							</form>
                          </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>