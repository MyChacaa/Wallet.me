<?php
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

?>
<div class="card">
    <div class="card-header">
        <strong class="card-title">Send & Request Money <b>Fee Setup</b></strong>
    </div>
    <div class="card-body">
        <?php
		if(isset($_POST['btn_save'])) {
		    $payfee_type = protect($_POST['payfee_type']);
			$payfee_percentage = protect($_POST['payfee_percentage']);
			$payfee_fixed = protect($_POST['payfee_fixed']);
			
			if(!is_numeric($payfee_percentage)) {
				echo error("Please enter transaction fee with numbers.");
			} else {
				$update = $db->query("UPDATE settings SET payfee_type='$payfee_type',payfee_percentage='$payfee_percentage',payfee_fixed='$payfee_fixed'");
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
    				<label>Who will pay the fee?</label>
    				<select class="form-control" name="payfee_type">
    				    <option value="1" <?php if ($settings['payfee_type'] == "1") { echo "selected"; } ?> >Sender will pay</option>
    				    <option value="2" <?php if ($settings['payfee_type'] == "2") { echo "selected"; } ?> >Receiver will pay</option>
    				</select>
    			</div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
    				<label>Transaction Fee (Percentage)</label>
    				<input type="text" class="form-control" name="payfee_percentage" value="<?php echo filter_var($settings['payfee_percentage'], FILTER_SANITIZE_STRING); ?>">
    				<small>Enter transaction fee in percentage without %. This transaction fee will be charged from recipient/sender of amount. Example: 3.4</small>
    			</div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
    				<label>Transaction Fee (Fixed Flat)</label>
    				<input type="text" class="form-control" name="payfee_fixed" value="<?php echo filter_var($settings['payfee_fixed'], FILTER_SANITIZE_STRING); ?>">
    				<small>Enter fixed send/request money fee. Your default currency is <b><?php echo filter_var($settings['default_currency'], FILTER_SANITIZE_STRING); ?></b>, if client send/request in other currency, this amount will be converted automatically.</small>
    			</div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary" style="width:100%;border-radius:0px;" name="btn_save"><i class="fa fa-check"></i> Save changes</button>
		</form>
</div>
    