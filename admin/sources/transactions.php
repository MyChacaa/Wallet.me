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
	$query = $db->query("SELECT * FROM transactions WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=transactions"); }
	$row = $query->fetch_assoc();
	?>
	

           <div class="col-md-12">
					<div class="card">
                        <div class="card-body">
                            <?php
                            $FormBTN = protect($_POST['btn_action']);
                            if($FormBTN == "process_withdrawal") {
                                $row['status'] = '3';
                                $update = $db->query("UPDATE withdrawals SET status='3' WHERE id='$row[id]'");
                                $update = $db->query("UPDATE activity SET status='1' WHERE txid='$row[txid]'");
                                $update = $db->query("UPDATE transactions SET status='1' WHERE txid='$row[txid]'");
                                $account = idinfo($row['uid'],"email");
                                echo success("Withdrawal was completed successfully.");
                            }

                            if($FormBTN == "cancel_withdrawal") {
                                $row['status'] = '2';
                                PW_UpdateUserWallet($row['uid'],$row['amount'],$row['currency'],1);
                                $update = $db->query("UPDATE withdrawals SET status='2' WHERE id='$row[id]'");
                                $update = $db->query("UPDATE activity SET status='2' WHERE txid='$row[txid]'");
                                $update = $db->query("UPDATE transactions SET status='2' WHERE txid='$row[txid]'");
                                echo success("Withdrawal was canceled successfully.");
                            }
                            ?>

                            <table class="table table-striped">
                                <tbody>
                                <tr>
                                        <td>Transaction ID:</td>
                                        <td><?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
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
                                        <td><?php echo filter_var($settings['name'], FILTER_SANITIZE_STRING); ?> transaction fee:</td>
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
                                    <?php if($row['item_id']) { ?>
                                        <tr>
                                        <td>Item Number:</td>
                                        <td><?php if($row['item_id']) { echo filter_var($row['item_id'], FILTER_SANITIZE_STRING); } else { echo 'n/a'; }  ?></td>
                                    </tr>
                                    <tr>
                                        <td>Item Name:</td>
                                        <td><?php if($row['item_name']) { echo filter_var($row['item_name'], FILTER_SANITIZE_STRING); } else { echo 'n/a'; }  ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            
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
                                    <th width="25%">Sender</th>
                                    <th>Recipient</th>
                                    <th width="15%">Amount</th>
                                    <th width="15%">Transaction ID</th>
                                    <th width="15%">Action</th>
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
                                    $statement = "transactions WHERE $p_query";
                                    $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC");
                                } else {
                                    $statement = "transactions WHERE type='1'";
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
                                                <a href="./?a=transactions&b=view&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="View"><span class="badge badge-primary"><i class="fa fa-search"></i> View</span></a> 
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    if($searching == "1") {
                                        echo '<tr><td colspan="6">No found results.</td></tr>';
                                    } else {
                                        echo '<tr><td colspan="6">No have transactions yet.</td></tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        if($searching == "0") {
                            $ver = "./?a=transactions";
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