<?php
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

?>
<div class="card">
    <div class="card-header">
        <strong class="card-title">Google Analytics <b>Code</b></strong>
    </div>
    <div class="card-body">
        <?php
        
        if(isset($_POST['bt_save'])) {
            $google_analytics_code = addslashes($_POST['google_analytics_code']);
            $update = $db->query("UPDATE settings SET google_analytics_code='$google_analytics_code'");
			$settingsQuery = $db->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
			$settings = $settingsQuery->fetch_assoc();
			echo success("Live Chat Code Updated.");
        }
        
        ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Enter Google Analytics Code</label>
                <textarea class="form-control" name="google_analytics_code" rows="9"><?php echo $settings['google_analytics_code']; ?></textarea>    
            </div>
    </div>
    <button type="submit" class="btn btn-primary" name="bt_save" value="bt_save" style="border-radius:0px;"><i class="fa fa-check-circle"></i>&nbsp;&nbsp;Save Changes</button>
    </form>
</div>