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
if($b == "add") {
?>
<div class="card">
    <div class="card-header">
        <strong class="card-title">Fixed Deposit <b>Plans</b></strong>
    </div>
   
    <div class="card-body">
        <?php
            if(isset($_POST['create'])){
                $create = protect($_POST['create']);
                if($create == "create") {
                    $name = protect($_POST['name']);
                    $status = protect($_POST['status']);
                    $min_amount = protect($_POST['min_amount']);
                    $max_amount = protect($_POST['max_amount']);
                    $return_per = protect($_POST['return_per']);
                    $days = protect($_POST['days']);
                    $check = $db->query("SELECT * FROM fixed_deposit_plans WHERE name='$name'");
                    if(empty($name) or empty($status) or empty($min_amount) or empty($max_amount) or empty($return_per) or empty($days)) {
                        echo error("Some fields are empty.");
                    } elseif($check->num_rows>0) { 
                        echo error("Plan <b>$name</b> is already exists.");
                    } else {
                        $insert = $db->query("INSERT fixed_deposit_plans (name,status,min_amount,max_amount,return_per,days) 
                        VALUES ('$name','$status','$min_amount','$max_amount','$return_per','$days')");
                        
                        echo success("Plan $name has been added.");
                    }
                }
            }
        ?>
        <form method="POST" action="">
            <div class="row">
                <div class="col-md">
                    <div class="form-group">
        				<label>Plan Name</label>
        				<input class="form-control" name="name">
        			</div>
                </div>
                <div class="col-md">
                    <div class="form-group">
        				<label>Plan Status</label>
        				<select class="form-control" name="status">
        				    <option value="1">Active</option>
        				    <option value="0">Inactive</option>
        				</select>
        			</div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md">
                    <div class="form-group">
        				<label>Minimum Amount</label>
        				<input class="form-control" name="min_amount">
        				<small>Default currency is <b><?= $settings['default_currency'] ?></b>, Minimum amount will be converted to other currencies automatically.</small>
        			</div>
                </div>
                <div class="col-md">
                    <div class="form-group">
                        <label>Maximum Amount</label>
        				<input class="form-control" name="max_amount">
        				<small>Default currency is <b><?= $settings['default_currency'] ?></b>, Maximum amount will be converted to other currencies automatically.</small>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md">
                    <div class="form-group">
        				<label>Percentage Return</label>
        				<input class="form-control" name="return_per">
        			</div>
                </div>
                <div class="col-md">
                    <div class="form-group">
        				<label>Duration (Days)</label>
        				<input class="form-control" name="days">
        			</div>
                </div>
            </div>
            <button class="btn btn-primary" type="submit" name="create" value="create" style="width:100%;">Create Plan</button>
        </form>
    
    </div>
</div>
<?php } elseif($b == "edit") { 
    $id = protect($_GET['id']);
	$query = $db->query("SELECT * FROM fixed_deposit_plans WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=fixed_deposit"); }
	$row = $query->fetch_assoc();

?>


<div class="card">
    <div class="card-header">
        <strong class="card-title">Fixed Deposit <b>Plans Edit</b></strong>
    </div>
    
    <div class="card-body">
        <?php
            if(isset($_POST['edit'])){
                $edit = protect($_POST['edit']);
                if($edit == "edit") {
                    $name = protect($_POST['name']);
                    $status = protect($_POST['status']);
                    $min_amount = protect($_POST['min_amount']);
                    $max_amount = protect($_POST['max_amount']);
                    $return_per = protect($_POST['return_per']);
                    $days = protect($_POST['days']);
                    if(empty($name) or empty($status) or empty($min_amount) or empty($max_amount) or empty($return_per) or empty($days)) {
                        echo error("Some fields are empty.");
                    } else {
                        $insert = $db->query("INSERT fixed_deposit_plans (name,status,min_amount,max_amount,return_per,days) 
                        VALUES ('$name','$status','$min_amount','$max_amount','$return_per','$days')");
                        
                        echo success("Plan $name has been added.");
                    }
                }
            }
        ?>
        <form method="POST" action="">
            <div class="row">
                <div class="col-md">
                    <div class="form-group">
        				<label>Plan Name</label>
        				<input class="form-control" name="name" value="<?= $row['name']?>">
        			</div>
                </div>
                <div class="col-md">
                    <div class="form-group">
        				<label>Plan Status</label>
        				<select class="form-control" name="status">
        				    <option value="1" <?php if($row['status'] == "1") { echo 'selected'; } ?>>Active</option>
        				    <option value="0" <?php if($row['status'] == "0") { echo 'selected'; } ?>>Inactive</option>
        				</select>
        			</div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md">
                    <div class="form-group">
        				<label>Minimum Amount</label>
        				<input class="form-control" name="min_amount" value="<?= $row['min_amount']?>">
        				<small>Default currency is <b><?= $settings['default_currency'] ?></b>, Minimum amount will be converted to other currencies automatically.</small>
        			</div>
                </div>
                <div class="col-md">
                    <div class="form-group">
                        <label>Maximum Amount</label>
        				<input class="form-control" name="max_amount" value="<?= $row['max_amount']?>">
        				<small>Default currency is <b><?= $settings['default_currency'] ?></b>, Maximum amount will be converted to other currencies automatically.</small>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md">
                    <div class="form-group">
        				<label>Percentage Return</label>
        				<input class="form-control" name="return_per" value="<?= $row['return_per']?>">
        			</div>
                </div>
                <div class="col-md">
                    <div class="form-group">
        				<label>Duration (Days)</label>
        				<input class="form-control" name="days" value="<?= $row['days']?>">
        			</div>
                </div>
            </div>
            <button class="btn btn-primary" type="submit" name="edit" value="edit" style="width:100%;">Save Plan</button>
        </form>
    
    </div>
</div>

<?php } elseif($b == "delete") { 
    $id = protect($_GET['id']);
	$query = $db->query("SELECT * FROM fixed_deposit_plans WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=fixed_deposit"); }
	$row = $query->fetch_assoc();
?>

<div class="col-md-12">
    <div class="card">
        <div class="card-body">
			<?php
			if(isset($_GET['confirm'])) {
				$delete = $db->query("DELETE FROM fixed_deposit_plans WHERE id='$row[id]'");
				echo success("Gateway <b>$row[name]</b> was deleted.");
			} else {
				echo info("Are you sure you want to delete <b>$row[name]</b>?");
				echo '<a href="./?a=fixed_deposit&b=delete&id='.$row['id'].'&confirm=1" class="btn btn-success"><i class="fa fa-check"></i> Yes</a>&nbsp;&nbsp;
					<a href="./?a=fixed_deposit" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
			}
			?>
		</div>
	</div>
</div>

<?php } else { ?>
<div class="col-md-12">
    <div class="card card-body">
        <input class="form-control" value="0 0 * * * curl  <?=$settings['url']?>crons/Cron.php?a=FixDeposit" disabled>
        <small>Create the following Cron Job using CURL (If you not setup this, Return amount will not be credit.) Use cPanel or use 3rd Party Ex : <a target="_blank" href="https://www.easycron.com?ref=135586">EasyCron (Free)</a></small>
    </div>
    
	<div class="card">
        <div class="card-body table-responsive">
                            
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Plan Name</th>
                        <th>Minimum</th>
                        <th>Maximum</th>
                        <th>Return Rate</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
                    $limit = 20;
                    $startpoint = ($page * $limit) - $limit;
                    if($page == 1) {
                        $i = 1;
                    } else {
                        $i = $page * $limit;
                    }
                    $statement = "fixed_deposit_plans";
                    $query = $db->query("SELECT * FROM {$statement} ORDER BY id LIMIT {$startpoint} , {$limit}");
                    if($query->num_rows>0) {
                        while($row = $query->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?=$row['name']?></td>
                                <td><?=$settings['default_currency']?> <?=$row['min_amount']?></td>
                                <td><?=$settings['default_currency']?> <?=$row['max_amount']?></td>
                                <td><?=$row['return_per']?>%</td>
                                <td><?=$row['days']?> Days</td>
                                <td><?php
                                    if($row['status'] == "1") {
                                        echo '<span class="badge badge-success">Active</span>';
                                    } else {
                                        echo '<span class="badge badge-danger">Inactive</span>';
                                    }
                                ?></td>
                                <td>
                                    <a href="./?a=fixed_deposit&b=edit&id=<?=$row['id']; ?>" title="Edit"><span class="badge badge-primary"><i class="fa fa-pencil"></i> Edit</span></a> 
                                    <a href="./?a=fixed_deposit&b=delete&id=<?=$row['id']; ?>" title="Delete"><span class="badge badge-danger"><i class="fa fa-trash"></i> Delete</span></a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo '<tr><td colspan="3">No have plans yet.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            <?php
                $ver = "./?a=deposit_methods";
                if(admin_pagination($statement,$ver,$limit,$page)) {
                    echo admin_pagination($statement,$ver,$limit,$page);
                }
            ?>
        </div>
    </div>
</div>
<?php } ?>