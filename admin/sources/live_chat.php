<?php
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

?>
<div class="card">
    <div class="card-header">
        <strong class="card-title">Live Chat <b>Code</b></strong>
    </div>
    <div class="card-body">
        <?php
        
        if(isset($_POST['live_chat'])) {
            $live_chat = addslashes($_POST['live_chat_code']);
            $update = $db->query("UPDATE settings SET live_chat_code='$live_chat'");
			$settingsQuery = $db->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
			$settings = $settingsQuery->fetch_assoc();
			echo success("Live Chat Code Updated.");
        }
        
        ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Enter Live Chat Code</label>
                <small>Recommended : Tawk.to</small>
                <textarea class="form-control" name="live_chat_code" rows="12"><?php echo $settings['live_chat_code']; ?></textarea>    
            </div>
    </div>
    <button type="submit" class="btn btn-primary" name="live_chat" value="live_chat" style="border-radius:0px;"><i class="fa fa-check-circle"></i>&nbsp;&nbsp;Save Changes</button>
    </form>
</div>