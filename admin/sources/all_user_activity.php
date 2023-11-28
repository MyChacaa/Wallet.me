<?php
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
$id = protect($_GET['id']);
$query = $db->query("SELECT * FROM users WHERE id='$id'");
if($query->num_rows==0) { header("Location: ./?a=users"); }
$row = $query->fetch_assoc();
?>
        
        
        
<div class="col-md-12">
    <div class="card">
	    <div class="card-body">
            <h3>Activities</h3>
            <hr/>
                        
            <form class="user-connected-from user-signup-form" action="" method="POST">
                <div class="row form-group">
                    <div class="col">
                        <input type="text" class="form-control" name="txid" placeholder="TXID">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" name="email" placeholder="Email">
                    </div>
                    <div class="col">
                        <div class="input-group date">
                            <input type="text" class="form-control" name="start_date" id="datepicker1" placeholder="Start Date">
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group date">
                            <input type="text" class="form-control" name="end_date"  id="datepicker2" placeholder="End Date">
                        </div>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary btn-block" name="pw_search" value="search" style="padding:11px;"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
            <?php
            $PW_Searching = 0;
			if(isset($_POST['pw_search'])) {
            $FormBTN = protect($_POST['pw_search']);
            if($FormBTN == "search") {
                $PW_Search = '';
                $transaction_id = protect($_POST['txid']);
                if(!empty($transaction_id)) {
                    $PW_Search .= " and txid='$transaction_id'";
                }
                $email = protect($_POST['email']);
                $email_id = PW_GetUserID($email);
                if($email_id !== false && $email_id > 0) {
                    $PW_Search .= " and u_field_1='$email_id'";
                }
                $start_date = protect($_POST['start_date']);
                if(!empty($start_date)) {
                    $start_date = strtotime($start_date);
                    $PW_Search .= " and created > $start_date";
                }
                $end_date = protect($_POST['end_date']);
                if(!empty($end_date)) {
                    $end_date = strtotime($end_date);
                    $PW_Search .= " and created < $start_date";
                }
                if(!empty($PW_Search)) {
                    $PW_Searching = 1;
                }
            }
			}
            ?>
                        
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>TXID</th>
                            <th>Activity</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
                        $limit = 6;
                        $startpoint = ($page * $limit) - $limit;
                        if($page == 1) {
                            $i = 1;
                        } else {
                            $i = $page * $limit;
                        }
                        if($PW_Searching == "1") {
                            $statement = "activity WHERE uid='$row[id]' $PW_Search";
                            $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC");
                        } else {
                            $statement = "activity WHERE uid='$row[id]'";
                            $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                        }
                        if($query->num_rows>0) {
                            while($row = $query->fetch_assoc()) {
                                $amount = $row['amount'];
                                if($row['type'] == "2" or $row['type'] == "4" or $row['type'] == "6" or $row['type'] == "7" or $row['type'] == "8" or $row['type'] == "20" or $row['type'] == "21" or $row['type'] == "23" or $row['type'] == "25") {
                                        $amount = '-'.$amount;
                                } else {
                                        $amount = '+'.$amount;
                                }
                                ?>
                                <tr>
                                    <td><?php echo PW_ActivityDate($row['created']); ?></td>
                                    <td><?php echo $row['txid']; ?></td>
                                    <td><?php echo DecodeUserActivity_admin($row['id']); ?></td>
                                    <td><?php echo $amount.' '.$row['currency']; ?></td>
                                    <td>
                                    <?php
                                    
                                    if($row['status'] == "1") {
                                		echo '<span class="badge badge-success">Completed</span>';
                                	} elseif($row['status'] == "2") {
                                		echo '<span class="badge badge-danger">Canceled</span>';
                                	} elseif($row['status'] == "3") { 
                                		echo '<span class="badge badge-warning">Pending</span>';
                                	} elseif($row['status'] == "4") {
                                		echo '<span class="badge badge-danger">On hold</span>';
                                	} elseif($row['status'] == "5") {
                                		echo '<span class="badge badge-success">Refunded</span>';
                                	} else {
                                		echo '<span class="badge badge-default">Unknown</span>';
                                	}
                                	?>
                                    </td>
                                </tr>
                                <?php
                                }
                            } else {
                                if($PW_Searching == "1") {
                                    echo '<tr><td colspan="6">'.$lang[info_7].'</td></tr>';
                                } else {
                                    echo '<tr><td colspan="6">'.$lang[info_8].'</td></tr>';
                                }
                            }
                            ?>
                    </tbody>
            </table>
                <?php
                if($PW_Searching == "0") {
                    $ver = "./?a=all_user_activity&id=$id";
                    if(admin_pagination($statement,$ver,$limit,$page)) {
                        echo admin_pagination($statement,$ver,$limit,$page);
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>