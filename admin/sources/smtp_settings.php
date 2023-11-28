<?php
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
?><br>
		
        

<div class="col-md-12">
	<div class="card">
        <div class="card-body">
		<?php
		if(isset($_POST['btn_save'])) {
			if(isset($_POST['SMTPAuth'])) { $SMTPAuth = true; $SMTPAuth_c = 'true'; } else { $SMTPAuth = false; $SMTPAuth_c = 'false'; }
			$smtp_host = protect($_POST['smtp_host']);
			$smtp_port = protect($_POST['smtp_port']);
			$smtp_user = protect($_POST['smtp_user']);
			$smtp_pass = protect($_POST['smtp_pass']);
			if(isset($_POST['smtp_ssl'])) { $ssl = 1; } else { $ssl = 0; }
			
			if($SMTPAuth == true && empty($smtp_host) or empty($smtp_port) or empty($smtp_user) or empty($smtp_pass)) { echo error("Please enter a SMTP settings."); }
			else {
				$contents = '<?php
                if(!defined("PWV1_INSTALLED")){
                header("HTTP/1.0 404 Not Found");
                exit;
                }
                
                $smtpconf = array();
                $smtpconf["host"] = "'.$smtp_host.'"; // SMTP SERVER IP/HOST
                $smtpconf["user"] = "'.$smtp_user.'";	// SMTP AUTH USERNAME if SMTPAuth is true
                $smtpconf["pass"] = "'.$smtp_pass.'";	// SMTP AUTH PASSWORD if SMTPAuth is true
                $smtpconf["port"] = "'.$smtp_port.'";	// SMTP SERVER PORT
                $smtpconf["ssl"] = "'.$ssl.'"; // 1 -  YES, 0 - NO
                $smtpconf["SMTPAuth"] = '.$SMTPAuth_c.'; // true / false
                ?>
                ';				
				$update = file_put_contents("../configs/smtp.settings.php",$contents);
				if($update) {
					$smtpconf["host"] = $smtp_host; // SMTP SERVER IP/HOST
					$smtpconf["user"] = $smtp_user;		// SMTP AUTH USERNAME if SMTPAuth is true
					$smtpconf["pass"] = $smtp_pass;	// SMTP AUTH PASSWORD if SMTPAuth is true
					$smtpconf["port"] = $smtp_port;	// SMTP SERVER PORT
					$smtpconf["ssl"] = $ssl; // 1 -  YES, 0 - NO
					$smtpconf["SMTPAuth"] = $SMTPAuth; // true / false
					echo success("Your changes was saved successfully.");
				} else {
					echo error("Please set chmod 777 of file <b>includes/smtp.settings.php</b>.");
				}
			}
		}
		?>
		<form action="" method="POST">
			<div class="form-check">
				<div class="checkbox">
					<label for="checkbox1" class="form-check-label ">
						<input type="checkbox" id="checkbox1" name="SMTPAuth" <?php if($smtpconf['SMTPAuth'] == true) { echo 'checked'; } ?> value="1" class="form-check-input"> SMTP Authentication
					</label>
				</div>
			</div>
			<div class="row">
			    <div class="col">
			        <div class="form-group">
        				<label>SMTP Host</label>
        				<input type="text" class="form-control" name="smtp_host" value="<?php echo filter_var($smtpconf['host'], FILTER_SANITIZE_STRING); ?>">
        			</div>
			    </div>
			    <div class="col">
			        <div class="form-group">
        				<label>SMTP Port</label>
        				<input type="text" class="form-control" name="smtp_port" value="<?php echo filter_var($smtpconf['port'], FILTER_SANITIZE_STRING); ?>">
        			</div>
			    </div>
			</div>
			<div class="row">
			    <div class="col">
			        <div class="form-group">
        				<label>SMTP Username</label>
        				<input type="text" class="form-control" name="smtp_user" value="<?php echo filter_var($smtpconf['user'], FILTER_SANITIZE_STRING); ?>">
        			</div>
			    </div>
			    <div class="col">
			        <div class="form-group">
        				<label>SMTP Password</label>
        				<input type="text" class="form-control" name="smtp_pass" value="<?php echo filter_var($smtpconf['pass'], FILTER_SANITIZE_STRING); ?>">
        			</div>
			    </div>
			</div>
			
			
			
			
			<div class="form-check">
				<div class="checkbox">
					<label for="checkbox2" class="form-check-label ">
						<input type="checkbox" id="checkbox2" name="smtp_ssl" <?php if($smtpconf['ssl'] == 1) { echo 'checked'; } ?> value="1" class="form-check-input"> Secure SSL/TLS Connection
					</label>
				</div>
			</div>
			<br>
			<button type="submit" class="btn btn-primary" name="btn_save"><i class="fa fa-check"></i> Save changes</button>
		</form>
	</div>
</div>
</div>