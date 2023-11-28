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
if ($b == "settings") {
    $escrow_settingsQuery = $db->query("SELECT * FROM escrow_settings ORDER BY id DESC LIMIT 1");
	$escrow_settings = $escrow_settingsQuery->fetch_assoc();
?>
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <strong class="card-title">Escrow <b>Fee Setup</b></strong>
        </div>
        <div class="card-body">
            <?php
    		if(isset($_POST['btn_save'])) {
    		    $payfee_type = protect($_POST['payfee_type']);
    			$payfee_percentage = protect($_POST['payfee_percentage']);
    			$payfee_fixed = protect($_POST['payfee_fixed']);
    			
    			$update = $db->query("UPDATE escrow_settings SET payfee_type='$payfee_type',payfee_percentage='$payfee_percentage',payfee_fixed='$payfee_fixed'");
				$escrow_settingsQuery = $db->query("SELECT * FROM escrow_settings ORDER BY id DESC LIMIT 1");
				$escrow_settings = $escrow_settingsQuery->fetch_assoc();
				echo success("Your changes was saved successfully.");
    		}
    		?>
            <form action="" method="POST">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
            				<label>Who will pay the fee?</label>
            				<select class="form-control" name="payfee_type">
            				    <option value="1" <?php if ($escrow_settings['payfee_type'] == "1") { echo "selected"; } ?> >Sender will pay</option>
            				    <option value="2" <?php if ($escrow_settings['payfee_type'] == "2") { echo "selected"; } ?> >Receiver will pay</option>
            				</select>
            			</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
            				<label>Transaction Fee (Percentage)</label>
            				<input type="text" class="form-control" name="payfee_percentage" value="<?php echo filter_var($escrow_settings['payfee_percentage'], FILTER_SANITIZE_STRING); ?>">
            				<small>Enter transaction fee in percentage without %. This transaction fee will be charged from recipient/sender of amount. Example: 3.4</small>
            			</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
            				<label>Transaction Fee (Fixed Flat)</label>
            				<input type="text" class="form-control" name="payfee_fixed" value="<?php echo filter_var($escrow_settings['payfee_fixed'], FILTER_SANITIZE_STRING); ?>">
            				<small>Enter fixed send/request money fee. Your default currency is <b><?php echo filter_var($settings['default_currency'], FILTER_SANITIZE_STRING); ?></b>, if client send/request in other currency, this amount will be converted automatically.</small>
            			</div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;border-radius:0px;" name="btn_save"><i class="fa fa-check"></i> Save changes</button>
            </form>
        </div>
    </div>
</div>

<?php
} elseif($b == "view") {
	$id = protect($_GET['id']);
	$query = $db->query("SELECT * FROM escrow WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=escrow"); }
	$row = $query->fetch_assoc();
	$query_escrow = $db->query("SELECT * FROM escrow_open WHERE txid='$row[txid]'");
    $escrow = $query_escrow->fetch_assoc();
	?>
	

           <div class="col-md-12">
					<div class="card">
                        <div class="card-body">
                            <?php
                                if (isset($_POST['return_to_buyer'])) {
                                    $update = $db->query("UPDATE escrow_open SET status='2' WHERE txid='$row[txid]'");
                                    $update = $db->query("UPDATE escrow SET status='2' WHERE txid='$row[txid]'");
                                    $update = $db->query("UPDATE activity SET status='2' WHERE txid='$row[txid]'");
            		                $update = $db->query("UPDATE transactions SET status='2' WHERE txid='$row[txid]'");
            		                PW_UpdateUserWallet($escrow['sender_uid'],$escrow['amount'],$escrow['currency'],1); //Sender will be added by
            		                $query = $db->query("SELECT * FROM escrow WHERE id='$id'");
            		                $row = $query->fetch_assoc();
            		                $query_escrow = $db->query("SELECT * FROM escrow_open WHERE txid='$row[txid]'");
                                    $escrow = $query_escrow->fetch_assoc();
            		                echo success("Amount has been Returned to buyer.");
                                }
                                if (isset($_POST['payment_to_seller'])) {
                                    PW_UpdateUserWallet($escrow['uid'],$escrow['amount'],$escrow['currency'],1); //Receiver will be added by
                                    $update = $db->query("UPDATE escrow_open SET status='1' WHERE txid='$row[txid]'");
                                    $update = $db->query("UPDATE escrow SET status='1' WHERE txid='$row[txid]'");
                                    $update = $db->query("UPDATE activity SET status='1' WHERE txid='$row[txid]'");
            		                $update = $db->query("UPDATE transactions SET status='1' WHERE txid='$row[txid]'");
            		                $query = $db->query("SELECT * FROM escrow WHERE id='$id'");
            		                $row = $query->fetch_assoc();
            		                $query_escrow = $db->query("SELECT * FROM escrow_open WHERE txid='$row[txid]'");
                                    $escrow = $query_escrow->fetch_assoc();
            		                echo success("Amount has been Sended to Seller.");
                                }
                            ?>

                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td>Transaction Hash:</td>
                                        <td><?php echo filter_var($row['txid'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Sender:</td>
                                        <td><a href="./?a=users&b=edit&id=<?php echo filter_var($row['sender'], FILTER_SANITIZE_STRING); ?>"><?php echo idinfo($row['sender'],"email"); ?></a></td>
                                    </tr>
                                    <tr>
                                        <td>Recipient:</td>
                                        <td><a href="./?a=users&b=edit&id=<?php echo filter_var($row['recipient'], FILTER_SANITIZE_STRING); ?>"><?php echo idinfo($row['recipient'],"email"); ?></a></td>
                                    </tr>
                                    <tr>
                                        <td>Gross amount:</td>
                                        <td><?php echo filter_var($row['amount'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($row['currency'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo filter_var($settings['name'], FILTER_SANITIZE_STRING); ?> Escrow fee:</td>
                                        <td><?php echo filter_var($row['fee'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($row['currency'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Net amount:</td>
                                        <td><?php echo filter_var($row['amount']-$row['fee'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($row['currency'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Date:</td>
                                        <td><?php if($row['created']>0) { echo date("d/m/Y H:i:s",$row['created']); } else { echo 'n/a'; } ?></td>
                                    </tr>
                                    <tr>
                                        <td>Payment Description:</td>
                                        <td><?php if($row['description']) { echo filter_var($row['description'], FILTER_SANITIZE_STRING); } else { echo 'n/a'; }  ?></td>
                                    </tr>
                                    <tr>
                                        <td>Status:</td>
                                        <td>
                                            <?php
                                            $status = $row['status'];
                                            if ($status == "4") { ?>
                                                <span class="badge badge-pill bg-gradient-warning">Hold</span>
                                            <?php } elseif ($status == "5") { ?>
                                                <span class="badge badge-pill bg-gradient-info">Confirmed by seller</span>
                                            <?php } elseif ($status == "3") { ?>
                                                <span class="badge badge-pill bg-gradient-info">Closed by buyer</span>
                                            <?php } elseif ($status == "2") { ?>
                                                <span class="badge badge-pill bg-gradient-success">Closed</span>
                                            <?php } elseif ($status == "1") { ?>
                                                <span class="badge badge-pill bg-gradient-success">Confirmed</span>
                                            <?php } elseif ($status == "6") { ?>
                                                <span class="badge badge-pill bg-gradient-danger">Dispute by Receiver</span>
                                            <?php } elseif ($status == "7") { ?>
                                                <span class="badge badge-pill bg-gradient-danger">Dispute by Receiver and Responded by Sender</span>
                                            <?php } elseif ($status == "8") { ?>
                                                <span class="badge badge-pill bg-gradient-danger">Dispute by Sender</span>
                                            <?php } elseif ($status == "9") { ?>
                                                <span class="badge badge-pill bg-gradient-danger">Dispute by Sender and Responded by Receiver</span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <?php if ($status == "6" or $status == "7") { ?>
                            <div class="row">
                                <div class="col-md">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6><?php echo idinfo($row['recipient'],"first_name"); ?>`s Dispute Message by Seller</h6>
                                            <br>
                                            <?= $row['dispute_seller'] ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6><?php echo idinfo($row['sender'],"first_name"); ?>`s Dispute Response by Buyer</h6>
                                            <br>
                                            <?= $row['dispute_buyer'] ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <form action="" method="POST">
                                <div class="row">
                                    <div class="col-md">
                                        <button class="btn btn-info" style="width:100%;" type="submit" name="return_to_buyer">Refund to Buyer/Sender of Payment - Transaction Cancel</button>
                                    </div>
                                    <div class="col-md">
                                        <button class="btn btn-warning" type="submit" style="width:100%;" name="payment_to_seller">Payment to Seller - Transaction Success</button>
                                    </div>
                                </div>
                            </form>
                            <?php } ?>
                            <?php if ($status == "8" or $status == "9") { ?>
                            <div class="row">
                                <div class="col-md">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6><?php echo idinfo($row['sender'],"first_name"); ?>`s Dispute Message by Buyer</h6>
                                            <br>
                                            <?= $row['dispute_buyer'] ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6><?php echo idinfo($row['recipient'],"first_name"); ?>`s Dispute Response by Seller</h6>
                                            <br>
                                            <?= $row['dispute_seller'] ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <form action="" method="POST">
                            <div class="row">
                                <div class="col-md">
                                    <button class="btn btn-info" style="width:100%;" type="submit" name="return_to_buyer">Refund to Buyer/Sender of Payment - Transaction Cancel</button>
                                </div>
                                <div class="col-md">
                                    <button class="btn btn-warning" type="submit" style="width:100%;" name="payment_to_seller">Payment Send to Seller/Receiver - Transaction Success</button>
                                </div>
                            </div>
                            </form>
                            <?php } ?>
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
                        <div class="col-md-5" style="padding:10px;">
                                <input type="text" class="form-control" name="txid" placeholder="Transaction ID" value="<?php if(isset($_POST['txid'])) { echo filter_var($_POST['txid'], FILTER_SANITIZE_STRING); } ?>">
                            </div>
                            <div class="col-md-5" style="padding:10px;">
                                <input type="text" class="form-control" name="email" placeholder="Email address" value="<?php if(isset($_POST['email'])) { echo filter_var($_POST['email'], FILTER_SANITIZE_STRING); } ?>">
                            </div>
                            <div class="col-md-2" style="padding:10px;">
                                <button type="submit" class="btn btn-primary btn-block" name="btn_search" value="deposits">Search</button>
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
                                    <th>Sender</th>
                                    <th>Recipient</th>
                                    <th>Amount</th>
                                    <th>Transaction ID</th>
                                    <th>Status</th>
                                    <th>Action</th>
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
                                if($FormBTN == "deposits") {
                                    $searching=1;
                                    $search_query = array();
                                    $s_email = protect($_POST['email']);
                                    if(!empty($s_email)) {
                                        if(PW_GetUserID($s_email)) {
                                            $s_uid = PW_GetUserID($s_email);
                                            $search_query[] = "sender='$s_uid' or recipient='$s_uid'";
                                        }
                                    }
                                    $s_txid = protect($_POST['txid']);
                                    if(!empty($s_txid)) { $search_query[] = "txid='$s_txid'"; }
                                    $search_query[] = "type='1'";
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
                                
                                if($searching==1) {
                                    if(empty($p_query)) {
                                        $qry = 'empty query';
                                    }
                                    $statement = "escrow WHERE $p_query";
                                    $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC");
                                } else {
                                    $statement = "escrow";
                                    $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                                }
                                if($query->num_rows>0) {
                                    while($row = $query->fetch_assoc()) {
                                        ?>
                                        <tr>
                                        <td><a href="./?a=users&b=edit&id=<?php echo filter_var($row['sender'], FILTER_SANITIZE_STRING); ?>"><?php echo idinfo($row['sender'],"email"); ?></a></td>
                                        <td><a href="./?a=users&b=edit&id=<?php echo filter_var($row['recipient'], FILTER_SANITIZE_STRING); ?>"><?php echo idinfo($row['recipient'],"email"); ?></a></td>
                                            <td><?php echo filter_var($row['amount'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($row['currency'], FILTER_SANITIZE_STRING); ?></td>
                                            <td><?php echo filter_var($row['txid'], FILTER_SANITIZE_STRING); ?></td>
                                            <td>
                                                <?php
                                                $status = $row['status'];
                                                if ($status == "4") { ?>
                                                    <span class="badge badge-pill bg-gradient-warning">Hold</span>
                                                <?php } elseif ($status == "5") { ?>
                                                    <span class="badge badge-pill bg-gradient-info">Confirmed by seller</span>
                                                <?php } elseif ($status == "3") { ?>
                                                    <span class="badge badge-pill bg-gradient-info">Closed by buyer</span>
                                                <?php } elseif ($status == "2") { ?>
                                                    <span class="badge badge-pill bg-gradient-success">Closed</span>
                                                <?php } elseif ($status == "1") { ?>
                                                    <span class="badge badge-pill bg-gradient-success">Confirmed</span>
                                                <?php } elseif ($status == "6") { ?>
                                                    <span class="badge badge-pill bg-gradient-danger">Dispute by Receiver</span>
                                                <?php } elseif ($status == "7") { ?>
                                                    <span class="badge badge-pill bg-gradient-danger">Dispute by Receiver and Responded by Sender</span>
                                                <?php } elseif ($status == "8") { ?>
                                                    <span class="badge badge-pill bg-gradient-danger">Dispute by Sender</span>
                                                <?php } elseif ($status == "9") { ?>
                                                    <span class="badge badge-pill bg-gradient-danger">Dispute by Sender and Responded by Receiver</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <a href="./?a=escrow&b=view&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="View"><span class="badge badge-primary"><i class="fa fa-search"></i> View</span></a> 
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    if($searching == "1") {
                                        echo '<tr><td colspan="6">No found results.</td></tr>';
                                    } else {
                                        echo '<tr><td colspan="6">No have escrow yet yet.</td></tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        if($searching == "0") {
                            $ver = "./?a=escrow";
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