<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
?>
<script type="text/javascript">
$(document).ready(function(){
	$(".loginInp").focus(function(){
		$(this).parent().addClass("logFocusInp");
	});
	$(".loginInp").focusout(function(){
		$(this).parent().removeClass("logFocusInp");
	});
	$(".regAccTypeBtn").click(function(){
		$(this).parent().children(".inpRegAccount").trigger("click");
		$(".regAccTypeBtn").removeClass("regActive");
		$(this).addClass("regActive");
	});
});
</script>
    <div class="rightLogin cwhite">
		<?php
		if ($m["registration"] !== "1") {
            echo error("Registration is OFF Currently, Please contact support.");
        } else {
			
		if(isset($_POST['pw_register'])) {
			$FormBTN = protect($_POST['pw_register']);
        
        if($FormBTN == "reg") {
            $first_name = protect($_POST['first_name']);
            $last_name = protect($_POST['last_name']);
            $email = protect($_POST['email']);
            $password = protect($_POST['password']);
            $cpassword = protect($_POST['cpassword']);
            $country = protect($_POST['country']);
            $city = protect($_POST['city']);
            $zip_code = protect($_POST['zip_code']);
            $address = protect($_POST['address']);
            $ref = protect($_POST['ref']);
            if($ref == ""){
                $ref = 0;
            }
            $account_type = protect($_POST['account_type']); // 1= personal, 2=business
			
            
			if (isset($_POST['g-recaptcha-response'])){
				$recaptcha_response = protect($_POST['g-recaptcha-response']);
			} else {
				$recaptcha_response = "";
			}

            $accept_tou = protect($_POST['accept_tou']);
            if($accept_tou == "yes") { $accept_tou = '1'; } else { $accept_tou = '0'; }
            if(empty($first_name) or empty($last_name) or empty($account_type) or empty($email) or empty($password) or empty($cpassword) or empty($country) or empty($city) or empty($zip_code) or empty($address)) {
                echo error($lang['error_20']);
            } elseif(!isValidEmail($email)) {
                echo error($lang['error_45']);
            } elseif($settings['enable_recaptcha'] == "1" && !VerifyGoogleRecaptcha($recaptcha_response)) {
                echo error($lang['error_52']);  
            } elseif(PW_CheckUser($email)==true) {
                echo error($lang['error_46']);
            } elseif(strlen($password)<8) { 
                echo error($lang['error_47']);
            } elseif($password !== $cpassword) {
                echo error($lang['error_48']);
            } elseif($accept_tou==0) {
                echo error($lang['error_49']);
            } elseif($country !== "United Kingdom" && !is_numeric($zip_code)) { 
                echo error($lang['error_21']);
            } elseif($country == "United Kingdom" && postcode_check($zip_code) == false) {
				echo error($lang['error_21']);	
			} else {
                $password = password_hash($password, PASSWORD_DEFAULT);
                $ip = $_SERVER['REMOTE_ADDR'];
                $time = time();
                $insert = $db->query("INSERT users (password,email,email_verified,status,account_type,ip,signup_time,first_name,last_name,country,city,zip_code,address,ref1) VALUES ('$password','$email','1','1','$account_type','$ip','$time','$first_name','$last_name','$country','$city','$zip_code','$address','$ref')");
                $GetU = $db->query("SELECT * FROM users WHERE email='$email'");
                $gu = $GetU->fetch_assoc();
                $insert = $db->query("INSERT users_wallets (uid,amount,currency) VALUES ('$gu[id]','0','$settings[default_currency]')");
                 if($settings['require_email_verify'] == "1") {
                    $email_hash = randomHash(25);
                    $update = $db->query("UPDATE users SET status='2',email_hash='$email_hash',email_verified='0' WHERE email='$email'");
                    PW_EmailSys_Send_Email_Verification($email);
                    echo success($lang['success_22']);
                } else {
                    echo success($lang['success_23']);
                }
            }
        }
		}
        ?>
		<form action="" method="POST">
			<?php
			
			if (isset($_COOKIE['ref'])){
			$ref = $_COOKIE['ref'];
			} else {
			$ref = "";
			}
			
			?>
			<input type="hidden" class="form-control" name="ref" value="<?php echo $ref; ?>">
			<h2 class="fw2 mt5 mb10"><span class="fw3">Become a member!</span></h2>

			<br>
            <div class="row">
				<div class="col-md-12">
					<p class="mb15">Select Account Type:</p>
					<div class="flex">
						<div class="w100">
							<span class="w100 regAccTypeBtn pointer us inline">Personal</span>
							<input type="radio" name="account_type" class="inpRegAccount" value="1" style="display: none;">
						</div>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<div class="w100">
							<span class="w100 regAccTypeBtn pointer us inline">Business</span>
							<input type="radio" name="account_type" class="inpRegAccount" value="2" style="display: none;">
						</div>
					</div>
				</div>
			</div>
            <div class="row">
				<div class="col-md-6">
					<input type="text" name="first_name" placeholder="<?php echo filter_var($lang['field_11']); ?>" class="inpReg">
				</div>
				<div class="col-md-6">
					<input type="text" name="last_name" placeholder="<?php echo filter_var($lang['field_12']); ?>" class="inpReg">
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<input type="email" name="email" placeholder="<?php echo filter_var($lang['field_25']); ?>" class="inpReg">
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<input type="password" name="password" placeholder="<?php echo filter_var($lang['field_29']); ?>" class="inpReg">
				</div>
				<div class="col-md-6">
					<input type="password" name="cpassword" placeholder="<?php echo filter_var($lang['field_30']); ?>" class="inpReg">
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<select name="country" class="inpReg" required>
					    <option value=""><?php echo filter_var($lang['field_31']); ?></option>
                        <?php
        				$country_Query = $db->query("SELECT * FROM country WHERE status='1'");
    		            while($country = $country_Query->fetch_assoc()) {
                            echo '<option value="'.$country['code'].'">'.$country['name'].'</option>';
                        }
                        ?>
					</select>
				</div>
				<div class="col-md-6">
					<input type="text" name="city" placeholder="<?php echo filter_var($lang['field_14']); ?>" class="inpReg">
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-6">
					<input type="text" name="zip_code" placeholder="<?php echo filter_var($lang['field_15']); ?>" class="inpReg">
				</div>
				<div class="col-md-6">
					<input type="text" name="address" placeholder="<?php echo filter_var($lang['field_16']); ?>" class="inpReg">
				</div>
			</div>
			<?php if($settings['enable_recaptcha'] == "1") { ?>
            <br>
            <center><script src="https://www.google.com/recaptcha/api.js" async defer></script>
            <div class="g-recaptcha" data-sitekey="<?php echo filter_var($settings['recaptcha_publickey']); ?>"></div></center>
            <br>
            <?php } ?>
			<div class="row">
				<dir class="col-md-12">
					<label class="flex fw2 fs14 us">
						<div class="cont">
							<input type="checkbox" id="accept_tou" name="accept_tou" value="yes" class="checkboxDef">
						</div>
						&nbsp;&nbsp;&nbsp;
						<p>I agree that I have read the <a href="javascript:" class="cwhite">Terms & conditions</a>.</p>
					</label>
				</dir>
				<div class="col-md-6">
					<button type="submit" name="pw_register" value="reg" class="regPageBtn w100"><?php echo filter_var($lang['btn_28']); ?>&nbsp;&nbsp; <i class="fa fa-caret-right"></i> </button>
				</div>
			</div>
		</form>
	</div> <!-- rightLogin -->
	<?php } ?>