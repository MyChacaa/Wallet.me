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
	$query = $db->query("SELECT * FROM fixed_deposits WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=fixed_deposit_list"); }
	$row = $query->fetch_assoc();
	?>
	

           <div class="col-md-12">
					<div class="card">
                        <div class="card-body">
                            <?php
                            $pid = $row['plan_id'];
                            $pQuery = $db->query("SELECT * FROM fixed_deposit_plans WHERE id='$pid'");
                            $pq = $pQuery->fetch_assoc();
                            
                            $FormBTN = protect($_POST['cancel']);
                            if($FormBTN == "cancel") {
                                $txid = strtoupper(randomHash(10));
                                $time = time();
                                $description = "Fixed Deposit request has been cancelled.";
                                
                                PW_UpdateUserWallet($row['uid'],$row['amount'],$row['currency'],1);
                                $create_transaction = $db->query("INSERT transactions (txid,type,sender,description,amount,currency,status,created) 
                                VALUES ('$txid','53','$row[uid]','$description','$row[amount]','$row[currency]','1','$time')");
                                
                                $insert_activity = $db->query("INSERT activity (txid,type,uid,amount,currency,status,created) 
                                VALUES ('$txid','53','$row[uid]','$row[amount]','$row[currency]','1','$time')");
                                $update = $db->query("UPDATE fixed_deposits SET status='3' WHERE id='$row[id]'");
                                $query = $db->query("SELECT * FROM fixed_deposits WHERE id='$id'");
                                $row = $query->fetch_assoc();
                                echo success("Fixed Deposit was cancelled and refunded successfully.");
                            }
                            ?>

                            <table class="table table-striped">
                                <tbody>
                                <tr>
                                        <td>Transaction ID:</td>
                                        <td><?php echo filter_var($row['txid'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                <tr>
                                        <td>Plan Name:</td>
                                        <td><?php echo filter_var($pq['name'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <tr>
                                        <td>User:</td>
                                        <td><a href="./?a=users&b=edit&id=<?php echo filter_var($row['uid'], FILTER_SANITIZE_STRING); ?>"><?php echo idinfo($row['uid'],"email"); ?></a></td>
                                    </tr>
                                    <tr>
                                        <td>Duration:</td>
                                        <td><?php echo filter_var($row['duration'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Rate:</td>
                                        <td><?php echo filter_var($row['rate'], FILTER_SANITIZE_STRING); ?>%</td>
                                    </tr>
                                    <tr>
                                        <td>Amount:</td>
                                        <td><?= $row['currency'] ?> <?= $row['amount'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Amount Return:</td>
                                        <td><?= $row['currency'] ?> <?= $row['total_return'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Start/End:</td>
                                        <td><?= $row['date_activation'] ?> / <?= $row['date_finish'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Created at:</td>
                                        <td><?php if($row['created_at']>0) { echo date("d/m/Y H:i:s",$row['created_at']); } else { echo 'n/a'; } ?></td>
                                    </tr>
                                    <tr>
                                        <td>Processed at:</td>
                                        <td><?php if($row['proceed_at']>0) { echo date("d/m/Y H:i:s",$row['proceed_at']); } else { echo 'n/a'; } ?></td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>
                                            <?php if ($row['status'] == "1") { ?>
                                                    <span class="badge badge-warning">Waiting</span><!--1-->
                                            <?php } elseif ($row['status'] == "2") { ?>
                                                    <span class="badge badge-success">Completed</span><!--2-->
                                            <?php } else { ?>
                                                    <span class="badge badge-danger">Cancelled</span> <!--3-->
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <form action="" method="POST">
                                <?php if ($row['status'] == "1") { ?>
                                <button class="btn btn-danger" type="submit" value="cancel" name="cancel">Cancel & Refund</button>
                                <?php } ?>
                            </form>
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
                                <input type="text" class="form-control" name="txid" placeholder="Enter TXID" value="<?php if(isset($_POST['txid'])) { echo filter_var($_POST['txid'], FILTER_SANITIZE_STRING); } ?>">
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
                                    <th>Email</th>
                                    <th>Amount</th>
                                    <th>Amount Return</th>
                                    <th>Start/End</th>
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
                                            $search_query[] = "uid='$s_uid'";
                                        }
                                    }
                                    $s_txid = protect($_POST['txid']);
                                    if(!empty($s_txid)) { $search_query[] = "number='$s_txid'"; }
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
                                    $statement = "fixed_deposits WHERE $p_query";
                                    $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC");
                                } else {
                                    $statement = "fixed_deposits";
                                    $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                                }
                                if($query->num_rows>0) {
                                    while($row = $query->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td><a href="./?a=users&b=edit&id=<?= $row['uid']; ?>"><?= idinfo($row['uid'],"email"); ?></a></td>
                                            <td><?= $row['currency']; ?> <?= $row['amount']; ?></td>
                                            <td><?= $row['currency']; ?> <?= $row['total_return']; ?></td>
                                            <td><?= $row['date_activation']; ?> / <?= $row['date_finish']; ?></td>
                                            <td>
                                                <?php if ($row['status'] == "1") { ?>
                                                        <span class="badge badge-warning">Waiting</span><!--1-->
                                                <?php } elseif ($row['status'] == "2") { ?>
                                                        <span class="badge badge-success">Completed</span><!--2-->
                                                <?php } else { ?>
                                                        <span class="badge badge-danger">Cancelled</span> <!--3-->
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <a href="./?a=fixed_deposit_list&b=view&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="View"><span class="badge badge-primary"><i class="fa fa-search"></i> View</span></a> 
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    if($searching == "1") {
                                        echo '<tr><td colspan="6">No found results.</td></tr>';
                                    } else {
                                        echo '<tr><td colspan="6">No have Fixed Deposit yet.</td></tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        if($searching == "0") {
                            $ver = "./?a=fixed_deposit_list";
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