<?php
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
$statement = "merchant_gateways WHERE name='Perfect Money'";
$query = $db->query("SELECT * FROM {$statement}");
$row = $query->fetch_assoc();
?>
<div class="row">
    <div class="col-md">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Perfect Money <b>Settings</b></strong>
            </div>
            <div class="card-body">
                <?php
        		if(isset($_POST['btn_save_pm'])) {
        		    $status = protect($_POST['status']);
        			$currency = protect($_POST['currency']);
        			$percentage_fee = protect($_POST['percentage_fee']);
        			$fix_fee = protect($_POST['fix_fee']);
        			$field_1 = protect($_POST['field_1']);
        			$field_2 = protect($_POST['field_2']);
        			
        			if(!is_numeric($percentage_fee)) {
        				echo error("Please enter transaction fee with numbers.");
        			} else {
        				$update = $db->query("UPDATE merchant_gateways SET status='$status',currency='$currency',percentage_fee='$percentage_fee',fix_fee='$fix_fee',field_1='$field_1',field_2='$field_2' WHERE name='Perfect Money'");
        				$query = $db->query("SELECT * FROM merchant_gateways");
        				$row = $query->fetch_assoc();
        				echo success("Your changes was saved successfully.");
        			}
        		}
        		?>
                <form action="" method="POST">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
            				<label>Status</label>
            				<select class="form-control" name="status">
            				    <option value="1" <?php if ($row['status'] == "1") { echo "selected"; } ?> class="form-control">Active</option>
            				    <option value="2" <?php if ($row['status'] == "2") { echo "selected"; } ?> class="form-control">Inactive</option>
            				</select>
            			</div>
                    </div>
                    <div class="col">
                        <div class="form-group">
            				<label>Currency</label>
            				<select class="form-control" name="currency" required>
                                <?php
                				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='1'");
            		            while($curr = $curr_Query->fetch_assoc()) {
                						if($curr['currency'] == $row['currency']) { $sel = 'selected'; } else { $sel = ''; }
                                    echo '<option value="'.$curr['currency'].'" '.$sel.'>'.$curr['currency'].'</option>';
                                }
                                ?>
                                <?php
                				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='2'");
            		            while($curr = $curr_Query->fetch_assoc()) {
                						if($curr['currency'] == $row['currency']) { $sel = 'selected'; } else { $sel = ''; }
                                    echo '<option value="'.$curr['currency'].'" '.$sel.'>'.$curr['currency'].'</option>';
                                }
                                ?>
                            </select>
            			</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
            				<label>Percentage Fee</label>
            				<input type="text" class="form-control" name="percentage_fee" value="<?= $row['percentage_fee']; ?>">
            				<small>Enter transaction fee in percentage without %. This transaction fee will be charged from recipient/sender of amount. Example: 3.4</small>
            			</div>
                    </div>
                    <div class="col">
                        <div class="form-group">
            				<label>Fixed/Flat Fee</label>
            				<input type="text" class="form-control" name="fix_fee" value="<?= $row['fix_fee']; ?>">
            				<small>Enter fixed merchant payment fee. Your default currency is <b><?php echo filter_var($settings['default_currency'], FILTER_SANITIZE_STRING); ?></b>, if client make merchant payment in other currency, this amount will be converted automatically.</small>
            			</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label>Perfect Money Account</label>
                            <input type="text" class="form-control" name="field_1" value="<?= $row['field_1']; ?>">
            			</div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label>Alternate Passpharase</label>
                            <input type="text" class="form-control" name="field_2" value="<?= $row['field_2']; ?>">
            			</div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;border-radius:0px;" name="btn_save_pm"><i class="fa fa-check"></i> Save changes</button>
        	</form>
        </div>
    </div>
    <div class="col-md">
        <?php
        //PAYEER
        $statement = "merchant_gateways WHERE name='Payeer'";
        $query_2 = $db->query("SELECT * FROM {$statement}");
        $row_2 = $query_2->fetch_assoc();
        ?>
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Payeer <b>Settings</b></strong>
            </div>
            <div class="card-body">
                <?php
        		if(isset($_POST['btn_save_py'])) {
        		    $status = protect($_POST['status']);
        			$currency = protect($_POST['currency']);
        			$percentage_fee = protect($_POST['percentage_fee']);
        			$fix_fee = protect($_POST['fix_fee']);
        			$field_1 = protect($_POST['field_1']);
        			$field_2 = protect($_POST['field_2']);
        			
        			if(!is_numeric($percentage_fee)) {
        				echo error("Please enter transaction fee with numbers.");
        			} else {
        				$update = $db->query("UPDATE merchant_gateways SET status='$status',currency='$currency',percentage_fee='$percentage_fee',fix_fee='$fix_fee',field_1='$field_1',field_2='$field_2' WHERE name='Payeer'");
        				$query_2 = $db->query("SELECT * FROM merchant_gateways WHERE name='Payeer'");
        				$row_2 = $query_2->fetch_assoc();
        				echo success("Your changes was saved successfully.");
        			}
        		}
        		?>
                <form action="" method="POST">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
            				<label>Status</label>
            				<select class="form-control" name="status">
            				    <option value="1" <?php if ($row_2['status'] == "1") { echo "selected"; } ?> class="form-control">Active</option>
            				    <option value="2" <?php if ($row_2['status'] == "2") { echo "selected"; } ?> class="form-control">Inactive</option>
            				</select>
            			</div>
                    </div>
                    <div class="col">
                        <div class="form-group">
            				<label>Currency</label>
            				<select class="form-control" name="currency" required>
                                <?php
                				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='1'");
            		            while($curr = $curr_Query->fetch_assoc()) {
            		                if($curr['currency'] == $row_2['currency']) { $sel = 'selected'; } else { $sel = ''; }
                						
                                    echo '<option value="'.$curr['currency'].'" '.$sel.'>'.$curr['currency'].'</option>';
                                }
                                ?>
                                <?php
                				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='2'");
            		            while($curr = $curr_Query->fetch_assoc()) {
                						if($curr['currency'] == $row_2['currency']) { $sel = 'selected'; } else { $sel = ''; }
                                    echo '<option value="'.$curr['currency'].'" '.$sel.'>'.$curr['currency'].'</option>';
                                }
                                ?>
                            </select>
            			</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
            				<label>Percentage Fee</label>
            				<input type="text" class="form-control" name="percentage_fee" value="<?= $row_2['percentage_fee']; ?>">
            				<small>Enter transaction fee in percentage without %. This transaction fee will be charged from recipient/sender of amount. Example: 3.4</small>
            			</div>
                    </div>
                    <div class="col">
                        <div class="form-group">
            				<label>Fixed/Flat Fee</label>
            				<input type="text" class="form-control" name="fix_fee" value="<?= $row_2['fix_fee']; ?>">
            				<small>Enter fixed merchant payment fee. Your default currency is <b><?php echo filter_var($settings['default_currency'], FILTER_SANITIZE_STRING); ?></b>, if client make merchant payment in other currency, this amount will be converted automatically.</small>
            			</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label>Payeer Shop ID</label>
                            <input type="text" class="form-control" name="field_1" value="<?= $row_2['field_1']; ?>">
            			</div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label>Payeer Secret Key</label>
                            <input type="text" class="form-control" name="field_2" value="<?= $row_2['field_2']; ?>">
            			</div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;border-radius:0px;" name="btn_save_py"><i class="fa fa-check"></i> Save changes</button>
        	</form>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md">
        <?php
        //Stripe
        $statement = "merchant_gateways WHERE name='Stripe'";
        $query_3 = $db->query("SELECT * FROM {$statement}");
        $row_3 = $query_3->fetch_assoc();
        ?>
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Stripe <b>Settings</b></strong>
            </div>
            <div class="card-body">
                <?php
        		if(isset($_POST['btn_save_St'])) {
        		    $status = protect($_POST['status']);
        			$currency = protect($_POST['currency']);
        			$percentage_fee = protect($_POST['percentage_fee']);
        			$fix_fee = protect($_POST['fix_fee']);
        			$field_1 = protect($_POST['field_1']);
        			$field_2 = protect($_POST['field_2']);
        			
        			if(!is_numeric($percentage_fee)) {
        				echo error("Please enter transaction fee with numbers.");
        			} else {
        				$update = $db->query("UPDATE merchant_gateways SET status='$status',currency='$currency',percentage_fee='$percentage_fee',fix_fee='$fix_fee',field_1='$field_1',field_2='$field_2' WHERE name='Stripe'");
        				$query_3 = $db->query("SELECT * FROM merchant_gateways WHERE name='Stripe'");
        				$row_3 = $query_3->fetch_assoc();
        				echo success("Your changes was saved successfully.");
        			}
        		}
        		?>
                <form action="" method="POST">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
            				<label>Status</label>
            				<select class="form-control" name="status">
            				    <option value="1" <?php if ($row_3['status'] == "1") { echo "selected"; } ?> class="form-control">Active</option>
            				    <option value="2" <?php if ($row_3['status'] == "2") { echo "selected"; } ?> class="form-control">Inactive</option>
            				</select>
            			</div>
                    </div>
                    <div class="col">
                        <div class="form-group">
            				<label>Currency</label>
            				<select class="form-control" name="currency" required>
                                <?php
                				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='1'");
            		            while($curr = $curr_Query->fetch_assoc()) {
            		                if($curr['currency'] == $row_3['currency']) { $sel = 'selected'; } else { $sel = ''; }
                						
                                    echo '<option value="'.$curr['currency'].'" '.$sel.'>'.$curr['currency'].'</option>';
                                }
                                ?>
                                <?php
                				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='2'");
            		            while($curr = $curr_Query->fetch_assoc()) {
                						if($curr['currency'] == $row_3['currency']) { $sel = 'selected'; } else { $sel = ''; }
                                    echo '<option value="'.$curr['currency'].'" '.$sel.'>'.$curr['currency'].'</option>';
                                }
                                ?>
                            </select>
            			</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
            				<label>Percentage Fee</label>
            				<input type="text" class="form-control" name="percentage_fee" value="<?= $row_3['percentage_fee']; ?>">
            				<small>Enter transaction fee in percentage without %. This transaction fee will be charged from recipient/sender of amount. Example: 3.4</small>
            			</div>
                    </div>
                    <div class="col">
                        <div class="form-group">
            				<label>Fixed/Flat Fee</label>
            				<input type="text" class="form-control" name="fix_fee" value="<?= $row_3['fix_fee']; ?>">
            				<small>Enter fixed merchant payment fee. Your default currency is <b><?php echo filter_var($settings['default_currency'], FILTER_SANITIZE_STRING); ?></b>, if client make merchant payment in other currency, this amount will be converted automatically.</small>
            			</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label>Stripe Secret Key</label>
                            <input type="text" class="form-control" name="field_1" value="<?= $row_3['field_1']; ?>">
            			</div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label>Stripe Public Key</label>
                            <input type="text" class="form-control" name="field_2" value="<?= $row_3['field_2']; ?>">
            			</div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;border-radius:0px;" name="btn_save_St"><i class="fa fa-check"></i> Save changes</button>
        	</form>
        </div>
    </div>
    <div class="col-md">
        <?php
        //Flutterwave
        $statement = "merchant_gateways WHERE name='Flutterwave'";
        $query_4 = $db->query("SELECT * FROM {$statement}");
        $row_4 = $query_4->fetch_assoc();
        ?>
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Flutterwave <b>Settings</b></strong>
            </div>
            <div class="card-body">
                <?php
        		if(isset($_POST['btn_save_Fw'])) {
        		    $status = protect($_POST['status']);
        			$currency = protect($_POST['currency']);
        			$percentage_fee = protect($_POST['percentage_fee']);
        			$fix_fee = protect($_POST['fix_fee']);
        			$field_1 = protect($_POST['field_1']);
        			$field_2 = protect($_POST['field_2']);
        			
        			if(!is_numeric($percentage_fee)) {
        				echo error("Please enter transaction fee with numbers.");
        			} else {
        				$update = $db->query("UPDATE merchant_gateways SET status='$status',currency='$currency',percentage_fee='$percentage_fee',fix_fee='$fix_fee',field_1='$field_1',field_2='$field_2' WHERE name='Flutterwave'");
        				$query_4 = $db->query("SELECT * FROM merchant_gateways WHERE name='Flutterwave'");
        				$row_4 = $query_4->fetch_assoc();
        				echo success("Your changes was saved successfully.");
        			}
        		}
        		?>
                <form action="" method="POST">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
            				<label>Status</label>
            				<select class="form-control" name="status">
            				    <option value="1" <?php if ($row_4['status'] == "1") { echo "selected"; } ?> class="form-control">Active</option>
            				    <option value="2" <?php if ($row_4['status'] == "2") { echo "selected"; } ?> class="form-control">Inactive</option>
            				</select>
            			</div>
                    </div>
                    <div class="col">
                        <div class="form-group">
            				<label>Currency</label>
            				<select class="form-control" name="currency" required>
                                <?php
                				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='1'");
            		            while($curr = $curr_Query->fetch_assoc()) {
            		                if($curr['currency'] == $row_4['currency']) { $sel = 'selected'; } else { $sel = ''; }
                						
                                    echo '<option value="'.$curr['currency'].'" '.$sel.'>'.$curr['currency'].'</option>';
                                }
                                ?>
                                <?php
                				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='2'");
            		            while($curr = $curr_Query->fetch_assoc()) {
                						if($curr['currency'] == $row_4['currency']) { $sel = 'selected'; } else { $sel = ''; }
                                    echo '<option value="'.$curr['currency'].'" '.$sel.'>'.$curr['currency'].'</option>';
                                }
                                ?>
                            </select>
            			</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
            				<label>Percentage Fee</label>
            				<input type="text" class="form-control" name="percentage_fee" value="<?= $row_4['percentage_fee']; ?>">
            				<small>Enter transaction fee in percentage without %. This transaction fee will be charged from recipient/sender of amount. Example: 3.4</small>
            			</div>
                    </div>
                    <div class="col">
                        <div class="form-group">
            				<label>Fixed/Flat Fee</label>
            				<input type="text" class="form-control" name="fix_fee" value="<?= $row_4['fix_fee']; ?>">
            				<small>Enter fixed merchant payment fee. Your default currency is <b><?php echo filter_var($settings['default_currency'], FILTER_SANITIZE_STRING); ?></b>, if client make merchant payment in other currency, this amount will be converted automatically.</small>
            			</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label>Flutterwave Secret Key</label>
                            <input type="text" class="form-control" name="field_1" value="<?= $row_4['field_1']; ?>">
            			</div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label>Flutterwave Public Key</label>
                            <input type="text" class="form-control" name="field_2" value="<?= $row_4['field_2']; ?>">
            			</div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;border-radius:0px;" name="btn_save_Fw"><i class="fa fa-check"></i> Save changes</button>
        	</form>
        </div>
    </div>
</div>
        



        
        