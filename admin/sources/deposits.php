<?php
// eWallet - PHP Script
// Author: DeluxeScript
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
	$query = $db->query("SELECT * FROM deposits WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=deposits"); }
	$row = $query->fetch_assoc();
	?>
	

           <div class="col-md-12">
					<div class="card">
                        <div class="card-body">
                            <?php
                            $FormBTN = protect($_POST['btn_action']);
                            if($FormBTN == "process_deposit") {
                                $row['status'] = '1';
                                PW_UpdateUserWallet($row['uid'],$row['amount'],$row['currency'],1);
                                $update = $db->query("UPDATE deposits SET status='1' WHERE id='$row[id]'");
                                $update = $db->query("UPDATE activity SET status='1' WHERE txid='$row[txid]'");
                                $update = $db->query("UPDATE transactions SET status='1' WHERE txid='$row[txid]'");
                                $account = idinfo($row['uid'],"email");
                                echo success("$row[amount] $row[currency] was debited to <b>$account</b> account.");
                            }

                            if($FormBTN == "cancel_deposit") {
                                $row['status'] = '2';
                                $update = $db->query("UPDATE deposits SET status='2' WHERE id='$row[id]'");
                                $update = $db->query("UPDATE activity SET status='2' WHERE txid='$row[txid]'");
                                $update = $db->query("UPDATE transactions SET status='2' WHERE txid='$row[txid]'");
                                echo success("Deposit was canceled successfully.");
                            }
                            ?>

                            <table class="table table-striped">
                                <tbody>
                                <tr>
                                        <td>Deposit ID:</td>
                                        <td><?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                <tr>
                                        <td>Deposit Hash:</td>
                                        <td><?php echo filter_var($row['txid'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <tr>
                                        <td>User:</td>
                                        <td><a href="./?a=users&b=edit&id=<?php echo filter_var($row['uid'], FILTER_SANITIZE_STRING); ?>"><?php echo idinfo($row['uid'],"email"); ?></a></td>
                                    </tr>
                                    <tr>
                                        <td>Amount:</td>
                                        <td><?php echo filter_var($row['amount'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($row['currency'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Gateway:</td>
                                        <td><?php echo gatewayinfo($row['method'],"name"); ?></td>
                                    </tr>
                                        <tr>
                                        <td>Reference Number:</td>
                                        <td><?php echo filter_var($row['reference_number'], FILTER_SANITIZE_STRING);  ?></td>
                                    </tr>
                                    <tr>
                                        <td>Gateway Transaction ID:</td>
                                        <td><?php if($row['gateway_txid']) { echo filter_var($row['gateway_txid'], FILTER_SANITIZE_STRING); } else { echo 'n/a'; } ?></td>
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
                                                if($status == "1") {
                                                    echo '<span class="badge badge-success">Completed</span>';
                                                } elseif($status == "2") {
                                                    echo '<span class="badge badge-danger">Canceled</span>';
                                                } elseif($status == "3") {
                                                    echo '<span class="badge badge-warning">Pending</span>';
                                                } else { }
                                                ?>
                                            </td>
                                    </tr>
                                </tbody>
                            </table>

                            <?php if($row['status'] == "3") { ?>
                            <form action="" method="POST">
                                <button type="submit" class="btn btn-success" name="btn_action" value="process_deposit">Accept Deposit</button> 
                                <button type="submit" class="btn btn-danger" name="btn_action" value="cancel_deposit">Cancel Deposit</button>
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
                        <div class="col-md-3" style="padding:10px;">
                                <input type="text" class="form-control" name="txid" placeholder="Deposit ID" value="<?php if(isset($_POST['txid'])) { echo filter_var($_POST['txid'], FILTER_SANITIZE_STRING); } ?>">
                            </div>
                            <div class="col-md-3" style="padding:10px;">
                                <input type="text" class="form-control" name="rn" placeholder="Reference Number" value="<?php if(isset($_POST['rn'])) { echo filter_var($_POST['rn'], FILTER_SANITIZE_STRING); } ?>">
                            </div>
                            <div class="col-md-4" style="padding:10px;">
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
                                    <th width="25%">User</th>
                                    <th width="15%">Amount</th>
                                    <th width="15%">Deposit ID</th>
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
                                    $s_rn = protect($_POST['rn']);
                                    if(!empty($s_rn)) { $search_query[] = "reference_number='$s_rn'"; }
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
                                $statement = "deposits";
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
                                            <td><a href="./?a=users&b=edit&id=<?php echo filter_var($row['uid'], FILTER_SANITIZE_STRING); ?>"><?php echo idinfo($row['uid'],"email"); ?></a></td>
                                            <td><?php echo filter_var($row['amount'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($row['currency'], FILTER_SANITIZE_STRING); ?></td>
                                            <td><?php echo filter_var($row['txid'], FILTER_SANITIZE_STRING); ?></td>
                                            <td><?php echo gatewayinfo($row['method'],"name"); ?></td>
                                            <td>
                                                <?php
                                                $status = $row['status'];
                                                if($status == "1") {
                                                    echo '<span class="badge badge-success">Completed</span>';
                                                } elseif($status == "2") {
                                                    echo '<span class="badge badge-danger">Canceled</span>';
                                                } elseif($status == "3") {
                                                    echo '<span class="badge badge-warning">Pending</span>';
                                                } else { }
                                                ?>
                                            </td>
                                            <td>
                                                <a href="./?a=deposits&b=view&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="View"><span class="badge badge-primary"><i class="fa fa-search"></i> View</span></a> 
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    if($searching == "1") {
                                        echo '<tr><td colspan="6">No found results.</td></tr>';
                                    } else {
                                        echo '<tr><td colspan="6">No have deposits yet.</td></tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        if($searching == "0") {
                            $ver = "./?a=deposits";
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