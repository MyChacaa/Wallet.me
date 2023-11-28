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
	$query = $db->query("SELECT * FROM withdrawals WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=withdrawals"); }
	$row = $query->fetch_assoc();
	?>
	

           <div class="col-md-12">
					<div class="card">
                        <div class="card-body">
                            <?php
                            if (isset($_POST['btn_action'])){
							$FormBTN = protect($_POST['btn_action']);
							} else {
							$FormBTN = "";
							}
                            if($FormBTN == "process_withdrawal") {
                                $row['status'] = '3';
                                $gateway_txid = protect($_POST['gateway_txid']);
                                $update = $db->query("UPDATE withdrawals SET status='3',gateway_txid='$gateway_txid' WHERE id='$row[id]'");
                                $update = $db->query("UPDATE activity SET status='1' WHERE txid='$row[txid]'");
                                $update = $db->query("UPDATE transactions SET status='1' WHERE txid='$row[txid]'");
                                $account = idinfo($row['uid'],"email");
                                $time = time();
                                PW_UpdateAdminWallet($row['fee'],$row['currency']);
                                $insert_admin_log = $db->query("INSERT admin_logs (type,time,u_field_1,u_field_2,u_field_3) VALUES ('4','$time','$row[fee]','$row[currency]','$row[txid]')");
                                $query = $db->query("SELECT * FROM withdrawals WHERE id='$id'");
                                $row = $query->fetch_assoc();
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
                                        <td>Deposit ID:</td>
                                        <td><?php echo $row['id']; ?></td>
                                    </tr>
                                <tr>
                                        <td>Deposit Hash:</td>
                                        <td><?php echo $row['txid']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>User:</td>
                                        <td><a href="./?a=users&b=edit&id=<?php echo $row['uid']; ?>"><?php echo idinfo($row['uid'],"email"); ?></a></td>
                                    </tr>
                                    <tr>
                                        <td>Amount:</td>
                                        <td><?php echo $row['amount']; ?> <?php echo $row['currency']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Fee:</td>
                                        <td><?php echo $row['fee']; ?> <?php echo $row['currency']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Gross Amount:</td>
                                        <td><?php $g_amount = $row['amount'] - $row['fee'] ; echo $g_amount; ?> <?php echo $row['currency']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Gateway:</td>
                                        <td><?php echo gatewayinfo($row['method'],"name"); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Gateway Transaction ID:</td>
                                        <td><?php if($row['gateway_txid']) { echo $row['gateway_txid']; } else { echo 'n/a'; } ?></td>
                                    </tr>
                                    <tr>
                                        <td>Date:</td>
                                        <td><?php if($row['requested_on']>0) { echo date("d/m/Y H:i:s",$row['requested_on']); } else { echo 'n/a'; } ?></td>
                                    </tr>
                                    <tr>
                                        <td>Processed on:</td>
                                        <td><?php if($row['processed_on']>0) { echo date("d/m/Y H:i:s",$row['processed_on']); } else { echo 'n/a'; } ?></td>
                                    </tr>
                                    <tr>
                                        <td>Status:</td>
                                        <td>
                                                <?php
                                                $status = $row['status'];
                                                if($status == "3") {
                                                    echo '<span class="badge badge-success">Completed</span>';
                                                } elseif($status == "2") {
                                                    echo '<span class="badge badge-danger">Canceled</span>';
                                                } elseif($status == "1") {
                                                    echo '<span class="badge badge-warning">Pending</span>';
                                                } else { }
                                                ?>
                                            </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <div class="card">
                                <div class="card-body">
                                    <h4><?php echo idinfo($row['uid'],"email"); ?>`s Details of the withdrawal</h4>
                                    <br>
                                    <?php
                                    $GetValues = $db->query("SELECT * FROM withdrawals_values WHERE withdrawal_id='$row[id]'");
                                    if($GetValues->num_rows>0) {
                                        while($gv = $GetValues->fetch_assoc()) {
                                            echo "<p>".PW_GetFieldName($gv['field_id']).": <b>".$gv['value']."</b></p>";
                                        }
                                    } else { 
                                        echo 'No data.';
                                    }
                                    ?>
                                </div>
                            </div>

                            <?php if($row['status'] == "1") { ?>
                            <form action="" method="POST">
                                <?php if($row['gateway_txid']) { echo '<input type="text" disabled class="form-control" name="gateway_txid" placeholder="Enter Transaction ID" value="'.$row['gateway_txid'].'">'; } else {  
                                echo'<input type="text" class="form-control" name="gateway_txid" placeholder="Enter Transaction ID" value="'.$row['gateway_txid'].'">'; } ?>
                                <br>
                                <button type="submit" class="btn btn-success" name="btn_action" value="process_withdrawal">Mark as Completed</button> 
                                <button type="submit" class="btn btn-danger" name="btn_action" value="cancel_withdrawal">Cancel Withdrawal</button>
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
                                <input type="text" class="form-control" name="txid" placeholder="Withdrawal ID" value="<?php if(isset($_POST['txid'])) { echo $_POST['txid']; } ?>">
                            </div>
                            <div class="col-md-5" style="padding:10px;">
                                <input type="text" class="form-control" name="email" placeholder="Email address" value="<?php if(isset($_POST['email'])) { echo $_POST['email']; } ?>">
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
                                    <th width="25%">User</th>
                                    <th width="15%">Amount</th>
                                    <th width="15%">Withdrawal ID</th>
                                    <th width="15%">Gateway</th>
                                    <th width="18%">Status</th>
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
                                            $search_query[] = "uid='$s_uid'";
                                        }
                                    }
                                    $s_txid = protect($_POST['txid']);
                                    if(!empty($s_txid)) { $search_query[] = "txid='$s_txid'"; }
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
                                $statement = "withdrawals";
                                if($searching==1) {
                                    if(empty($p_query)) {
                                        $qry = 'empty query';
                                    }
                                    $query = $db->query("SELECT * FROM {$statement} WHERE $p_query ORDER BY id DESC");
                                } else {
                                    $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                                }
                                if($query->num_rows>0) {
                                    while($row = $query->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td><a href="./?a=users&b=edit&id=<?php echo $row['uid']; ?>"><?php echo idinfo($row['uid'],"email"); ?></a></td>
                                            <td><?php echo $row['amount']; ?> <?php echo $row['currency']; ?></td>
                                            <td><?php echo $row['txid']; ?></td>
                                            <td><?php echo gatewayinfo($row['method'],"name"); ?></td>
                                            <td>
                                                <?php
                                                $status = $row['status'];
                                                if($status == "3") {
                                                    echo '<span class="badge badge-success">Completed</span>';
                                                } elseif($status == "2") {
                                                    echo '<span class="badge badge-danger">Canceled</span>';
                                                } elseif($status == "1") {
                                                    echo '<span class="badge badge-warning">Pending</span>';
                                                } else { }
                                                ?>
                                            </td>
                                            <td>
                                                <a href="./?a=withdrawals&b=view&id=<?php echo $row['id']; ?>" title="View"><span class="badge badge-primary"><i class="fa fa-search"></i> View</span></a> 
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    if($searching == "1") {
                                        echo '<tr><td colspan="6">No found results.</td></tr>';
                                    } else {
                                        echo '<tr><td colspan="6">No have withdrawals yet.</td></tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        if($searching == "0") {
                            $ver = "./?a=withdrawals";
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