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
    
           <div class="col-md-12">
					<div class="card">
                        <div class="card-body">
                        <script type="text/javascript">
						function load_account_fiels(value) {
							var data_url = "requests/WithdrawalGatewayFields.php?gateway="+value;
							$.ajax({
								type: "GET",
								url: data_url,
								dataType: "html",
								success: function (data) {
									$("#account_fields").html(data);
								}
							});
						}
						</script>
                            <?php
							if (isset($_POST['btn_add'])){
							$FormBTN = protect($_POST['btn_add']);
							} else {
							$FormBTN = "";
							}
                            if($FormBTN == "deposit_gateway") {
                                $name = protect($_POST['name']);
                                $status = protect($_POST['status']);
                                $fee = protect($_POST['fee']);
                                if(isset($_POST['include_fee'])) { $include_fee = 1; } else { $include_fee = 0; }
                                $extra_fee = protect($_POST['extra_fee']);
                                $field_1 = protect($_POST['field_1']);
                                $field_2 = protect($_POST['field_2']);
                                $field_3 = protect($_POST['field_3']);
                                $process_time = protect($_POST['process_time']);
                                $process_type = protect($_POST['process_type']);
                                $min_amount = protect($_POST['min_amount']);
                                $max_amount = protect($_POST['max_amount']);
                                $currency = protect($_POST['currency']);
                                $check = $db->query("SELECT * FROM gateways WHERE name='$name' and type='2'");
                                if(empty($name)) {
                                    echo error("Please select gateway.");
                                } elseif(empty($field_1)) {
                                    echo error("Please enter a $name fields.");
                                }  elseif($check->num_rows>0) { 
                                    echo error("Gateway <b>$name</b> is already exists.");
                                } elseif(empty($fee)) {
                                    echo error("Please enter your withdrawal fee.");
                                } elseif(!is_numeric($fee)) {
                                    echo error("Invalid fee format. You must enter it with numbers. Example: 10");
                                } elseif($include_fee == "1" && empty($extra_fee)) {
                                    echo error("Please enter percentage fee.");
                                } elseif($include_fee == "1" && !is_numeric($extra_fee)) {
                                    echo error("Invalid percentage format. You must enter it with numbers. Example: 3.55");
                                } elseif(!is_numeric($process_time)) {
                                    echo error("Please enter process time with numbers.");
                                } elseif(empty($process_type)) {
                                    echo error("Please select process type.");
                                } else {
                                    $insert = $db->query("INSERT gateways (name,type,fee,include_fee,extra_fee,status,process_type,process_time,min_amount,max_amount,currency) VALUES ('$name','2','$fee','$include_fee','$extra_fee','$status','$process_type','$process_time','$min_amount','$max_amount','$currency')");
                                    $query = $db->query("SELECT * FROM gateways ORDER BY id DESC LIMIT 1");
                                    $row = $query->fetch_assoc();
                                        $field_1 = protect($_POST['field_1']);
                                        $field_2 = protect($_POST['field_2']);
                                        $field_3 = protect($_POST['field_3']);
                                        $field_4 = protect($_POST['field_4']);
                                        $field_5 = protect($_POST['field_5']);
                                        $field_6 = protect($_POST['field_6']);
                                        $field_7 = protect($_POST['field_7']);
                                        $field_8 = protect($_POST['field_8']);
                                        $field_9 = protect($_POST['field_9']);
                                        $field_10 = protect($_POST['field_10']);
                                        if(!empty($field_1)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number) VALUES ('$row[id]','$field_1','1')"); }
                                        if(!empty($field_2)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number) VALUES ('$row[id]','$field_2','2')"); }
                                        if(!empty($field_3)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number) VALUES ('$row[id]','$field_3','3')"); }
                                        if(!empty($field_4)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number) VALUES ('$row[id]','$field_4','4')"); }
                                        if(!empty($field_5)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number) VALUES ('$row[id]','$field_5','5')"); }
                                        if(!empty($field_6)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number) VALUES ('$row[id]','$field_6','6')"); }
                                        if(!empty($field_7)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number) VALUES ('$row[id]','$field_7','7')"); }
                                        if(!empty($field_8)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number) VALUES ('$row[id]','$field_8','8')"); }
                                        if(!empty($field_9)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number) VALUES ('$row[id]','$field_9','9')"); }
                                        if(!empty($field_10)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number) VALUES ('$row[id]','$field_10','10')"); }
                                    
                                    echo success("Gateway <b>$name</b> was added successfully.");
                                }
                            }
                            ?>

                            <form action="" method="POST">
                                <div class="form-group">
                                    <label>Gateway</label>
                                    <select class="form-control" name="name" onchange="load_account_fiels(this.value);">
                                        <option value=""></option>
                                        <option value="PayPal">PayPal</option>
                                        <option value="Payeer">Payeer</option>
                                        <option value="AdvCash">AdvCash</option>
                                        <option value="Perfect Money">Perfect Money</option>
                                        <option value="Skrill">Skrill</option>
                                        <option value="Capitalist">Capitalist</option>
                                        <option value="Bitcoin">Bitcoin</option>
                                        <option value="Litecoin">Litecoin</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Active</label>
                                    <select class="form-control" name="status">
										<?php
										
										if (isset($row['status'])){
										$row['status'] = $row['status'];
										} else {
										$row['status'] = "0";
										}
										
										?>
                                        <option value="1" <?php if($row['status'] == "1") { echo "selected"; } ?>>Yes</option>
                                        <option value="0" <?php if($row['status'] == "0") { echo "selected"; } ?>>No</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Withdrawal Fee</label>
                                    <input type="text" class="form-control" name="fee">
                                    <small>Enter fixed withdrawal fee. Your default currency is <b><?php echo filter_var($settings['default_currency'], FILTER_SANITIZE_STRING); ?></b>, if client withdrawal in other currency, this amount will be converted automatically.</small>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="include_fee" value="yes"> Include additional percentage fee</label>
                                </div>
                                <div class="form-group">
                                    <label>Percentage Fee</label>
                                    <input type="text" class="form-control" name="extra_fee">
                                    <small>You can setup percentage fee. Enter percentage without symbol %. If do not want to setup fee leave blank.</small>
                                </div>
                                <div class="form-group">
                                    <label>Currency</label>
                                    <select class="form-control" name="currency">
                                        <?php
                        				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='1'");
                    		            while($curr = $curr_Query->fetch_assoc()) {
                        						
                                            echo '<option value="'.$curr['currency'].'">'.$curr['currency'].'</option>';
                                        }
                                        ?>
                                        <?php
                        				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='2'");
                    		            while($curr = $curr_Query->fetch_assoc()) {
                        						
                                            echo '<option value="'.$curr['currency'].'">'.$curr['currency'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Minimum Amount</label>
                                            <input type="text" name="min_amount" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Maximum Amount</label>
                                            <input type="text" name="max_amount" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <span id="account_fields"></span>
                                <div class="row form-group">
                                    <div class="col-md-12"><label>Process time</label></div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="process_time" placeholder="1">
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-control" name="process_type">
                                            <option value="1">minute(s)</option>
                                            <option value="2">hour(s)</option>
                                            <option value="3">day(s)</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary" name="btn_add" value="deposit_gateway"><i class="fa fa-plus"></i> Add</button>
                            </form>
		                </div>
                    </div>
            </div>
    
    <?php
} elseif($b == "edit") {
	$id = protect($_GET['id']);
	$query = $db->query("SELECT * FROM gateways WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=withdrawal_methods"); }
	$row = $query->fetch_assoc();
	?>
	
           <div class="col-md-12">
					<div class="card">
                        <div class="card-body">

                            <?php
                            $FormBTN = protect($_POST['btn_save']);
                            if($FormBTN == "info") {
                                $name = protect($_POST['name']);
                                $status = protect($_POST['status']);
                                $fee = protect($_POST['fee']);
                                if(isset($_POST['include_fee'])) { $include_fee = 1; } else { $include_fee = 0; }
                                $extra_fee = protect($_POST['extra_fee']);
                                $process_time = protect($_POST['process_time']);
                                $process_type = protect($_POST['process_type']);
                                $currency = protect($_POST['currency']);
                                $min_amount = protect($_POST['min_amount']);
                                $max_amount = protect($_POST['max_amount']);
                                if(empty($fee)) {
                                    echo error("Please enter your withdrawal fee.");
                                } elseif(!is_numeric($fee)) {
                                    echo error("Invalid fee format. You must enter it with numbers. Example: 10");
                                } elseif($include_fee == "1" && empty($extra_fee)) {
                                    echo error("Please enter percentage fee.");
                                } elseif($include_fee == "1" && !is_numeric($extra_fee)) {
                                    echo error("Invalid percentage format. You must enter it with numbers. Example: 3.55");
                                } elseif(!is_numeric($process_time)) {
                                    echo error("Please enter process time with numbers.");
                                } elseif(empty($process_type)) {
                                    echo error("Please select process type.");
                                } else {
                                    foreach($_POST['fieldvalues'] as $k => $v) {
                                        $update = $db->query("UPDATE gateways_fields SET field_name='$v' WHERE id='$k'");
                                    }
                                    $update = $db->query("UPDATE gateways SET max_amount='$max_amount',min_amount='$min_amount',currency='$currency',status='$status',fee='$fee',include_fee='$include_fee',extra_fee='$extra_fee',process_type='$process_type',process_time='$process_time' WHERE id='$row[id]'");
                                    $query = $db->query("SELECT * FROM gateways WHERE id='$row[id]'");
                                    $row = $query->fetch_assoc();
                                    echo success("Your changes was saved successfully.");
                                }
                            }
                            ?>

                            <form action="" method="POST">
                            <div class="form-group">
                                    <label>Gateway</label>
                                    <input type="text" class="form-control" name="name" disabled value="<?php echo filter_var($row['name'], FILTER_SANITIZE_STRING); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Active</label>
                                    <select class="form-control" name="status">
                            <option value="1" <?php if($row['status'] == "1") { echo 'selected'; } ?>>Yes</option>
                            <option value="0" <?php if($row['status'] == "0") { echo 'selected'; } ?>>No</option>
                        </select>
                        </div>
                                <div class="form-group">
                                    <label>Withdrawal Fee</label>
                                    <input type="text" class="form-control" name="fee" value="<?php echo filter_var($row['fee'], FILTER_SANITIZE_STRING); ?>">
                                    <small>Enter fixed withdrawal fee. Your default currency is <b><?php echo filter_var($settings['default_currency'], FILTER_SANITIZE_STRING); ?></b>, if client withdrawal in other currency, this amount will be converted automatically.</small>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="include_fee" value="yes" <?php if($row['include_fee'] == "1") { echo 'checked'; } ?>> Include additional percentage fee</label>
                                </div>
                                <div class="form-group">
                                    <label>Percentage Fee</label>
                                    <input type="text" class="form-control" name="extra_fee" value="<?php echo filter_var($row['extra_fee'], FILTER_SANITIZE_STRING); ?>">
                                    <small>You can setup percentage fee. Enter percentage without symbol %. If do not want to setup fee leave blank.</small>
                                </div>
                                <div class="form-group">
                                    <label>Currency</label>
                                    <select class="form-control" name="currency">
                                        <?php
                                        
                                        
                        				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='1'");
                    		            while($curr = $curr_Query->fetch_assoc()) {
                        					if($curr['currency'] == filter_var($row['currency'], FILTER_SANITIZE_STRING)) { $sel = 'selected'; } else { $sel = ''; }
                                            echo '<option value="'.$curr['currency'].'" '.$sel.'>'.$curr['currency'].'</option>';
                                        }
                                        ?>
                                        <?php
                        				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='2'");
                    		            while($curr = $curr_Query->fetch_assoc()) {
                        					if($curr['currency'] == filter_var($row['currency'], FILTER_SANITIZE_STRING)) { $sel = 'selected'; } else { $sel = ''; }	
                                            echo '<option value="'.$curr['currency'].'" '.$sel.'>'.$curr['currency'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Minimum Amount</label>
                                            <input type="text" name="min_amount" value="<?php echo filter_var($row['min_amount'], FILTER_SANITIZE_STRING); ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Maximum Amount</label>
                                            <input type="text" name="max_amount" value="<?php echo filter_var($row['max_amount'], FILTER_SANITIZE_STRING); ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $GatewayFields = $db->query("SELECT * FROM gateways_fields WHERE gateway_id='$row[id]' ORDER BY id");
                                if($GatewayFields->num_rows>0) {
                                    $i=1;
                                    while($gf = $GatewayFields->fetch_assoc()) {
                                        echo '<div class="form-group">
                                            <label>Name of the Field '.$i.'</label>
                                            <input type="text" class="form-control" name="fieldvalues['.$gf['id'].']" value="'.$gf['field_name'].'">
                                        </div>';
                                        $i++;
                                    }
                                }
                                ?>
                                <div class="row form-group">
                                    <div class="col-md-12"><label>Process time</label></div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="process_time" placeholder="1" value="<?php echo filter_var($row['process_time'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-control" name="process_type">
                                            <option value="1" <?php if($row['process_type'] == "1") { echo 'selected'; } ?>>minute(s)</option>
                                            <option value="2" <?php if($row['process_type'] == "2") { echo 'selected'; } ?>>hour(s)</option>
                                            <option value="3" <?php if($row['process_type'] == "3") { echo 'selected'; } ?>>day(s)</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary" name="btn_save" value="info"><i class="fa fa-check"></i> Save changes</button>
                            </form>
		                </div>
                    </div>
            </div>
    
	<?php
} elseif($b == "delete") {
	$id = protect($_GET['id']);
	$query = $db->query("SELECT * FROM gateways WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=withdrawal_methods"); }
	$row = $query->fetch_assoc();
	?>
	

           <div class="col-md-12">
					<div class="card">
                        <div class="card-body">
			<?php
			if(isset($_GET['confirm'])) {
				$delete = $db->query("DELETE FROM gateways WHERE id='$row[id]'");
				echo success("Gateway <b>$row[name]</b> was deleted.");
			} else {
				echo info("Are you sure you want to delete gateway <b>$row[name]</b>?");
				echo '<a href="./?a=withdrawal_methods&b=delete&id='.$row['id'].'&confirm=1" class="btn btn-success"><i class="fa fa-check"></i> Yes</a>&nbsp;&nbsp;
					<a href="./?a=withdrawal_methods" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
			}
			?>
		</div>
	</div>
	</div>
	<?php
} else {
?>
<br>
		
           <div class="col-md-12">
					<div class="card">
                        <div class="card-body table-responsive">
                            
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="40%">Gateway</th>
                                    <th width="20%">Withdrawal Fee</th>
                                    <th width="15%">Active</th>
                                    <th width="15%">Action</th>
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
                                $statement = "gateways WHERE type='2'";
                                $query = $db->query("SELECT * FROM {$statement} ORDER BY id LIMIT {$startpoint} , {$limit}");
                                if($query->num_rows>0) {
                                    while($row = $query->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td><?php echo filter_var($row['name'], FILTER_SANITIZE_STRING); ?></td>
                                            <td>
                                                <?php
                                                $fee = $row['fee'];
                                                $include_fee = $row['include_fee'];
                                                $extra_fee = $row['extra_fee'];
                                                if($include_fee == "1") {
                                                    $efee = '+ '.$extra_fee.'%';
                                                } else {
                                                    $efee = '';
                                                }
                                                echo filter_var($fee.' '.$settings['default_currency'].' '.$efee, FILTER_SANITIZE_STRING);
                                                ?>
                                            </td>
                                            <td><?php
                                                if($row['status'] == "1") {
                                                    echo '<span class="badge badge-success">Yes</span>';
                                                } else {
                                                    echo '<span class="badge badge-danger">No</span>';
                                                }
                                            ?></td>
                                            <td>
                                                <a href="./?a=withdrawal_methods&b=edit&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="Edit"><span class="badge badge-primary"><i class="fa fa-pencil"></i> Edit</span></a> 
                                                <a href="./?a=withdrawal_methods&b=delete&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="Delete"><span class="badge badge-danger"><i class="fa fa-trash"></i> Delete</span></a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="4">No have gateways yet.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                            $ver = "./?a=withdrawal_methods";
                            if(admin_pagination($statement,$ver,$limit,$page)) {
                                echo admin_pagination($statement,$ver,$limit,$page);
                            }
                        ?>
                    </div>
                </div>
            </div>
<?php
}
?>