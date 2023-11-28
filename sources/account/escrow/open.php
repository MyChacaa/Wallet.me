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
?>
<div class="container-fluid py-4">
    <?php
	if(isset($_POST['escrow'])) {
    $FormBTN = protect($_POST['escrow']);
    if($FormBTN == "escrow") {
        $amount = protect($_POST['amount']);
        $amount = number_format($amount, 2, '.', '');
        $currency = protect($_POST['currency']);
        $email = protect($_POST['email']);
        $description = protect($_POST['description']);
        
        if(empty($amount)) {
            echo error($lang['error_6']);
        } elseif(empty($description)) {
            echo error("Brief Description was required.");
        } elseif(empty($currency)) {
            echo error("Currency was required.");
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
            $escrow_settingsQuery = $db->query("SELECT * FROM escrow_settings ORDER BY id DESC LIMIT 1");
	        $escrow_settings = $escrow_settingsQuery->fetch_assoc();
            $fee_per = ($amount * $escrow_settings['payfee_percentage']) / 100;
            if ($escrow_settings['default_currency'] !== "$currency") {
                
                $fee_fix = $escrow_settings['payfee_fixed'];
                $fee_fix = PW_currencyConvertor($fee_fix,$escrow_settings['default_currency'],$currency);
                
            } else {
                $fee_fix = $escrow_settings['payfee_fixed'];
            }
            
            $fee = $fee_per + $fee_fix;
            
            if ($escrow_settings['payfee_type'] == "1") {
                //sender will pay fee
                
                $amount_with_fee = $amount + $fee;
                
                if (get_wallet_balance($_SESSION['pw_uid'],$currency) < $amount_with_fee) {
                    echo error ("Insufficent Fund in account.");
                } else {
                    
                    $date = date("Y-m-d");
                    PW_UpdateUserWallet($_SESSION['pw_uid'],$amount_with_fee,$currency,2); //Sender will be deducted by
                    //PW_UpdateUserWallet($recipient_id,$amount,$currency,1); //Receiver will be debited by
                    
                    //For Receiver - This was record on Escrow open table for temprory
                    //Status 9 = RESPONDED DISPUTE BY Buyer
                    //Status 8 = DISPUTE BY Buyer
                    //Status 7 = RESPOND OF DISPUTE BY SELLER
                    //Status 6 = DISPUTE BY SELLER
                    //Status 5 = DELIVERED BY SELLER
                    //Status 4 = HOLD/OPEN
                    //Status 3 = CLOSE BY BUYER
                    //Status 2 = CLOSE CONFIRM BY SELLER
                    //Status 1 = CONFIRM DELIVERY BY BUYER
                    $insert_escrow = $db->query("INSERT escrow_open (txid,uid,sender_uid,amount,currency,created,date_created,status) 
                    VALUES ('$txid','$recipient_id','$_SESSION[pw_uid]','$amount','$currency','$time','$date','4')");
                    
                    //For All Escrow Logs
                    $create_escrow = $db->query("INSERT escrow (txid,sender,recipient,description,amount,currency,fee,status,created) 
                    VALUES ('$txid','$_SESSION[pw_uid]','$recipient_id','$description','$amount_with_fee','$currency','$fee','4','$time')");
                    
                    $create_transaction = $db->query("INSERT transactions (txid,type,sender,recipient,description,amount,currency,fee,status,created) 
                    VALUES ('$txid','61','$_SESSION[pw_uid]','$recipient_id','$description','$amount_with_fee','$currency','$fee','4','$time')");
                    
                    $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                    VALUES ('$txid','62','$_SESSION[pw_uid]','$recipient_id','$amount_with_fee','$currency','4','$time')");
                    
                    $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                    VALUES ('$txid','61','$recipient_id','$_SESSION[pw_uid]','$amount','$currency','4','$time')");
                    
                    //PW_UpdateAdminWallet($fee,$currency);
                    //$insert_admin_log = $db->query("INSERT admin_logs (type,time,u_field_1,u_field_2,u_field_3) VALUES ('1','$time','$fee','$currency','$txid')");
                    //PW_EmailSys_PaymentNotification($email,$amount,$currency,$description,$txid);
                    
                    
                    header("Location: $settings[url]account/transaction/$txid");
                    
                }
                
            } elseif ($escrow_settings['payfee_type'] == "2") {
                //receiver will pay fee
                
                $amount_with_fee = $amount - $fee;
                
                $date = date("Y-m-d");
                PW_UpdateUserWallet($_SESSION['pw_uid'],$amount,$currency,2); //Sender will be deducted by
                //PW_UpdateUserWallet($recipient_id,$amount_with_fee,$currency,1); //Receiver will be debited by
                
                //For Receiver - This was record on Escrow open table for temprory
                //Status 9 = RESPONDED DISPUTE BY Buyer
                //Status 8 = DISPUTE BY Buyer
                //Status 7 = RESPOND OF DISPUTE BY SELLER
                //Status 6 = DISPUTE BY SELLER
                //Status 5 = DELIVERED BY SELLER
                //Status 4 = HOLD/OPEN
                //Status 3 = CLOSE BY BUYER
                //Status 2 = CLOSE CONFIRM BY SELLER
                //Status 1 = CONFIRM DELIVERY BY BUYER
                
                $insert_escrow = $db->query("INSERT escrow_open (txid,uid,sender_uid,amount,currency,created,date_created,status) 
                VALUES ('$txid','$recipient_id','$_SESSION[pw_uid]','$amount_with_fee','$currency','$time','$date','4')");
                
                //For All Escrow Logs
                $create_escrow = $db->query("INSERT escrow (txid,sender,recipient,description,amount,currency,fee,status,created) 
                VALUES ('$txid','$_SESSION[pw_uid]','$recipient_id','$description','$amount','$currency','$fee','4','$time')");
                
                $create_transaction = $db->query("INSERT transactions (txid,type,sender,recipient,description,amount,currency,fee,status,created) 
                VALUES ('$txid','61','$_SESSION[pw_uid]','$recipient_id','$description','$amount','$currency','$fee','4','$time')");
            
                $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                VALUES ('$txid','62','$_SESSION[pw_uid]','$recipient_id','$amount','$currency','4','$time')");
                
                $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                VALUES ('$txid','61','$recipient_id','$_SESSION[pw_uid]','$amount_with_fee','$currency','4','$time')");
                
                //PW_UpdateAdminWallet($fee,$currency);
                //$insert_admin_log = $db->query("INSERT admin_logs (type,time,u_field_1,u_field_2,u_field_3) VALUES ('1','$time','$fee','$currency','$txid')");
                //PW_EmailSys_PaymentNotification($email,$amount,$currency,$description,$txid);
                
                
                header("Location: $settings[url]account/transaction/$txid");
            
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
        <div class="overflow-hidden position-relative border-radius-lg bg-cover h-100" style="background-image: url('<?=$settings['url']; ?>assets/wallet/img/home-decor-2.jpg');">
          <span class="mask bg-gradient-info"></span>
          <div class="card-body position-relative z-index-1 d-flex flex-column h-100 p-3">
            <h5 class="text-white font-weight-bolder mb-4 pt-2">Escrow Payments</h5>
            <p class="text-white">Hold funds in Escrow until a task is completed. Release If you are satisfied with the task.</p>
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
                        <textarea class="form-control" name="description" rows="3" placeholder="Description and Terms of Payment"></textarea>
                    </div>
                    <button type="submit" name="escrow" value="escrow" class="btn bg-gradient-warning btn-block">Open Escrow Payment</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md">
          <div class="card">
            <div class="card-header pb-0">
              <h6>How Escrow Works?</h6>
            </div>
            <div class="card-body p-3">
              <div class="timeline timeline-one-side">
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-bell-55 text-success text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Open Escrow</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Enter amount, select currency and enter terms of payments and open the escrow payment.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-circle-08 text-danger text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Seller Verification</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Once Seller were delivered your product digital or physical, seller will confirm the transaction.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-world text-info text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Buyer Verification</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">If buyer were confirmed that seller provide well good, and buyer were satisfied so buyer will confirm transaction.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-align-center text-warning text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Confirmed</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">If both side confirmed transaction, the amount will be release from escrow Immediately. After confirmation no disputes are allowed or to be taken.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-send text-info text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Disputes</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">If one Party was not confirm the transaction, The case will go for the <?=$settings['name']?> team review and final decision will taken by them.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

</div>