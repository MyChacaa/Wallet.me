<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
$id = protect($_GET['id']);
$query = $db->query("SELECT * FROM requests WHERE id='$id' and uid='$_SESSION[pw_uid]' and status='1'");
if($query->num_rows==0) { 
    $redirect = $settings['url']."index.php?a=account&b=summary";
    header("Location: $redirect");
}
$row = $query->fetch_assoc();
$update = $db->query("UPDATE requests SET status='3' WHERE id='$row[id]'");
?>
<div class="col-md-12">
    <div class="user-login-signup-form-wrap">
        <div class="modal-content">
            <div class="modal-body">
                <div class="user-connected-form-block">
                    <h3><?php echo filter_var($lang['head_requests'], FILTER_SANITIZE_STRING); ?></h2>
                    <hr/>
                    <?php
                    $amount = $row['amount'];
                    $currency = $row['currency'];
                    $description = $row['description'];
                    $recipient_id = $row['fromu'];
                    $email = idinfo($recipient_id,"email");
                    if(get_wallet_balance($_SESSION['pw_uid'],$currency) < $amount) {
                        echo error($lang['error_8']);
                    } else {
                        
                        $txid = strtoupper(randomHash(30));
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
                                
                            PW_UpdateUserWallet($_SESSION['pw_uid'],$amount_with_fee,$currency,2);
                            PW_UpdateUserWallet($recipient_id,$amount,$currency,1);
                            
                            $create_transaction = $db->query("INSERT transactions (txid,type,sender,recipient,description,amount,currency,fee,status,created) 
                            VALUES ('$txid','1','$_SESSION[pw_uid]','$recipient_id','$description','$amount_with_fee','$currency','$fee','1','$time')");
                            
                            $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                            VALUES ('$txid','2','$_SESSION[pw_uid]','$recipient_id','$amount_with_fee','$currency','1','$time')");
                            
                            $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                            VALUES ('$txid','1','$recipient_id','$_SESSION[pw_uid]','$amount','$currency','1','$time')");
                            $success_5 = str_ireplace("%amount%",$amount,$lang['success_5']);
							$success_5 = str_ireplace("%currency%",$currency,$success_5);
							$success_5 = str_ireplace("%email%",$email,$success_5);
							echo success($success_5);
                            }
                            
                        } elseif ($settings['payfee_type'] == "2") {
                            //receiver will pay fee
                            
                            $amount_with_fee = $amount - $fee;
                            PW_UpdateUserWallet($_SESSION['pw_uid'],$amount,$currency,2);
                            PW_UpdateUserWallet($recipient_id,$amount_with_fee,$currency,1);
                            
                            $create_transaction = $db->query("INSERT transactions (txid,type,sender,recipient,description,amount,currency,fee,status,created) 
                            VALUES ('$txid','1','$_SESSION[pw_uid]','$recipient_id','$description','$amount','$currency','$fee','1','$time')");
                            
                            $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                            VALUES ('$txid','2','$_SESSION[pw_uid]','$recipient_id','$amount','$currency','1','$time')");
                            
                            $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                            VALUES ('$txid','1','$recipient_id','$_SESSION[pw_uid]','$amount_with_fee','$currency','1','$time')");
                            $success_5 = str_ireplace("%amount%",$amount,$lang['success_5']);
							$success_5 = str_ireplace("%currency%",$currency,$success_5);
							$success_5 = str_ireplace("%email%",$email,$success_5);
                            echo success($success_5);
                        } else {
                            echo error ("Error! Occur from operative. contact support.");
                        }
                        
                        
                        PW_UpdateAdminWallet($fee,$currency);
                        $insert_admin_log = $db->query("INSERT admin_logs (type,time,u_field_1,u_field_2,u_field_3) VALUES ('2','$time','$fee','$currency','$txid')");
                        
                        
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
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>