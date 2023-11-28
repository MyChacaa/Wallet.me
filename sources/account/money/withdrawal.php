<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
error_reporting(0);
if ($m["withdrawal"] !== "1") {
    $redirect = $settings['url']."account/summary";
    header("Location: $redirect");
}
$ga 		= new GoogleAuthenticator();
$qrCodeUrl 	= $ga->getQRCodeGoogleUrl(idinfo($_SESSION['pw_uid'],"email"), $_SESSION['pw_secret'], $settings['name']);
?>
<div class="container-fluid py-4">
<div class="row">
    <div class="col-md">
      <div class="h-100 p-3">
        <div class="overflow-hidden position-relative border-radius-lg bg-cover h-100" style="background-image: url('<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>assets/miltipay/img/ivancik.jpg');">
          <span class="mask bg-gradient-dark"></span>
          <div class="card-body position-relative z-index-1 d-flex flex-column h-100 p-3">
            <h5 class="text-white font-weight-bolder mb-4 pt-2">Withdraw Money</h5>
            <p class="text-white">Withdraw your fund from your selective gateway.</p>

                <?php
                $hide_form=0;
				if(isset($_POST['pw_withdrawal'])) {
                $FormBTN = protect($_POST['pw_withdrawal']);
                if($FormBTN == "withdrawal") {
                    $withdrawal_to = protect($_POST['withdrawal_to']);
                    $wallet = protect($_POST['wallet_id']);
                    $amount = protect($_POST['amount']);
                    $wallet_passphrase = protect($_POST['wallet_passphrase']);
                    $code = protect($_POST['code']);
                    
                    $query_gateway = $db->query("SELECT * FROM gateways WHERE type='2' and id='$withdrawal_to'");
                    $row_gateway = $query_gateway->fetch_assoc();
                    $max = $row_gateway['max_amount'];
                    $min = $row_gateway['min_amount'];
                    
                    $checkResult = $ga->verifyCode($_SESSION['pw_secret'], $code, 2);    // 2 = 2*30sec clock tolerance
                    $CheckWallet = $db->query("SELECT * FROM users_wallets WHERE id='$wallet' and uid='$_SESSION[pw_uid]'");
                    if($CheckWallet->num_rows>0) {
                        $wb = $CheckWallet->fetch_assoc();
                    }
                    
                    $amount = number_format($amount, 2, '.', '');
                    $fee = gatewayinfo($withdrawal_to,"fee");
                    $include_fee = gatewayinfo($withdrawal_to,"include_fee");
                    $extra_fee = gatewayinfo($withdrawal_to,"extra_fee");
                    if($wb['currency'] !== $settings['default_currency']) {
                        $fee = currencyConvertor($fee,$settings['default_currency'],$wb['currency']);
                        $amount_with_fee = $amount - $fee;
                    } else {
                        $amount_with_fee = $amount - $fee;
                    }
                    if($include_fee == "1") {
                        $calculate = $amount * $extra_fee;
                        $calculate = $calculate / 100;
                        $amount_with_fee = $amount_with_fee - $calculate;
                    }
                    $amount_with_fee = number_format($amount_with_fee, 2, '.', '');
                    $cfee = $amount - $amount_with_fee;
                    
                    if(empty($withdrawal_to)) {
                        echo error($lang['error_14']);
                    } elseif(empty($wallet)) {
                        echo error($lang['error_15']);
                    } elseif(!is_numeric($amount)) {
                        echo error($lang['error_7']);
                    } elseif($amount<0) {
                        echo error($lang['error_7']);
                    } elseif($CheckWallet->num_rows==0) {
                        echo error($lang['error_16']);
                    } elseif($wb['currency'] !== $row_gateway['currency']) {
                        echo error("$row_gateway[name] Supports $row_gateway[currency] only.");
                    } elseif($amount > $wb['amount']) {
                        echo error($lang['error_8']);
                    } elseif($amount > $max) {
                        echo error("Maximum Withdraw via $row_gateway[name] is $row_gateway[currency] $max.");
                    } elseif($amount < $min) {
                        echo error("Minimum Withdraw via $row_gateway[name] is $row_gateway[currency] $min.");
                    }  elseif(idinfo($_SESSION['pw_uid'],"wallet_passphrase") && empty($wallet_passphrase)) {
                        echo error($lang['error_12']);
                    } elseif(idinfo($_SESSION['pw_uid'],"wallet_passphrase") && !password_verify($wallet_passphrase,idinfo($_SESSION['pw_uid'],"wallet_passphrase"))) {
                        echo error($lang['error_13']);
                    } elseif(idinfo($_SESSION['pw_uid'],"2fa_auth") == "1" && idinfo($_SESSION['pw_uid'],"2fa_auth_login") == "1" && !$checkResult) {
                        echo error($lang['error_51']);
                    } else {
                        $error=0;
                        foreach($_POST['fieldvalues'] as $k=>$v) {
                            if(empty($v)) {
                                $error=1;
                                $fname = PW_GetFieldName($k);
                                $msg = error('Field: "'.$fname.'" is empty.');
                            }
                        }

                        if($error==1) {
                            echo filter_var($msg, FILTER_SANITIZE_STRING);
                        } else {
                            $time = time();
                            $txid = strtoupper(randomHash(10));
                            
                            $create_withdrawal = $db->query("INSERT withdrawals (uid,txid,method,amount,currency,fee,requested_on,processed_on,status) VALUES ('$_SESSION[pw_uid]','$txid','$withdrawal_to','$amount','$wb[currency]','$cfee','$time','0','1')");
                            $WithdrawalQuery = $db->query("SELECT * FROM withdrawals WHERE uid='$_SESSION[pw_uid]' ORDER BY id DESC LIMIT 1");
                            $w = $WithdrawalQuery->fetch_assoc();
                            foreach($_POST['fieldvalues'] as $k=>$v) {
                                if(!empty($v)) {
                                    $insert = $db->query("INSERT withdrawals_values (uid,withdrawal_id,gateway_id,field_id,value) VALUES ('$_SESSION[pw_uid]','$w[id]','$withdrawal_to','$k','$v')");
                                }
                            }
                            PW_UpdateUserWallet($_SESSION['pw_uid'],$amount,$wb['currency'],2);
                            $create_transaction = $db->query("INSERT transactions (txid,type,sender,withdrawal_via,description,amount,currency,fee,status,created,item_id) VALUES ('$txid','4','$_SESSION[pw_uid]','$withdrawal_to','$description','$amount','$wb[currency]','$cfee','3','$time','$w[id]')");
                            $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,withdrawal_via,created) VALUES ('$txid','4','$_SESSION[pw_uid]','$w[id]','$amount','$wb[currency]','3','$withdrawal_to','$time')");
                            echo success($lang['success_8']);
                        }
                    }
                }
				}
                if($hide_form==0) {
                ?>
                <form class="user-connected-from user-login-form" action="" method="POST">
                    <div class="row form-group">
                        <div class="col">
                            <label style="color:white;"><?php echo filter_var($lang['field_4'], FILTER_SANITIZE_STRING); ?></label>
                            <select class="form-control" name="withdrawal_to" onchange="PW_Load_Gateway_Fields(this.value);" required>
                                <option value="">Select Payment Gateway</option>
                                <?php
                                $GetGateways = $db->query("SELECT * FROM gateways WHERE type='2' and status='1' ORDER BY id");
                                if($GetGateways->num_rows>0) {
                                    while($get = $GetGateways->fetch_assoc()) {
                                        echo '<option value="'.$get['id'].'">'.$get['name'].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col">
                            <label style="color:white;"><?php echo filter_var($lang['field_5'], FILTER_SANITIZE_STRING); ?></label>
                            <select class="form-control" name="wallet_id" onchange="PW_GetWalletCurrency(this.value);" required>
                                <?php
                                $GetUserWallets = $db->query("SELECT * FROM users_wallets WHERE uid='$_SESSION[pw_uid]'");
                                if($GetUserWallets->num_rows>0) {
                                    while($getu = $GetUserWallets->fetch_assoc()) {
                                        echo '<option value="'.$getu['id'].'">'.get_wallet_balance($_SESSION['pw_uid'],$getu['currency']).' '.$getu['currency'].'</option>';
                                    }
                                } 
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="color:white;"><?php echo filter_var($lang['field_6'], FILTER_SANITIZE_STRING); ?></label>
                        <input type="text" class="form-control" name="amount" id="send_amount" onkeyup="PW_Calculate(this.value);" onkeydown="PW_Calculate(this.value);">
                    </div>
                    <input style="color:white;" type="hidden" id="c_currency">
                    <input type="hidden" id="d_currency" value="<?php echo filter_var($settings['default_currency'], FILTER_SANITIZE_STRING); ?>">
                    <span style="color:white;" id="pw_gateway_fields"></span>
                    <?php if(idinfo($_SESSION['pw_uid'],"wallet_passphrase")) { ?>
                    <div class="form-group">
                        <label style="color:white;"><?php echo filter_var($lang['field_7'], FILTER_SANITIZE_STRING); ?></label>
                        <input type="password" class="form-control" name="wallet_passphrase">
                    </div>
                    <?php } ?>
                    
                    <?php if(idinfo($_SESSION['pw_uid'],"2fa_auth") == "1" && idinfo($_SESSION['pw_uid'],"2fa_auth_send") == "1") { ?>
                        <div class="form-group">
                        <label style="color:white;"><?php echo filter_var($lang['placeholder_12'], FILTER_SANITIZE_STRING); ?></label>
                        <input type="text" class="form-control" name="code" placeholder="">
                    </div>
                    <?php } ?>
                    <button type="submit" name="pw_withdrawal" value="withdrawal" class="btn btn-info"><?php echo filter_var($lang['btn_13'], FILTER_SANITIZE_STRING); ?></button>
                </form>
                <?php } ?>
                </div>
            </div>
          </div>
        </div>
        <div class="col-md">
          <div class="card">
            <div class="card-header pb-0">
              <h6>How to Withdraw money?</h6>
            </div>
            <div class="card-body p-3">
              <div class="timeline timeline-one-side">
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-money-coins text-success text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Amount</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Enter any amount you want to withdraw.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-diamond text-danger text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Currency</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Currently we only support USD currency. So you are able to withdraw only via USD.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-world text-info text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Gateway</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Select gateway, we offering multiple ways of withdraw fund. Select one which will be suitable.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-cart text-warning text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Withdraw</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Click on Withdraw, Your request will be receive by the operator, once approve you will receive the withdrawal.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-satisfied text-primary text-gradient"></i>
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
<input type="hidden" id="url" value="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>">