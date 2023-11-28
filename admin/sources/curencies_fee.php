<div class="card">
    <div class="card-header">
        <strong class="card-title">Fee <b>Setting</b></strong>
    </div>
    <div class="card-body">
        <?php
    
		if(isset($_POST['fee_curr'])) {
		   $fee_currency = protect($_POST['fee_currency']);
		   $update = $db->query("UPDATE settings SET curcnv_fee_percentage='$fee_currency'");
		   $settingsQuery = $db->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
			$settings = $settingsQuery->fetch_assoc();
		   echo success("Fee has been updated.");
		}
    
    
        ?>
        <form action="" method="POST">
        <div class="row">
            <div class="col">
	            <div class="form-group">
    				<label>Currency Converter Fee in Percentage</label>
    				<input class="form-control" name="fee_currency" value="<?php echo $settings['curcnv_fee_percentage']; ?>">
    				
    				<small>Don't include <b>%</b> sign. Ex: 1.4</small>
    			</div>
	        </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary" name="fee_curr" value="fee_curr" style="border-radius:0px;"><i class="fa fa-check-circle"></i>&nbsp;&nbsp;&nbsp;Save Changes</button>
    </form>
</div>
