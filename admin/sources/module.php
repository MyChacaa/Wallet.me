<?php
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
?>
<div class="card">
    <div class="card-header">
        <strong class="card-title">Modules <b>Control</b></strong>
    </div>
    <div class="card-body">
        
        <?php
		if(isset($_POST['btn_save'])) {
		    
			if(isset($_POST['deposit'])) { $deposit = 1; } else { $deposit = 0; }
			if(isset($_POST['withdrawal'])) { $withdrawal = 1; } else { $withdrawal = 0; }
			if(isset($_POST['send_money'])) { $send_money = 1; } else { $send_money = 0; }
			if(isset($_POST['request_money'])) { $request_money = 1; } else { $request_money = 0; }
			if(isset($_POST['currency_convert'])) { $currency_convert = 1; } else { $currency_convert = 0; }
			
			if(isset($_POST['disputes'])) { $disputes = 1; } else { $disputes = 0; }
			if(isset($_POST['merchants'])) { $merchants = 1; } else { $merchants = 0; }
			if(isset($_POST['payment_link'])) { $payment_link = 1; } else { $payment_link = 0; }
			if(isset($_POST['referral_system'])) { $referral_system = 1; } else { $referral_system = 0; }
			if(isset($_POST['support_ticket'])) { $support_ticket = 1; } else { $support_ticket = 0; }
			
			if(isset($_POST['live_chat'])) { $live_chat = 1; } else { $live_chat = 0; }
			if(isset($_POST['google_analytics'])) { $google_analytics = 1; } else { $google_analytics = 0; }
			if(isset($_POST['registration'])) { $registration = 1; } else { $registration = 0; }
			if(isset($_POST['forget_password'])) { $forget_password = 1; } else { $forget_password = 0; }
			if(isset($_POST['fixed_deposit'])) { $fixed_deposit = 1; } else { $fixed_deposit = 0; }
			if(isset($_POST['escrow'])) { $escrow = 1; } else { $escrow = 0; }

		
			$contents = '<?php
if(!defined("PWV1_INSTALLED")){
header("HTTP/1.0 404 Not Found");
exit;
}
                
$m = array();
$m["deposit"] = "'.$deposit.'"; // Deposit
$m["withdrawal"] = "'.$withdrawal.'"; // Withdrawal
$m["send_money"] = "'.$send_money.'"; // Send Money
$m["request_money"] = "'.$request_money.'"; // Request Money
$m["currency_convert"] = "'.$currency_convert.'"; // Currency Convert
$m["disputes"] = "'.$disputes.'"; // Dispute
$m["merchants"] = "'.$merchants.'"; // Merchants
$m["payment_link"] = "'.$payment_link.'"; // Payment Link
$m["referral_system"] = "'.$referral_system.'"; // Referral System
$m["support_ticket"] = "'.$support_ticket.'"; // Support Ticket
$m["live_chat"] = "'.$live_chat.'"; // Live Chat
$m["google_analytics"] = "'.$google_analytics.'"; // Google Analytics
$m["registration"] = "'.$registration.'"; // User Registration
$m["forget_password"] = "'.$forget_password.'"; // Forget Password
$m["fixed_deposit"] = "'.$fixed_deposit.'"; // Fixed Deposit
$m["escrow"] = "'.$escrow.'"; // Escrow
?>
            ';				
			$update = file_put_contents("../configs/module.php",$contents);
			if($update) {
				$m["deposit"] = $deposit; // Deposit
				$m["withdrawal"] = $withdrawal; // Withdrawal
                $m["send_money"] = $send_money; // Send Money
                $m["request_money"] = $request_money; // Request Money
                $m["currency_convert"] = $currency_convert; // Currency Convert
                $m["disputes"] = $disputes; // Dispute
                $m["merchants"] = $merchants; // Merchants
                $m["payment_link"] = $payment_link; // Payment Link
                $m["referral_system"] = $referral_system; // Referral System
                $m["support_ticket"] = $support_ticket; // Support Ticket
                $m["live_chat"] = $live_chat; // Live Chat
                $m["google_analytics"] = $google_analytics; // Google Analytics
                $m["registration"] = $registration; // User Registration
                $m["forget_password"] = $forget_password; // Forget Password
                $m["fixed_deposit"] = $fixed_deposit; // Fixed Deposit
                $m["escrow"] = $escrow; // Escrow
				echo success("Your changes was saved successfully.");
			} else {
				echo error("Please set chmod 777 of file <b>config/module.php</b>.");
			}
			
		}
		?>
        
        <form action="" method="POST">
            <div class="row">
                <div class="col">
                    <div class="checkbox">
                		<label>
                		  <input type="checkbox" name="deposit" value="yes" <?php if($m['deposit'] == "1") { echo 'checked'; }?>> Deposits
                		</label>
                	</div>
                	<div class="checkbox">
                		<label>
                		  <input type="checkbox" name="withdrawal" value="yes" <?php if($m['withdrawal'] == "1") { echo 'checked'; }?>> Withdrawals
                		</label>
                	</div>
                	<div class="checkbox">
                		<label>
                		  <input type="checkbox" name="send_money" value="yes" <?php if($m['send_money'] == "1") { echo 'checked'; }?>> Send Money
                		</label>
                	</div>
                	<div class="checkbox">
                		<label>
                		  <input type="checkbox" name="request_money" value="yes" <?php if($m['request_money'] == "1") { echo 'checked'; }?>> Request Money
                		</label>
                	</div>
                	<div class="checkbox">
                		<label>
                		  <input type="checkbox" name="currency_convert" value="yes" <?php if($m['currency_convert'] == "1") { echo 'checked'; }?>> Currency Conversion
                		</label>
                	</div>
                </div>
                <div class="col">
                    <div class="checkbox">
                		<label>
                		  <input type="checkbox" name="disputes" value="yes" <?php if($m['disputes'] == "1") { echo 'checked'; }?>> Disputes
                		</label>
                	</div>
                	<div class="checkbox">
                		<label>
                		  <input type="checkbox" name="merchants" value="yes" <?php if($m['merchants'] == "1") { echo 'checked'; }?>> Merchants
                		</label>
                	</div>
                	<div class="checkbox">
                		<label>
                		  <input type="checkbox" name="payment_link" value="yes" <?php if($m['payment_link'] == "1") { echo 'checked'; }?>> Payment Links
                		</label>
                	</div>
                	<div class="checkbox">
                		<label>
                		  <input type="checkbox" name="referral_system" value="yes" <?php if($m['referral_system'] == "1") { echo 'checked'; }?>> Referral System
                		</label>
                	</div>
                	<div class="checkbox">
                		<label>
                		  <input type="checkbox" name="support_ticket" value="yes" <?php if($m['support_ticket'] == "1") { echo 'checked'; }?>> Support Tickets
                		</label>
                	</div>
                </div>
                <div class="col">
                    <div class="checkbox">
                		<label>
                		  <input type="checkbox" name="live_chat" value="yes" <?php if($m['live_chat'] == "1") { echo 'checked'; }?>> Live Chat
                		</label>
                	</div>
                	<div class="checkbox">
                		<label>
                		  <input type="checkbox" name="google_analytics" value="yes" <?php if($m['google_analytics'] == "1") { echo 'checked'; }?>> Google Analytics
                		</label>
                	</div>
                	<div class="checkbox">
                		<label>
                		  <input type="checkbox" name="registration" value="yes" <?php if($m['registration'] == "1") { echo 'checked'; }?>> Users Registration
                		</label>
                	</div>
                	<div class="checkbox">
                		<label>
                		  <input type="checkbox" name="forget_password" value="yes" <?php if($m['forget_password'] == "1") { echo 'checked'; }?>> Forget Password
                		</label>
                	</div>
                	<div class="checkbox">
                		<label>
                		  <input type="checkbox" name="fixed_deposit" value="yes" <?php if($m['fixed_deposit'] == "1") { echo 'checked'; }?>> Fixed Deposit
                		</label>
                	</div>
                </div>
                <div class="col">
                	<div class="checkbox">
                		<label>
                		  <input type="checkbox" name="escrow" value="yes" <?php if($m['escrow'] == "1") { echo 'checked'; }?>> Escrow Payments
                		</label>
                	</div>
                </div>
            </div>
    </div>
        <button type="submit" class="btn btn-primary btn-block" style="border-radius:0px;" name="btn_save"><i class="fa fa-check"></i> Save changes</button>
        </form>
</div>