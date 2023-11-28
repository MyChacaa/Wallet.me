<?php
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

?>

<div class="card">
    <div class="card-header">
        <strong class="card-title">E-Voucher <b>Settings</b></strong>
    </div>
    
    <div class="card-body">
        <?php
        
        $evoucher_settingsQuery = $db->query("SELECT * FROM evoucher_settings ORDER BY id DESC LIMIT 1");
		$evoucher_settings = $evoucher_settingsQuery->fetch_assoc();
		
		if(isset($_POST['btn_save'])) {
            $FormBTN = protect($_POST['btn_save']);
            if($FormBTN == "btn_save") {
                $status = protect($_POST['status']);
                $digit = protect($_POST['digit']);
                $creation_fee_fix = protect($_POST['creation_fee_fix']);
                $creation_fee_per = protect($_POST['creation_fee_per']);
                
                if (empty($creation_fee_fix)) {
                    $creation_fee_fix = "0.00";
                }
                if (empty($creation_fee_per)) {
                    $creation_fee_per = "0";
                }
                $update = $db->query("UPDATE evoucher_settings SET status='$status',digit='$digit',creation_fee_fix='$creation_fee_fix',creation_fee_per='$creation_fee_per' WHERE id='1'");
                $evoucher_settingsQuery = $db->query("SELECT * FROM evoucher_settings ORDER BY id DESC LIMIT 1");
		        $evoucher_settings = $evoucher_settingsQuery->fetch_assoc();
                echo success("Changes has been saved.");
            }
		}
        ?>
        <form method="POST" action="">
            <div class="row">
    	        <div class="col">
    	            <div class="form-group">
    				<label>E Voucher Status</label>
        				<select class="form-control" name="status">
        				    <option value="1" <?php if($evoucher_settings['status'] == "1") { echo 'selected'; } ?>>Enable</option>
        				    <option value="0" <?php if($evoucher_settings['status'] == "0") { echo 'selected'; } ?>>Disable</option>
        				</select>
        			</div>
    	        </div>
    	        <div class="col">
    	            <div class="form-group">
        				<label>E Voucher Digits</label>
        				<select class="form-control" name="digit">
        				    <option value="8" <?php if($evoucher_settings['digit'] == "8") { echo 'selected'; } ?>>8 Digit</option>
        				    <option value="12" <?php if($evoucher_settings['digit'] == "12") { echo 'selected'; } ?>>12 Digit</option>
        				    <option value="16" <?php if($evoucher_settings['digit'] == "16") { echo 'selected'; } ?>>16 Digit</option>
        				</select>
        			</div>
    	        </div>
    	    </div>
            <div class="row">
    	        <div class="col">
    	            <div class="form-group">
    				<label>E Voucher Fix Fee (Creation)</label>
        				<input type="text" class="form-control" name="creation_fee_fix" value="<?= $evoucher_settings['creation_fee_fix'] ?>">
        			</div>
    	        </div>
    	        <div class="col">
    	            <div class="form-group">
        				<label>E Voucher Percentage Fee (Creation)</label>
        				<input type="text" class="form-control" name="creation_fee_per" value="<?= $evoucher_settings['creation_fee_per'] ?>">
        			</div>
    	        </div>
    	    </div>
    	    <button type="submit" class="btn btn-primary" name="btn_save" value="btn_save"><i class="fa fa-check"></i> Save changes</button>
	    </form>
    </div>
</div>