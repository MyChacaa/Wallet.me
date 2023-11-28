<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
if ($m["request_money"] !== "1") {
    $redirect = $settings['url']."index.php?a=account&b=summary";
    header("Location: $redirect");
}
?>
<div class="container-fluid py-4">
    <?php
	if(isset($_POST['request'])) {
    $FormBTN = protect($_POST['request']);
    if($FormBTN == "request") {
        $amount = protect($_POST['amount']);
        $currency = protect($_POST['currency']);
        $email = protect($_POST['email']);
        $description = protect($_POST['description']);
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
        } elseif(idinfo($_SESSION['pw_uid'],"email") == $email) {
            echo error($lang['error_9']);
        } elseif(PW_CheckUser($email)==false) {
            echo error($lang['error_10']);
        } else {
            $amount = number_format($amount, 2, '.', '');
            $uid = PW_GetUserID($email);
            $time = time();
            $insert = $db->query("INSERT requests (uid,fromu,amount,currency,description,requested_on,status) VALUES ('$uid','$_SESSION[pw_uid]','$amount','$currency','$description','$time','1')");
            if(idinfo($_SESSION['pw_uid'],"account_type") == "1") { $from = idinfo($_SESSION['pw_uid'],"first_name")." ".idinfo($_SESSION['pw_uid'],"last_name"); } else { $from = idinfo($_SESSION['pw_uid'],"business_name"); }
            PW_EmailSys_PaymentRequestNotification($email,$amount,$currency,$description,$from);
            $success_6 = str_ireplace("%amount%",$amount,$lang['success_6']);
            $success_6 = str_ireplace("%currency%",$currency,$success_6);
            $success_6 = str_ireplace("%email%",$email,$success_6);
            echo success($success_6);
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
            <h5 class="text-white font-weight-bolder mb-4 pt-2">Request Money</h5>
            <p class="text-white">Request fund from anyone, anywhere & anytime free of cost with high limit.</p>
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
                        <input type="email" class="form-control" id="exampleInputEmail1" name="email" placeholder="<?php echo filter_var($lang['placeholder_3'], FILTER_SANITIZE_STRING); ?>">
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="description" rows="3" placeholder="<?php echo filter_var($lang['placeholder_4'], FILTER_SANITIZE_STRING); ?>"></textarea>
                    </div>
                    <button type="submit" name="request" value="request" class="btn btn-primary"><?php echo filter_var($lang['btn_11'], FILTER_SANITIZE_STRING); ?></button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md">
          <div class="card">
            <div class="card-header pb-0">
              <h6>How to Request money?</h6>
            </div>
            <div class="card-body p-3">
              <div class="timeline timeline-one-side">
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-circle-08 text-danger text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Email</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Enter email address for the person your are requesting an amount to.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-world text-info text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Amount</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Enter amount you want to request to your friend or customer or anyone.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-align-center text-warning text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Description</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Write any description to the request, If you want you can write, If you not want then you can't.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-send text-info text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Send Request</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">By clicking send request button the request will be sended to the sender of the amount, If sender will approve you will receive the fund in your account.</p>
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