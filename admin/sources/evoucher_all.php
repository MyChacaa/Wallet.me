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
	$query = $db->query("SELECT * FROM evoucher WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=evoucher_all"); }
	$row = $query->fetch_assoc();
	?>
	

           <div class="col-md-12">
					<div class="card">
                        <div class="card-body">
                            <?php
                            $FormBTN = protect($_POST['block']);
                            if($FormBTN == "block") {
                                $update = $db->query("UPDATE evoucher SET status='2' WHERE id='$row[id]'");
                                $query = $db->query("SELECT * FROM evoucher WHERE id='$id'");
                                $row = $query->fetch_assoc();
                                echo success("E-Voucher was blocked successfully.");
                            }
                            if($FormBTN == "un_block") {
                                $update = $db->query("UPDATE evoucher SET status='1' WHERE id='$row[id]'");
                                $query = $db->query("SELECT * FROM evoucher WHERE id='$id'");
                                $row = $query->fetch_assoc();
                                echo success("E-Voucher was unblocked successfully.");
                            }
                            ?>

                            <table class="table table-striped">
                                <tbody>
                                <tr>
                                        <td>Transaction ID:</td>
                                        <td><?php echo filter_var($row['txid'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                <tr>
                                        <td>E-Voucher Lable:</td>
                                        <td><?php echo filter_var($row['lable'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Sender:</td>
                                        <td><a href="./?a=users&b=edit&id=<?php echo filter_var($row['uid'], FILTER_SANITIZE_STRING); ?>"><?php echo idinfo($row['uid'],"email"); ?></a></td>
                                    </tr>
                                    <tr>
                                        <td>Balance:</td>
                                        <td><?php echo filter_var($row['amount'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($row['currency'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Date:</td>
                                        <td><?php if($row['created']>0) { echo date("d/m/Y H:i:s",$row['created']); } else { echo 'n/a'; } ?></td>
                                    </tr>
                                    <tr>
                                        <td>E-Voucher #:</td>
                                        <td><?php echo filter_var($row['number'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Activation #:</td>
                                        <td><?php echo filter_var($row['activation'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>
                                            <?php if ($row['status'] == "1") { ?>
                                                    <span class="badge badge-success">Active</span><!--1-->
                                            <?php } elseif ($row['status'] == "3") { ?>
                                                    <span class="badge badge-danger">Redeemed</span><!--3-->
                                            <?php } else { ?>
                                                    <span class="badge badge-danger">Block</span> <!--2-->
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <form action="" method="POST">
                                <?php if ($row['status'] == "1") { ?>
                                <button class="btn btn-danger" type="submit" value="block" name="block">Block Card</button>
                                <?php } ?>
                                <?php if ($row['status'] == "2") { ?>
                                <button class="btn btn-danger" type="submit" value="un_block" name="block">Unblock Card</button>
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
                                <input type="text" class="form-control" name="txid" placeholder="E-Voucher Number" value="<?php if(isset($_POST['txid'])) { echo filter_var($_POST['txid'], FILTER_SANITIZE_STRING); } ?>">
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
                                    <th width="25%">Email</th>
                                    <th>Voucher #</th>
                                    <th width="15%">Balance</th>
                                    <th width="15%">Status</th>
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
                                    $statement = "evoucher WHERE $p_query";
                                    $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC");
                                } else {
                                    $statement = "evoucher";
                                    $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                                }
                                if($query->num_rows>0) {
                                    while($row = $query->fetch_assoc()) {
                                        ?>
                                        <tr>
                                        <td><a href="./?a=users&b=edit&id=<?= $row['uid']; ?>"><?= idinfo($row['uid'],"email"); ?></a></td>
                                        <td><?= $row['number']; ?></td>
                                            <td><?php echo filter_var($row['amount'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($row['currency'], FILTER_SANITIZE_STRING); ?></td>
                                            <td>
                                                <?php if ($row['status'] == "1") { ?>
                                                        <span class="badge badge-success">Active</span><!--1-->
                                                <?php } elseif ($row['status'] == "3") { ?>
                                                        <span class="badge badge-danger">Redeemed</span><!--3-->
                                                <?php } else { ?>
                                                        <span class="badge badge-danger">Block</span> <!--2-->
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <a href="./?a=evoucher_all&b=view&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="View"><span class="badge badge-primary"><i class="fa fa-search"></i> View</span></a> 
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    if($searching == "1") {
                                        echo '<tr><td colspan="6">No found results.</td></tr>';
                                    } else {
                                        echo '<tr><td colspan="6">No have E-Vouchers yet.</td></tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        if($searching == "0") {
                            $ver = "./?a=evoucher_all";
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