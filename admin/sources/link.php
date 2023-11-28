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
	$query = $db->query("SELECT * FROM payment_link WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=link"); }
	$row = $query->fetch_assoc();
	?>
	

           <div class="col-md-12">
					<div class="card">
                        <div class="card-body">
                            <?php
                            $FormBTN = protect($_POST['delete']);
                            if($FormBTN == "delete") {
                                $hash = protect($_POST['hash']);
                                $delete = $db->query("DELETE FROM payment_link WHERE hash='$hash'");
                                echo "<br><br>";
                                echo  info("Payment Link Deleted.");
                                header("Location: ./?a=link");
                            }
                            ?>

                            <table class="table table-striped">
                                <tbody>
                                <tr>
                                        <td>Link ID:</td>
                                        <td><?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                <tr>
                                        <td>Link Hash:</td>
                                        <td><?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <tr>
                                        <td>User:</td>
                                        <td><a href="./?a=users&b=edit&id=<?php echo filter_var($row['user_id'], FILTER_SANITIZE_STRING); ?>"><?php echo filter_var($row['merchant_email'], FILTER_SANITIZE_STRING); ?></a></td>
                                    </tr>
                                    <tr>
                                        <td>Item No / Name:</td>
                                        <td><?php echo filter_var($row['item_number'], FILTER_SANITIZE_STRING); ?> / <?php echo filter_var($row['item_name'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Amount:</td>
                                        <td><?php echo filter_var($row['item_price'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($row['item_currency'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Date:</td>
                                        <td><?php if($row['created']>0) { echo date("d/m/Y H:i:s",$row['time']); } else { echo 'n/a'; } ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <form action="" method="POST">
                                    <input type="hidden" name="hash" value="<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>">
                                    <input type="text" value="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>link/<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>" id="myInput<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>" style="width:35%;background:none;border:none;">
                                    	&nbsp;	&nbsp;	&nbsp;	&nbsp;	&nbsp;
                                    <button type="button" class="btn btn-info text-xs" onclick="my<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>Function()" onmouseout="out<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>Func()"><span class="tooltiptext" id="myTooltip<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>">Copy</span></button>
                                    <a target="_blank" href="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>link/<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>" class="btn btn-secondary text-xs">View</a>
                                    <button type="submit" name="delete" value="delete" class="btn btn-danger">Delete</button>
                                    </form>
                                    <script>
                                    function my<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>Function() {
                                      var copyText = document.getElementById("myInput<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>");
                                      copyText.select();
                                      copyText.setSelectionRange(0, 99999);
                                      document.execCommand("copy");
                                      
                                      var tooltip = document.getElementById("myTooltip<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>");
                                      tooltip.innerHTML = "Copied!";
                                    }
                                    
                                    function outFunc() {
                                      var tooltip = document.getElementById("myTooltip<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>");
                                      tooltip.innerHTML = "Copy";
                                    }
                                    </script>
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
                                <input type="text" class="form-control" name="txid" placeholder="Link Hash" value="<?php if(isset($_POST['txid'])) { echo filter_var($_POST['txid'], FILTER_SANITIZE_STRING); } ?>">
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
                                    <th class="border-top-0">Email</th>
                                    <th class="border-top-0">Amount</th>
                                    <th class="border-top-0">Created on</th>
                                    <th class="border-top-0">Action</th>
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
                                        $statement = "payment_link WHERE $p_query";
                                        $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC");
                                    } else {
                                        $statement = "payment_link";
                                        $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                                    }
                                    if($query->num_rows>0) {
                                        while($row = $query->fetch_assoc()) {
                                            ?>
                                    <tr>
                                        <td class="text-truncate"><a href="./?a=users&b=edit&id=<?php echo filter_var($row['user_id'], FILTER_SANITIZE_STRING); ?>"><?php echo filter_var($row['merchant_email'], FILTER_SANITIZE_STRING); ?></a></td>
                                        <td class="text-truncate">
                                            <?php echo filter_var($row['item_price'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($row['item_currency'], FILTER_SANITIZE_STRING); ?>
                                        </td>
                                        <td class="text-truncate">
                                            <?php echo date("d/m/Y H:i:s",$row['time']); ?>
                                        </td>
                                        <td>
                                             <a href="./?a=link&b=view&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="View"><button type="button" class="btn btn-sm btn-outline-danger round">View</button></a>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    } else {
                                        if($searching == "1") {
                                            echo '<tr><td colspan="6">No found results.</td></tr>';
                                        } else {
                                            echo '<tr><td colspan="6">No have Links yet.</td></tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        <?php
                        if($searching == "0") {
                            $ver = "./?a=link";
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