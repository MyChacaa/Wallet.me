<?php
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
?>
        

<div class="col-md-12">
	<div class="card">
        <div class="card-body">
		<?php
		if(isset($_POST['btn_save'])) {
			$title = protect($_POST['title']);
			$description = protect($_POST['description']);
			$keywords = protect($_POST['keywords']);
			$name = protect($_POST['name']);
			$url = protect($_POST['url']);
			$infoemail = protect($_POST['infoemail']);
			$supportemail = protect($_POST['supportemail']);
			$default_language = protect($_POST['default_language']);
			$default_currency = protect($_POST['default_currency']);
			$payfee_percentage = protect($_POST['payfee_percentage']);
			if(empty($title) or empty($description) or empty($keywords) or empty($default_language) or empty($default_currency) or empty($name) or empty($url) or empty($infoemail) or empty($supportemail)) {
				echo error("All fields are required."); 
			} elseif(!isValidURL($url)) { 
				echo error("Please enter valid site url address.");
			} elseif(!isValidEmail($infoemail)) { 
				echo error("Please enter valid info email address.");
			} elseif(!isValidEmail($supportemail)) { 
				echo error("Please enter valid support email address.");
			} elseif(!is_numeric($payfee_percentage)) {
				echo error("Please enter transaction fee with numbers.");
			} else {
				$update = $db->query("UPDATE settings SET payfee_percentage='$payfee_percentage',title='$title',description='$description',keywords='$keywords',default_language='$default_language',default_currency='$default_currency',name='$name',url='$url',infoemail='$infoemail',supportemail='$supportemail'");
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
    				<label>Enter Site Title</label>
        				<input type="text" class="form-control" name="title" value="<?php echo filter_var($settings['title'], FILTER_SANITIZE_STRING); ?>">
        			</div>
		        </div>
		        <div class="col">
		            <div class="form-group">
        				<label>Enter Site name</label>
        				<input type="text" class="form-control" name="name" value="<?php echo filter_var($settings['name'], FILTER_SANITIZE_STRING); ?>">
        			</div>
		        </div>
		    </div>
		    <div class="row">
		        <div class="col">
		            <div class="form-group">
        				<label>Write Description</label>
        				<textarea class="form-control" name="description" rows="2"><?php echo filter_var($settings['description'], FILTER_SANITIZE_STRING); ?></textarea>
        			</div>
		        </div>
		        <div class="col">
		            <div class="form-group">
        				<label>SEO Keywords</label>
        				<textarea class="form-control" name="keywords" rows="2"><?php echo filter_var($settings['keywords'], FILTER_SANITIZE_STRING); ?></textarea>
        			</div>
		        </div>
		    </div>
			
			<div class="row">
		        <div class="col">
		            <div class="form-group">
        				<label>Site url address</label>
        				<input type="text" class="form-control" name="url" value="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>">
        			</div>
		        </div>
		        <div class="col">
		            <div class="form-group">
						<label>Default language</label>
						<select class="form-control" name="default_language">
						<?php
						if ($handle = opendir('../languages')) {
							while (false !== ($file = readdir($handle)))
							{
								if ($file != "." && $file != ".." && $file != "index.php" && strtolower(substr($file, strrpos($file, '.') + 1)) == 'php')
								{
									$lang = str_ireplace(".php","",$file);
									if($settings['default_language'] == $lang) { $sel ='selected'; } else { $sel = ''; }
									echo '<option value="'.$lang.'" '.$sel.'>'.$lang.'</option>';
								}
							}
							closedir($handle);
						}
						?>
						</select>
					</div>
		        </div>
		    </div>
			
			<div class="row">
		        <div class="col">
		            <div class="form-group">
        				<label>No-Reply Email address</label>
        				<input type="text" class="form-control" name="infoemail" value="<?php echo filter_var($settings['infoemail'], FILTER_SANITIZE_STRING); ?>">
        			</div>
		        </div>
		        <div class="col">
		            <div class="form-group">
        				<label>Support Email address</label>
        				<input type="text" class="form-control" name="supportemail" value="<?php echo filter_var($settings['supportemail'], FILTER_SANITIZE_STRING); ?>">
        			</div>
		        </div>
		    </div>
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
        				<small>Wants to change currency? Contact us</small>
			        </div>
		        </div>
		        <div class="col">
		            <div class="form-group">
        				<label>Transaction Fee</label>
        				<input type="text" class="form-control" name="payfee_percentage" value="<?php echo filter_var($settings['payfee_percentage'], FILTER_SANITIZE_STRING); ?>">
        				<small>Enter transaction fee in percentage without %. This transaction fee will be charged from recipient of amount. Example: 3.4</small>
        			</div>
		        </div>
		    </div>
			
			
										
			
			
			<button type="submit" class="btn btn-primary" name="btn_save"><i class="fa fa-check"></i> Save changes</button>
		</form>
	</div>
</div>
</div>

<div class="col-md-12">
	<div class="card">
        <div class="card-body">

            <?php
              
				if (isset($_POST['ce_btn'])){
				$CEAction = protect($_POST['ce_btn']);
				} else {
				$CEAction = "";
				}
              if(isset($CEAction) && $CEAction == "save") {
                if(isset($_POST['enable_recaptcha'])) { $enable_recaptcha = 1; } else { $enable_recaptcha = '0'; }
                $recaptcha_publickey = protect($_POST['recaptcha_publickey']);
                $recaptcha_privatekey = protect($_POST['recaptcha_privatekey']);
                if($enable_recaptcha == "1" && empty($recaptcha_publickey)) {
                    echo error("Please enter a reCaptcha public key.");
                } elseif($enable_recaptcha == "1" && empty($recaptcha_privatekey)) {
                    echo error("Please enter a reCaptcha private key.");
                } else {
                    $update = $db->query("UPDATE settings SET enable_recaptcha='$enable_recaptcha',recaptcha_publickey='$recaptcha_publickey',recaptcha_privatekey='$recaptcha_privatekey'");
                    $settingsQuery = $db->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
                    $settings = $settingsQuery->fetch_assoc();
                    echo success("Your changes was saved successfully.");
                }
              }
              ?>

            <form action="" method="POST">
                <div class="form-check">
                    <div class="checkbox">
                        <label for="checkbox1" class="form-check-label ">
                            <input type="checkbox" id="checkbox1" name="enable_recaptcha" <?php if($settings['enable_recaptcha'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Enable Google reCaptcha
                        </label>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label>reCaptcha Public Key</label>
                            <input type="text" class="form-control" name="recaptcha_publickey" value="<?php echo filter_var($settings['recaptcha_publickey'], FILTER_SANITIZE_STRING); ?>">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label>reCaptcha Private Key</label>
                            <input type="text" class="form-control" name="recaptcha_privatekey" value="<?php echo filter_var($settings['recaptcha_privatekey'], FILTER_SANITIZE_STRING); ?>">
                        </div>
                    </div>
                </div>
                
                
                <button type="submit" class="btn btn-primary" name="ce_btn" value="save"><i class="fa fa-check"></i> Save Changes</button>
            </form>
        </div>
    </div>
</div>


<div class="col-md-12">
	<div class="card">
        <div class="card-body">
            <?php
    		if(isset($_POST['btn_ver'])) {
                if(isset($_POST['require_email_verify'])) { $require_email_verify = 1; } else { $require_email_verify = 0; }
                if(isset($_POST['require_document_verify'])) { $require_document_verify = 1; } else { $require_document_verify = 0; }
                $limit_maxamount_sent = protect($_POST['limit_maxamount_sent']);
                $limit_maxtxs_sent = protect($_POST['limit_maxtxs_sent']);
    			$update = $db->query("UPDATE settings SET require_email_verify='$require_email_verify',require_document_verify='$require_document_verify'");
    			$settingsQuery = $db->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
    			$settings = $settingsQuery->fetch_assoc();
    			echo success("Your changes was saved successfully.");
    		}
    		?>
            <form action="" method="POST">
            <div class="checkbox">
				<label>
				    <input type="checkbox" name="require_email_verify" value="yes" <?php if($settings['require_email_verify'] == "1") { echo 'checked'; }?>> Request e-mail verification from the user
				</label>
            </div>
            <hr/>
            <div class="checkbox">
				<label>
				    <input type="checkbox" name="require_document_verify" value="yes" <?php if($settings['require_document_verify'] == "1") { echo 'checked'; }?>> Request document verification (KYC) from the user
				</label>
            </div>
            <hr/>
			<button type="submit" class="btn btn-primary" name="btn_ver"><i class="fa fa-check"></i> Save changes</button>
		</form>
            
        </div>
    </div>
</div>
