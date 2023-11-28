<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
error_reporting(0);
if ($m["send_money"] !== "1") {
    $redirect = $settings['url']."index.php?a=account&b=summary";
    header("Location: $redirect");
}
$ga 		= new GoogleAuthenticator();
$qrCodeUrl 	= $ga->getQRCodeGoogleUrl(idinfo($_SESSION['pw_uid'],"email"), $_SESSION['pw_secret'], $settings['name']);
?>
<div class="container-fluid py-4">
    <?php
	if(isset($_POST['send'])) {
    $FormBTN = protect($_POST['send']);
    if($FormBTN == "send") {
        $amount = protect($_POST['amount']);
        $amount = number_format($amount, 2, '.', '');
        $currency = protect($_POST['currency']);
        $email = protect($_POST['email']);
        $description = protect($_POST['description']);
        $wallet_passphrase = protect($_POST['wallet_passphrase']);
        $code = protect($_POST['code']);
        $checkResult = $ga->verifyCode($_SESSION['pw_secret'], $code, 2);    // 2 = 2*30sec clock tolerance
        if(empty($amount)) {
            echo error($lang['error_6']);
        } elseif(!is_numeric($amount)) {
            echo error($lang['error_7']);
        } elseif($amount<0) {
            echo error($lang['error_7']);
        } elseif($amount == "0") {
            echo error($lang['error_7']);
        }else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $amount)) {
             echo error("Invalid Amount");   
        } elseif(get_wallet_balance($_SESSION['pw_uid'],$currency) < $amount) {
            echo error($lang['error_8']);
        } elseif(idinfo($_SESSION['pw_uid'],"email") == $email) {
            echo error($lang['error_9']);
        } elseif(PW_CheckUser($email)==false) {
            echo error($lang['error_11']);
        } elseif(idinfo($_SESSION['pw_uid'],"wallet_passphrase") && empty($wallet_passphrase)) {
            echo error($lang['error_12']);
        } elseif(idinfo($_SESSION['pw_uid'],"wallet_passphrase") && !password_verify($wallet_passphrase,idinfo($_SESSION['pw_uid'],"wallet_passphrase"))) {
            echo error($lang['error_13']);
        } elseif(idinfo($_SESSION['pw_uid'],"2fa_auth") == "1" && idinfo($_SESSION['pw_uid'],"2fa_auth_login") == "1" && !$checkResult) {
            echo error($lang['error_51']);
        } else {
            
            
            $recipient_id = PW_GetUserID($email);
            $txid = strtoupper(randomHash(10));
            $time = time();
            $fee_per = ($amount * $settings['payfee_percentage']) / 100;
            if ($settings['default_currency'] !== "$currency") {
                
                $fee_fix = $settings['payfee_fixed'];
                $fee_fix = PW_currencyConvertor($fee_fix,$settings['default_currency'],$currency);
                
            } else {
                $fee_fix = $settings['payfee_fixed'];
            }
            
            $fee = $fee_per + $fee_fix;
            
            if ($settings['payfee_type'] == "1") {
                //sender will pay fee
                
                $amount_with_fee = $amount + $fee;
                
                if (get_wallet_balance($_SESSION['pw_uid'],$currency) < $amount_with_fee) {
                    echo error ("Insufficent Fund in account.");
                } else {
                    PW_UpdateUserWallet($_SESSION['pw_uid'],$amount_with_fee,$currency,2); //Sender will be credited by
                    PW_UpdateUserWallet($recipient_id,$amount,$currency,1); //Receiver will be debited by
                    
                    $create_transaction = $db->query("INSERT transactions (txid,type,sender,recipient,description,amount,currency,fee,status,created) 
                    VALUES ('$txid','1','$_SESSION[pw_uid]','$recipient_id','$description','$amount_with_fee','$currency','$fee','1','$time')");
                    
                    $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                    VALUES ('$txid','2','$_SESSION[pw_uid]','$recipient_id','$amount_with_fee','$currency','1','$time')");
                    
                    $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                    VALUES ('$txid','1','$recipient_id','$_SESSION[pw_uid]','$amount','$currency','1','$time')");
                    
                    $success_7 = str_ireplace("%amount%",$amount,$lang['success_7']);
                    $success_7 = str_ireplace("%currency%",$currency,$success_7);
                    $success_7 = str_ireplace("%email%",$email,$success_7);
                    
                    PW_UpdateAdminWallet($fee,$currency);
                    $insert_admin_log = $db->query("INSERT admin_logs (type,time,u_field_1,u_field_2,u_field_3) VALUES ('1','$time','$fee','$currency','$txid')");
                    PW_EmailSys_PaymentNotification($email,$amount,$currency,$description,$txid);
                    
                    // Referral Credit Code Here
                    if ($m["referral_system"] == "1") {
                    $myuser_infoQuery= $db->query("SELECT * FROM users WHERE id=".$_SESSION['pw_uid']); 
                    $myuser_info = $myuser_infoQuery->fetch_assoc();
                    if($myuser_info['ref1'] > 0){
                        $bonusQuery = $db->query("SELECT * FROM settings WHERE id=1");
                        $bonus_settings = $bonusQuery->fetch_assoc();
                        
                        $date = date('Y-m-d');
                        
                        
                        $upline_infoQuery= $db->query("SELECT * FROM users WHERE id=".$myuser_info['ref1']); 
                        $upline_info = $upline_infoQuery->fetch_assoc();
                        
                        $prize = $amount*($bonus_settings['ref_com']/100);
                        $prize = number_format($prize, 2, '.', '');
                        PW_UpdateUserWallet($myuser_info['ref1'],$prize,$currency,1);
                        $insert_bonus_logs = $db->query("INSERT bonus_logs (user_email,from_who,commission,currency,date) VALUES ('$upline_info[email]','$myuser_info[email]','$prize','$currency','$date')");
                    }
                    }
                    echo success($success_7);
                }
                
            } elseif ($settings['payfee_type'] == "2") {
                //receiver will pay fee
                
                $amount_with_fee = $amount - $fee;
                
                PW_UpdateUserWallet($_SESSION['pw_uid'],$amount,$currency,2); //Sender will be credited by
                PW_UpdateUserWallet($recipient_id,$amount_with_fee,$currency,1); //Receiver will be debited by
                
                $create_transaction = $db->query("INSERT transactions (txid,type,sender,recipient,description,amount,currency,fee,status,created) 
                VALUES ('$txid','1','$_SESSION[pw_uid]','$recipient_id','$description','$amount','$currency','$fee','1','$time')");
            
                $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                VALUES ('$txid','2','$_SESSION[pw_uid]','$recipient_id','$amount','$currency','1','$time')");
                
                $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                VALUES ('$txid','1','$recipient_id','$_SESSION[pw_uid]','$amount_with_fee','$currency','1','$time')");
                
                $success_7 = str_ireplace("%amount%",$amount,$lang['success_7']);
                $success_7 = str_ireplace("%currency%",$currency,$success_7);
                $success_7 = str_ireplace("%email%",$email,$success_7);
                
                PW_UpdateAdminWallet($fee,$currency);
                $insert_admin_log = $db->query("INSERT admin_logs (type,time,u_field_1,u_field_2,u_field_3) VALUES ('1','$time','$fee','$currency','$txid')");
                PW_EmailSys_PaymentNotification($email,$amount,$currency,$description,$txid);
                
                // Referral Credit Code Here
                if ($m["referral_system"] == "1") {
                $myuser_infoQuery= $db->query("SELECT * FROM users WHERE id=".$_SESSION['pw_uid']); 
                $myuser_info = $myuser_infoQuery->fetch_assoc();
                if($myuser_info['ref1'] > 0){
                    $bonusQuery = $db->query("SELECT * FROM settings WHERE id=1");
                    $bonus_settings = $bonusQuery->fetch_assoc();
                    
                    $date = date('Y-m-d');
                    
                    
                    $upline_infoQuery= $db->query("SELECT * FROM users WHERE id=".$myuser_info['ref1']); 
                    $upline_info = $upline_infoQuery->fetch_assoc();
                    
                    $prize = $amount*($bonus_settings['ref_com']/100);
                    $prize = number_format($prize, 2, '.', '');
                    PW_UpdateUserWallet($myuser_info['ref1'],$prize,$currency,1);
                    $insert_bonus_logs = $db->query("INSERT bonus_logs (user_email,from_who,commission,currency,date) VALUES ('$upline_info[email]','$myuser_info[email]','$prize','$currency','$date')");
                }
                }
                echo success($success_7);
            
            } else {
                echo error ("Error! Occur from operative. contact support.");
            }
            
            
        }
    }
	}
    ?>
                            

<div class="row">
    <div class="col-md">
      <div class="h-100 p-3">
        <div class="overflow-hidden position-relative border-radius-lg bg-cover h-100" style="background-image: url('<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>assets/miltipay/img/ivancik.jpg');">
          <span class="mask bg-gradient-dark"></span>
          <div class="card-body position-relative z-index-1 d-flex flex-column h-100 p-3">
            <h5 class="text-white font-weight-bolder mb-4 pt-2">Send Money</h5>
            <p class="text-white">No cost, and Limitless Sending anywhere anyone, Send Money to your Family members, Friends and colleague.</p>
                <form class="user-connected-from user-login-form" action="" method="POST">
                    <div class="input-group input-pw-amount">
                        <input type="text" class="form-control" name="amount" placeholder="0.00" aria-label="Amount (to the nearest dollar)">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <select class="form-control" name="currency">
                                    <?php
                    				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='1'");
                		            while($curr = $curr_Query->fetch_assoc()) {
                    						
                                        echo '<option value="'.$curr['currency'].'">'.$curr['currency'].'</option>';
                                    }
                                    ?>
                                    <?php
                    				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='2'");
                		            while($curr = $curr_Query->fetch_assoc()) {
                    						
                                        echo '<option value="'.$curr['currency'].'">'.$curr['currency'].'</option>';
                                    }
                                    ?>
                                </select>
                            </span>
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <input type="email" class="form-control" id="exampleInputEmail1" name="email" placeholder="<?php echo filter_var($lang['placeholder_5'], FILTER_SANITIZE_STRING); ?>">
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="description" rows="3" placeholder="<?php echo filter_var($lang['placeholder_4'], FILTER_SANITIZE_STRING); ?>"></textarea>
                    </div>
                    <?php if(idinfo($_SESSION['pw_uid'],"wallet_passphrase")) { ?>
                    <div class="form-group">
                        <input type="password" class="form-control" name="wallet_passphrase" placeholder="<?php echo filter_var($lang['placeholder_6'], FILTER_SANITIZE_STRING); ?>">
                    </div>
                    <?php } ?>
                    <?php if(idinfo($_SESSION['pw_uid'],"2fa_auth") == "1" && idinfo($_SESSION['pw_uid'],"2fa_auth_send") == "1") { ?>
                        <div class="form-group">
                        <input type="text" class="form-control" name="code" placeholder="<?php echo filter_var($lang['placeholder_12'], FILTER_SANITIZE_STRING); ?>">
                    </div>
                    <?php } ?>
                    <button type="submit" name="send" value="send" class="btn btn-primary"><?php echo filter_var($lang['btn_12'], FILTER_SANITIZE_STRING); ?> Money</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md">
          <div class="card">
            <div class="card-header pb-0">
              <h6>How to send money?</h6>
            </div>
            <div class="card-body p-3">
              <div class="timeline timeline-one-side">
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-bell-55 text-success text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Deposit Fund</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">any amount you can deposit in your wallet for your use.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-circle-08 text-danger text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Email</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Enter email address for the person your are sending an amount to.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-world text-info text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Amount</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Enter amount you want to send to your friend or customer or anyone.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-align-center text-warning text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Description</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Write any description to the payment, If you want you can write, If you not want then you can't.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-send text-info text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Send</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">By clicking send button the amount will be instantly transfer to the revceiver account, and cannot be refunded.</p>
                  </div>
                </div>
                
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-key-25 text-primary text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Share & Like our Profiles</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Like and Share our website on Facebook and any other social media site.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

</div>