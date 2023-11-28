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
	$query = $db->query("SELECT * FROM users_converts WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=curencies_log"); }
	$row = $query->fetch_assoc();
	?>
	

                <div class="col-md-12">
					<div class="card">
                        <div class="card-body">
                            <table class="table table-striped">
                                <tbody>
                                <tr>
                                        <td>ID :</td>
                                        <td><?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                <tr>
                                        <td>Exchange ID:</td>
                                        <td><?php echo filter_var($row['txid'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <tr>
                                        <td>User:</td>
                                        <td><a href="./?a=users&b=edit&id=<?php echo filter_var($row['uid'], FILTER_SANITIZE_STRING); ?>"><?php echo idinfo($row['uid'],"email"); ?></a></td>
                                    </tr>
                                    <tr>
                                        <td>Way of Exchange:</td>
                                        <td><?php echo $row['from_amount']; ?> <?php echo $row['from_currency']; ?> - <?php echo $row['to_amount']; ?> <?php echo $row['to_currency']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo filter_var($settings['name'], FILTER_SANITIZE_STRING); ?> Fee Charged:</td>
                                        <td><?php echo filter_var($row['fee'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($row['to_currency'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Date:</td>
                                        <td><?php if($row['created']>0) { echo date("d/m/Y H:i:s",$row['created']); } else { echo 'n/a'; } ?></td>
                                    </tr>
                                    <tr>
                                        <td>Exchange Rates:</td>
                                        <td><?php echo $row['from_rate']; ?> <?php echo $row['from_currency']; ?> - <?php echo $row['to_rate']; ?> <?php echo $row['to_currency']; ?></td>
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
                                <input type="text" class="form-control" name="txid" placeholder="Exchange ID" value="<?php if(isset($_POST['txid'])) { echo filter_var($_POST['txid'], FILTER_SANITIZE_STRING); } ?>">
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
                                    <th>User</th>
                                    <th>From - To</th>
                                    <th>EXC ID</th>
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
                                
                                if($searching==1) {
                                    if(empty($p_query)) {
                                        $qry = 'empty query';
                                    }
                                    $statement = "users_converts WHERE $p_query";
                                    $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC");
                                } else {
                                    $statement = "users_converts";
                                    $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                                }
                                if($query->num_rows>0) {
                                    while($row = $query->fetch_assoc()) {
                                        ?>
                                        <tr>
                                        <td><a href="./?a=users&b=edit&id=<?php echo filter_var($row['uid'], FILTER_SANITIZE_STRING); ?>"><?php echo idinfo($row['uid'],"email"); ?></a></td>
                                        <td><?php echo $row['from_amount']; ?> <?php echo $row['from_currency']; ?> - <?php echo $row['to_amount']; ?> <?php echo $row['to_currency']; ?></td>
                                            <td><?php echo $row['txid']; ?></td>
                                            <td>
                                                <a href="./?a=curencies_log&b=view&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="View"><span class="badge badge-primary"><i class="fa fa-search"></i> View</span></a> 
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    if($searching == "1") {
                                        echo '<tr><td colspan="6">No found results.</td></tr>';
                                    } else {
                                        echo '<tr><td colspan="6">No have currencies log yet.</td></tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        if($searching == "0") {
                            $ver = "./?a=curencies_log";
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