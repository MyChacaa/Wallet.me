<?php
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

?>

<div class="card">
    <div class="card-header">
        <strong class="card-title">Manage <b>Currencies</b></strong>
    </div>
    
    <div class="card-body">
        <?php
    
		if(isset($_POST['de_curr'])) {
		   $default_currency = protect($_POST['default_currency']);
		   $update = $db->query("UPDATE settings SET default_currency='$default_currency'");
		   $update = $db->query("UPDATE currency SET status='1', default_curr='1' WHERE currency='$default_currency'");
		   $settingsQuery = $db->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
		   $settings = $settingsQuery->fetch_assoc();
		   echo success("Your changes was saved successfully.");
		}
    
    
        ?>
        <form action="" method="POST">
        <div class="row">
            <div class="col">
	            <div class="form-group">
    				<label>Default wallet currency</label>
    				<select class="form-control" name="default_currency">
    				<?php
                    $currencies = getFiatCurrencies();
                    foreach($currencies as $code=>$name) {
    						if($settings['default_currency'] == $code) { $sel = 'selected'; } else { $sel = ''; }
                        echo '<option value="'.$code.'" '.$sel.'>'.$name.'</option>';
                    }
                    ?>
    				</select>
    			</div>
	        </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary" name="de_curr" value="de_curr" style="border-radius:0px;"><i class="fa fa-check-circle"></i>&nbsp;&nbsp;&nbsp;Save Changes</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <strong class="card-title">Add Others <b>Currencies</b></strong>
    </div>
    <div class="card-body">
        <?php
    
		if(isset($_POST['add_curr'])) {
		   $add_currency = protect($_POST['add_currency']);
		   $update = $db->query("UPDATE currency SET status='1', default_curr='2' WHERE currency='$add_currency'");
		   echo success("Currency has been added.");
		}
    
    
        ?>
        <form action="" method="POST">
        <div class="row">
            <div class="col">
	            <div class="form-group">
    				<label>Select wallet currency you wants to add</label>
    				<select class="form-control" name="add_currency">
    				<?php
    				$curr_Query = $db->query("SELECT * FROM currency WHERE status='2' and default_curr='2'");
		            while($curr = $curr_Query->fetch_assoc()) {
    						
                        echo '<option value="'.$curr['currency'].'">'.$curr['currency'].'</option>';
                    }
                    ?>
    				</select>
    			</div>
	        </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary" name="add_curr" value="add_curr" style="border-radius:0px;"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;&nbsp;Add Currency</button>
    </form>
</div>
