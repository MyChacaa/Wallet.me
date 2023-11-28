<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
if ($m["currency_convert"] !== "1") {
    $redirect = $settings['url']."index.php?a=account&b=summary";
    header("Location: $redirect");
}
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md">
            <div class="h-100 p-3">
                <div class="overflow-hidden position-relative border-radius-lg bg-cover h-100" style="background-image: url('<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>assets/wallet/img/ivancik.jpg');">
                    <span class="mask bg-gradient-dark"></span>
                        <div class="card-body position-relative z-index-1 d-flex flex-column h-100 p-3">
                            <h5 class="text-white font-weight-bolder mb-4 pt-2"><?= $lang['menu_currencyconverter'] ?></h5>
                            <p class="text-white">Convert multiple currencies Instantly.</p>
                            <?php
							if(isset($_POST['pw_convert'])) {
                            $FormBTN = protect($_POST['pw_convert']);
                            if($FormBTN == "convert") {
                               $wallet_id = protect($_POST['wallet_id']);
                               $from_currency = $wallet_id;
                               $to_currency = protect($_POST['to_currency']);
                               $amount = protect($_POST['amount']);
                               $CheckWallet = $db->query("SELECT * FROM users_wallets WHERE currency='$wallet_id' and uid='$_SESSION[pw_uid]'");
                                if($CheckWallet->num_rows>0) {
                                    $wb = $CheckWallet->fetch_assoc();
                                }
                                if(empty($wallet_id) or empty($to_currency) or empty($amount)) {
                                    echo error($lang['error_20']);
                                } elseif(!is_numeric($amount)) {
                                    echo error($lang['error_7']);
                                } elseif($amount>$wb['amount']) {
                                    echo error("$lang[error_53] $from_currency.");
                                }else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $amount)) {
                                    echo error("Invalid Amount");   
                                } else {
                                        
                                        $rates = PW_currencyConvertor($amount,$from_currency,$to_currency);
                                        $receive = $rates;
                                        $rate_from = 1;
                                        $rate_to = PW_currencyConvertor(1,$from_currency,$to_currency);
                                        
                                        $admin_fee = ($receive*$settings['curcnv_fee_percentage'])/100;
                                        $fee = number_format($admin_fee, 2, '.', '');
                                        
                                        $receive = $receive - $fee;
                                        
                                        PW_UpdateUserWallet($_SESSION['pw_uid'],$amount,$from_currency,2);
                                        PW_UpdateUserWallet($_SESSION['pw_uid'],$receive,$to_currency,1);
                                        $txid = strtoupper(randomHash(10));
                                        $time = time();
                                        $reference_number = $currency.strtoupper(randomHash(10)); 
                                        
                                        PW_UpdateAdminWallet($fee,$to_currency);
                                        $insert_admin_log = $db->query("INSERT admin_logs (type,time,u_field_1,u_field_2,u_field_3) VALUES ('3','$time','$fee','$to_currency','$txid')");
                                        
                                        $insert_convert = $db->query("INSERT users_converts (uid,txid,from_wallet,to_wallet,from_amount,from_currency,to_amount,to_currency,from_rate,to_rate,fee,created,updated) 
                                        VALUES ('$_SESSION[pw_uid]','$txid','$wb[id]','0','$amount','$from_currency','$receive','$to_currency','$rate_from','$rate_to','$fee','$time','0')");
                                        
                                        $QueryConvert = $db->query("SELECT * FROM users_converts WHERE uid='$_SESSION[pw_uid]' ORDER BY id DESC LIMIT 1");
                                        $cnv = $QueryConvert->fetch_assoc();
                                        $create_transaction = $db->query("INSERT transactions (txid,type,sender,recipient,description,deposit_via,amount,currency,fee,status,created) VALUES ('$txid','8','$_SESSION[pw_uid]','$cnv[id]','','','$amount','$from_currency','','1','$time')");
                                        $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) VALUES ('$txid','8','$_SESSION[pw_uid]','$cnv[id]','$amount','$from_currency','1','$time')");
                                        $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) VALUES ('$txid','9','$_SESSION[pw_uid]','$cnv[id]','$receive','$to_currency','1','$time')");
                                        $success_25 = str_ireplace("%from_amount%",$amount." ".$from_currency,$lang['success_25']);
                                        $success_25 = str_ireplace("%to_amount%",$receive." ".$to_currency,$success_25);
                                        echo success($success_25);
                                    
                                    
                                }
                            }
							}
                            ?>
                    
                    
                    <form class="user-connected-from user-login-form" action="" method="POST">
                    <div class="row form-group">
                            <div class="col">
                                <label class="text-white"><?php echo filter_var($lang['field_32'], FILTER_SANITIZE_STRING); ?></label>
                                <select class="form-control" name="wallet_id" id="from_currency">
                                    <?php
                                    $GetUserWallets = $db->query("SELECT * FROM users_wallets WHERE uid='$_SESSION[pw_uid]'");
                                    if($GetUserWallets->num_rows>0) {
                                        while($getu = $GetUserWallets->fetch_assoc()) {
                                            echo '<option value="'.$getu['currency'].'">'.get_wallet_balance($_SESSION['pw_uid'],$getu['currency']).' '.$getu['currency'].'</option>';
                                        }
                                    } 
                                    ?>
                                </select>
                            </div>
                            <div class="col">
                                <label class="text-white"><?php echo filter_var($lang['field_33'], FILTER_SANITIZE_STRING); ?></label>
                                <select class="form-control" name="to_currency" id="to_currency">
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
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="text-white"><?php echo filter_var($lang['field_6'], FILTER_SANITIZE_STRING); ?></label>
                            <input type="text" class="form-control" name="amount" id="cnv_amount">
                        </div>

                        <button type="submit" name="pw_convert" value="convert" class="btn btn-primary"><?php echo filter_var($lang['btn_31'], FILTER_SANITIZE_STRING); ?></button>
                    </form>
                    <small style="color:white;">*Fee will be charge by <?php echo $settings['curcnv_fee_percentage']; ?>% on receiving amount.</small>
                </div>
            </div>
          </div>
        </div>
        <div class="col-md">
          <div class="card">
            <div class="card-header pb-0">
              <h6>Currency Calculator</h6>
            </div>
            <div class="card-body p-3">
              
                
                <form class="frConverter user-connected-from user-login-form">
                    <div class="row form-group">
                    	<span>I Have</span>
                    	<div class="col"><select name="base_currency" class="form-control"></select></div>
                    	<input type="hidden" class="form-control" name="default_base" value="<?php echo $settings['default_currency']; ?>"/>
                    	<div class="col"><input type="text" class="form-control" name="base_value" size="5" value="1"/></div>
                    </div>
                    <div class="row form-group">
                    	<span>I Want</span>
                    	<div class="col"><select class="form-control" name="target_currency" onChange="frExCalc();"></select></div>
                    	<input type="hidden"  class="form-control" name="default_target" value="eur"/>
                    	<div class="col"><input type="text" class="form-control" name="target_value" size="5" value=""/></div>
                    </div>
                    <br/><a class="frLink" target="_blank" href="//www.floatrates.com/"><small>Exchange Rates</small></a>
                </form>
            </div>
          </div>
        </div>
      </div>

</div>

<input type="hidden" id="url" value="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>">





<script type="text/javascript">
  (function() {
    var js = document.createElement('script'); js.type = 'text/javascript'; js.async = true;
    js.src = '//www.floatrates.com/scripts/converter.js';
    var sjs = document.getElementsByTagName('script')[0]; sjs.parentNode.insertBefore(js, sjs);
  })();
</script>
