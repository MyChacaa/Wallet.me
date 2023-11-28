<?php
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

?>
<div class="card">
    <div class="card-header">
        <strong class="card-title">Merchant <b>Fee Setup</b></strong>
    </div>
    <div class="card-body">
        <?php
		if(isset($_POST['btn_save'])) {
		    $merchant_percentage = protect($_POST['merchant_percentage']);
			$merchant_fixed = protect($_POST['merchant_fixed']);
			
			if(!is_numeric($merchant_percentage)) {
				echo error("Please enter transaction fee with numbers.");
			} else {
				$update = $db->query("UPDATE settings SET merchant_percentage='$merchant_percentage',merchant_fixed='$merchant_fixed'");
				$settingsQuery = $db->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
				$settings = $settingsQuery->fetch_assoc();
				echo success("Your changes was saved successfully.");
			}
		}
		?>
		
		<form action="" method="POST">
        <div class="row">
            <div class="col">
                <div class="form-group">
    				<label>Merchant Payment Fee (Percentage)</label>
    				<input type="text" class="form-control" name="merchant_percentage" value="<?php echo filter_var($settings['merchant_percentage'], FILTER_SANITIZE_STRING); ?>">
    				<small>Enter transaction fee in percentage without %. This transaction fee will be charged from recipient/sender of amount. Example: 3.4</small>
    			</div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
    				<label>Merchant Payment Fee (Fixed Flat)</label>
    				<input type="text" class="form-control" name="merchant_fixed" value="<?php echo filter_var($settings['merchant_fixed'], FILTER_SANITIZE_STRING); ?>">
    				<small>Enter fixed merchant payment fee. Your default currency is <b><?php echo filter_var($settings['default_currency'], FILTER_SANITIZE_STRING); ?></b>, if client make merchant payment in other currency, this amount will be converted automatically.</small>
    			</div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary" style="width:100%;border-radius:0px;" name="btn_save"><i class="fa fa-check"></i> Save changes</button>
		</form>
</div>
    