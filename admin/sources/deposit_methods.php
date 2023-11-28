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

if($b == "add") {
    ?>
    
           <div class="col-md-12">
					<div class="card">
                        <div class="card-body">
                        <script type="text/javascript">
						function load_account_fiels(value) {
							var data_url = "requests/DepositGatewayFields.php?gateway="+value;
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
                                $min_amount = protect($_POST['min_amount']);
                                $max_amount = protect($_POST['max_amount']);
                                $currency = protect($_POST['currency']);
                                $a_field_1 = protect($_POST['a_field_1']);
                                $a_field_2 = protect($_POST['a_field_2']);
                                $a_field_3 = protect($_POST['a_field_3']);
                                $check = $db->query("SELECT * FROM gateways WHERE name='$name' and type='1'");
                                if(empty($name) or empty($min_amount) or empty($max_amount)) {
                                    echo error("Please select gateway.");
                                } elseif($check->num_rows>0) { 
                                    echo error("Gateway <b>$name</b> is already exists.");
                                } elseif($name !== "Bank Transfer" && empty($a_field_1)) {
                                    echo error("Please enter a $name account.");
                                } else {
                                    $insert = $db->query("INSERT gateways (name,type,a_field_1,a_field_2,a_field_3,status,min_amount,max_amount,currency) VALUES ('$name','1','$a_field_1','$a_field_2','$a_field_3','$status','$min_amount','$max_amount','$currency')");
                                    $query = $db->query("SELECT * FROM gateways ORDER BY id DESC LIMIT 1");
                                    $row = $query->fetch_assoc();
                                    if($name == "Bank Transfer") {
                                        $a_field_1 = protect($_POST['a_field_1']);
                                        $a_field_2 = protect($_POST['a_field_2']);
                                        $a_field_3 = protect($_POST['a_field_3']);
                                        $a_field_4 = protect($_POST['a_field_4']);
                                        $a_field_5 = protect($_POST['a_field_5']);
                                        $a_field_6 = protect($_POST['a_field_6']);
                                        $a_field_7 = protect($_POST['a_field_7']);
                                        $a_field_8 = protect($_POST['a_field_8']);
                                        $a_field_9 = protect($_POST['a_field_9']);
                                        $a_field_10 = protect($_POST['a_field_10']);
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
                                        if(!empty($field_1)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number,field_value) VALUES ('$row[id]','$field_1','1','$a_field_1')"); }
                                        if(!empty($field_2)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number,field_value) VALUES ('$row[id]','$field_2','2','$a_field_2')"); }
                                        if(!empty($field_3)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number,field_value) VALUES ('$row[id]','$field_3','3','$a_field_3')"); }
                                        if(!empty($field_4)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number,field_value) VALUES ('$row[id]','$field_4','4','$a_field_4')"); }
                                        if(!empty($field_5)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number,field_value) VALUES ('$row[id]','$field_5','5','$a_field_5')"); }
                                        if(!empty($field_6)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number,field_value) VALUES ('$row[id]','$field_6','6','$a_field_6')"); }
                                        if(!empty($field_7)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number,field_value) VALUES ('$row[id]','$field_7','7','$a_field_7')"); }
                                        if(!empty($field_8)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number,field_value) VALUES ('$row[id]','$field_8','8','$a_field_8')"); }
                                        if(!empty($field_9)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number,field_value) VALUES ('$row[id]','$field_9','9','$a_field_9')"); }
                                        if(!empty($field_10)) { $insert = $db->query("INSERT gateways_fields (gateway_id,field_name,field_number,field_value) VALUES ('$row[id]','$field_10','10','$a_field_10')"); }
                                    }
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
                                        <option value="Paytm">Paytm</option>
                                        <option value="Stripe">Stripe (Visa/MasterCard)</option>
                                        <option value="Flutterwave">Flutterwave (Visa/MasterCard)</option>
                                        <option value="2Checkout">2Checkout (Visa/Mastercard)</option>
                                        <option value="Payeer">Payeer</option>
                                        <option value="AdvCash">AdvCash</option>
                                        <option value="Perfect Money">Perfect Money</option>
                                        <option value="Skrill">Skrill</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                    </select>
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
                                        <option value="1" <?php if($row['status'] == "1") { echo 'selected'; } ?>>Yes</option>
                                        <option value="0" <?php if($row['status'] == "0") { echo 'selected'; } ?>>No</option>
                                    </select>
                                </div>
                                <span id="account_fields"></span>
                                <button type="submit" class="btn btn-primary" name="btn_add" value="deposit_gateway"><i class="fa fa-plus"></i> Add</button>
                            </form>
		                </div>
                    </div>
            </div>
    
    <?php
} elseif($b == "edit") {
	$id = protect($_GET['id']);
	$query = $db->query("SELECT * FROM gateways WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=deposit_methods"); }
	$row = $query->fetch_assoc();
	?>
	
           <div class="col-md-12">
					<div class="card">
                        <div class="card-body">

                            <?php
                            $FormBTN = protect($_POST['btn_save']);
                            if($FormBTN == "info") {
                                if($row['name'] == "Bank Transfer") {
                                    foreach($_POST['fields'] as $k=>$v) {
                                        $field = protect($K);
                                        $field_v = protect($v);
                                        $field_e = protect($_POST['values'][$k]);
                                        $update = $db->query("UPDATE gateways_fields SET field_name='$field_v',field_value='$field_e' WHERE id='$k'");
                                    }
                                } else {
                                    $a_field_1 = protect($_POST['a_field_1']);
                                    $a_field_2 = protect($_POST['a_field_2']);
                                    $a_field_3 = protect($_POST['a_field_3']);
                                    $a_field_4 = protect($_POST['a_field_4']);
                                    $a_field_5 = protect($_POST['a_field_5']);
                                   
                                    $update = $db->query("UPDATE gateways SET a_field_1='$a_field_1',a_field_2='$a_field_2',a_field_3='$a_field_3',a_field_4='$a_field_4',a_field_5='$a_field_5' WHERE id='$row[id]'");
                                    $query = $db->query("SELECT * FROM gateways WHERE id='$row[id]'");
                                    $row = $query->fetch_assoc();
                                }
                                $currency = protect($_POST['currency']);
                                $min_amount = protect($_POST['min_amount']);
                                $max_amount = protect($_POST['max_amount']);
                                $status = protect($_POST['status']);
                                $update = $db->query("UPDATE gateways SET status='$status' WHERE id='$row[id]'");
                                $update = $db->query("UPDATE gateways SET min_amount='$min_amount', max_amount='$max_amount',currency='$currency' WHERE id='$row[id]'");
                                $query = $db->query("SELECT * FROM gateways WHERE id='$row[id]'");
                                $row = $query->fetch_assoc();
                                echo success("Your changes was saved successfully.");
                            }
                            ?>

                            <form action="" method="POST">
                                <div class="form-group">
                                    <label>Gateway</label>
                                    <input type="text" class="form-control" name="name" disabled value="<?php echo filter_var($row['name'], FILTER_SANITIZE_STRING); ?>">
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
                            <div class="form-group">
                                <label>Active</label>
                                <select class="form-control" name="status">
                                    <option value="1" <?php if($row['status'] == "1") { echo 'selected'; } ?>>Yes</option>
                                    <option value="0" <?php if($row['status'] == "0") { echo 'selected'; } ?>>No</option>
                                </select>
                            </div>
                                <?php
                                if($row['name'] == "Bank Transfer") {
                                    ?>
                                    <div class="row">
                                    <?php
                                    $fieldsquery = $db->query("SELECT * FROM gateways_fields WHERE gateway_id='$row[id]' ORDER BY id");
                                    if($fieldsquery->num_rows>0) {
                                        $i=1;
                                        while($field = $fieldsquery->fetch_assoc()) {
                                            ?>
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label>Name of the Field <?php echo filter_var($i, FILTER_SANITIZE_STRING); ?></label>
                                                    <input type="text" class="form-control" name="fields[<?php echo filter_var($field['id'], FILTER_SANITIZE_STRING); ?>]" value="<?php echo filter_var($field['field_name'], FILTER_SANITIZE_STRING); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label>Value of the Field <?php echo filter_var($i, FILTER_SANITIZE_STRING); ?></label>
                                                    <input type="text" class="form-control" name="values[<?php echo filter_var($field['id'], FILTER_SANITIZE_STRING); ?>]" value="<?php echo filter_var($field['field_value'], FILTER_SANITIZE_STRING); ?>">
                                                </div>
                                            </div>
                                            <?php
                                            $i++;
                                        }
                                    }
                                    ?>
                                    </div>
                                    <?php     
                                } elseif($row['name'] == "PayPal") {
                                    ?>
                                    <div class="form-group">
                                        <label>Your PayPal account</label>
                                        <input type="text" class="form-control" name="a_field_1" value="<?php echo filter_var($row['a_field_1'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <?php
                                } elseif($row['name'] == "Payeer") {
                                    ?>
                                    <div class="form-group">
                                        <label>Your Payeer account</label>
                                        <input type="text" class="form-control" name="a_field_1" value="<?php echo filter_var($row['a_field_1'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Your Payeer secret key</label>
                                        <input type="text" class="form-control" name="a_field_2" value="<?php echo filter_var($row['a_field_2'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <?php
                                } elseif($row['name'] == "2Checkout") {
                                    ?>
                                    <div class="form-group">
                                        <label>Your 2Checkout Merchant Code</label>
                                        <input type="text" class="form-control" name="a_field_1" value="<?php echo filter_var($row['a_field_1'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Your 2Checkout secret key</label>
                                        <input type="text" class="form-control" name="a_field_2" value="<?php echo filter_var($row['a_field_2'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Success URL</label>
                                        <input type="text" class="form-control" value="<?= $settings['url'] ?>callbacks/checkPayment.php?a=2Checkout" disabled>
                                        <small>Login to 2checkout portal, go to integration, go to webhooks & api menu, scroll down page and check Redirect URL Section add above url their.</small>
                                    </div>
                                    <?php
                                } elseif($row['name'] == "Flutterwave") {
                                    ?>
                                    <div class="form-group">
                                        <label>Public key</label>
                                        <input type="text" class="form-control" name="a_field_1" value="<?php echo filter_var($row['a_field_1'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Secret key</label>
                                        <input type="text" class="form-control" name="a_field_2" value="<?php echo filter_var($row['a_field_2'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <?php    
                                } elseif($row['name'] == "Perfect Money") {
                                    ?>
                                    <div class="form-group">
                                        <label>Your Perfect Money account</label>
                                        <input type="text" class="form-control" name="a_field_1" value="<?php echo filter_var($row['a_field_1'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Account ID or API NAME</label>
                                        <input type="text" class="form-control" name="a_field_3" value="<?php echo filter_var($row['a_field_3'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Passpharse</label>
                                        <input type="text" class="form-control" name="a_field_2" value="<?php echo filter_var($row['a_field_2'], FILTER_SANITIZE_STRING); ?>">
                                        <small>Alternate Passphrase you entered in your Perfect Money account.</small>
                                    </div>
                                    <?php    
                                } elseif($row['name'] == "Stripe") {
                                    ?>
                                    <div class="form-group">
                                        <label>Your Stripe Public Key</label>
                                        <input type="text" class="form-control" name="a_field_1" value="<?php echo filter_var($row['a_field_1'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Your Stripe Secret Key</label>
                                        <input type="text" class="form-control" name="a_field_2" value="<?php echo filter_var($row['a_field_2'], FILTER_SANITIZE_STRING); ?>">
                                    </div>    
                                    <?php  
                                } elseif($row['name'] == "Paytm") {
                                    ?>
                                    <div class="form-group">
                                        <label>Your Paytm Merchant key</label>
                                        <input type="text" class="form-control" name="a_field_1" value="<?php echo filter_var($row['a_field_1'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Your Paytm Merchant ID</label>
                                        <input type="text" class="form-control" name="a_field_2" value="<?php echo filter_var($row['a_field_2'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Your Paytm Website name</label>
                                        <input type="text" class="form-control" name="a_field_3" value="<?php echo filter_var($row['a_field_3'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <?php
                                } elseif($row['name'] == "Skrill") {
                                    ?>
                                    <div class="form-group">
                                        <label>Your Skrill account</label>
                                        <input type="text" class="form-control" name="a_field_1" value="<?php echo filter_var($row['a_field_1'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Your Skrill secret key</label>
                                        <input type="text" class="form-control" name="a_field_2" value="<?php echo filter_var($row['a_field_2'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <?php   
                                } elseif($row['name'] == "AdvCash") {
                                    ?>
                                    <div class="form-group">
                                        <label>Your AdvCash account (Email)</label>
                                        <input type="text" class="form-control" name="a_field_1" value="<?php echo filter_var($row['a_field_1'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Your AdvCash U Account</label>
                                        <input type="text" class="form-control" name="a_field_4" value="<?php echo filter_var($row['a_field_4'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Your AdvCash Secret Key</label>
                                        <input type="text" class="form-control" name="a_field_2" value="<?php echo filter_var($row['a_field_2'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Your AdvCash SCI Name</label>
                                        <input type="text" class="form-control" name="a_field_3" value="<?php echo filter_var($row['a_field_3'], FILTER_SANITIZE_STRING); ?>">
                                    </div>
                                    <?php
                                } else { }
                                ?>
                                <button type="submit" class="btn btn-primary" name="btn_save" value="info"><i class="fa fa-check"></i> Save changes</button>
                            </form>
		                </div>
                    </div>
            </div>
    
	
        

           
        <?php
} elseif($b == "delete") {
	$id = protect($_GET['id']);
	$query = $db->query("SELECT * FROM gateways WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=deposit_methods"); }
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
				echo '<a href="./?a=deposit_methods&b=delete&id='.$row['id'].'&confirm=1" class="btn btn-success"><i class="fa fa-check"></i> Yes</a>&nbsp;&nbsp;
					<a href="./?a=deposit_methods" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
			}
			?>
		</div>
	</div>
	</div>
	<?php
} else {
?>


           <div class="col-md-12">
					<div class="card">
                        <div class="card-body table-responsive">
                            
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="70%">Gateway</th>
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
                                $statement = "gateways WHERE type='1'";
                                $query = $db->query("SELECT * FROM {$statement} ORDER BY id LIMIT {$startpoint} , {$limit}");
                                if($query->num_rows>0) {
                                    while($row = $query->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td><?php echo filter_var($row['name'], FILTER_SANITIZE_STRING); ?></td>
                                            <td><?php
                                                if($row['status'] == "1") {
                                                    echo '<span class="badge badge-success">Yes</span>';
                                                } else {
                                                    echo '<span class="badge badge-danger">No</span>';
                                                }
                                            ?></td>
                                            <td>
                                                <a href="./?a=deposit_methods&b=edit&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="Edit"><span class="badge badge-primary"><i class="fa fa-pencil"></i> Edit</span></a> 
                                                <a href="./?a=deposit_methods&b=delete&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="Delete"><span class="badge badge-danger"><i class="fa fa-trash"></i> Delete</span></a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="3">No have gateways yet.</td></tr>';
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
<?php
}
?>