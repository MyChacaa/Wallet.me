<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

if(isset($_POST['vvv'])){
        $txid = protect($_POST['txid']);
        $picic = protect($_POST['picic']);
        
        $GetDeposit = $db->query("SELECT * FROM deposits WHERE id='$picic'");
        $get = $GetDeposit->fetch_assoc();
        $txidee = $get['txid'];
        $gateway = $get['method'];
        $amount = $get['amount'];
        $currency = $get['currency'];
        $time = time();
        $reference_number = $get['reference_number'];
        $description = 'Deposit '.$amount.' '.$currency.' to '.idinfo($_SESSION['pw_uid'],"email");
        
        $chk_deposit = $db->query("SELECT * FROM gateways WHERE id=$gateway");
        if($chk_deposit->num_rows < 0) {
            error("Gateways error");    
        }
        
        $update = $db->query("UPDATE deposits SET status='3',gateway_txid='$txid',processed_on='$time' WHERE id='$picic'");
		$update = $db->query("UPDATE activity SET status='3' WHERE u_field_1='$picic'");
		$update = $db->query("UPDATE transactions SET status='3' WHERE recipient='$picic'");

        echo "<script>window.location = '$settings[url]deposit/$picic/success';</script>";
	}
	if(isset($_POST['canceleded'])){
	    $picic = protect($_POST['picic']);
	    $trans = protect($_POST['txdee']);
	    
	    $update = $db->query("UPDATE activity SET status='2' WHERE u_field_1='$picic'");
		$update = $db->query("UPDATE transactions SET status='2' WHERE recipient='$picic'");
        $update = $db->query("UPDATE deposits SET status='2' WHERE id='$picic'");
        echo "<script>window.location = '$settings[url]deposit/$picic/fail';</script>";
	}
function getPaymentForm($id,$gateway_id) {
	global $db, $settings;
	$gateway = gatewayinfo($gateway_id,"name");
	$m_gateway = gatewayinfo($gateway_id,"external_gateway");
	if($gateway == "PayPal") { return PF_PayPal($id,$gateway_id); }
	elseif($gateway == "AdvCash") { return PF_AdvCash($id,$gateway_id); }
	elseif($gateway == "Payeer") { return PF_Payeer($id,$gateway_id); }
	elseif($gateway == "Perfect Money") { return PF_PerfectMoney($id,$gateway_id); }
	elseif($gateway == "Paytm") { return PF_Paytm($id,$gateway_id); }
	elseif($gateway == "Stripe") { return PF_Stripe($id,$gateway_id); }
	elseif($gateway == "Skrill") { return PF_Skrill($id,$gateway_id); }
	elseif($gateway == "Flutterwave") { return PF_Flutterwave($id,$gateway_id); }
	elseif($gateway == "2Checkout") { return PF_2Checkout($id,$gateway_id); }
	elseif($gateway == "Bank Transfer") { return PF_BankTransfer($id,$gateway_id); }
	elseif($m_gateway == "1") { return PF_m_1($id,$gateway_id); }
	else { return 'Something was wrong. Please contact with administrator.'; }
}

function PF_PayPal($id,$gateway) {
	global $db, $settings;
	require("payment_src/paypal_class.php");
	define('EMAIL_ADD', gatewayinfo($gateway,"a_field_1")); // For system notification.
	define('PAYPAL_EMAIL_ADD', gatewayinfo($gateway,"a_field_1"));
	
	// Setup class
	$p = new paypal_class( ); 				 // initiate an instance of the class.
	$p -> admin_mail = EMAIL_ADD; 
	$GetDeposit = $db->query("SELECT * FROM deposits WHERE id='$id'");
	$get = $GetDeposit->fetch_assoc();
	$amount = $get['amount'];
	$currency = $get['currency'];
	$url = $settings['url'];
	$uid = $get['uid'];
	$payment_note = 'Deposit #'.$id.' to '.idinfo($uid,"email").' wallet';
	$p->add_field('business', PAYPAL_EMAIL_ADD); //don't need add this item. if y set the $p -> paypal_mail.
	$p->add_field('return', $url.'deposit/'.$id.'/success');
	$p->add_field('cancel_return', $url.'deposit/'.$id.'/fail');
	$p->add_field('notify_url', $url.'callbacks/checkPayment.php?a=PayPal');
	$p->add_field('item_name', $payment_note);
	$p->add_field('item_number', $id);
	$p->add_field('amount', $amount);
	$p->add_field('currency_code', $currency);
	$p->add_field('cmd', '_xclick');
	$p->add_field('rm', '2');	// Return method = POST
						 
	$return = $p->submit_paypal_post(); // submit the fields to paypal
	$return .= '<script type="text/javascript" src="'.$settings[url].'assets/js/jquery-1.12.4.min.js"></script>';
	$return .= '<script type="text/javascript">$(document).ready(function() { $("#paypal_form")[0].submit();; });</script>';
	$return .= '<br><center><i class="fa fa-spin fa-spinner"></i><br/>Redirecting...</center><br>';
	return $return;
}

function PF_Stripe($id,$gateway) {
	global $db, $settings;
	$GetDeposit = $db->query("SELECT * FROM deposits WHERE id='$id'");
	$get = $GetDeposit->fetch_assoc();
    define('STRIPE_API_KEY', gatewayinfo($gateway,"a_field_2")); 
	define('STRIPE_PUBLISHABLE_KEY', gatewayinfo($gateway,"a_field_1")); 
	$payment_note = 'Reference #'.$get['reference_number'];
	$productName = $payment_note; 
	$productNumber = $id; 
	$productPrice = $get['amount'];
	$currency = strtolower($get['currency']);
	if($settings['favicon']) {
	    $logo = $settings['url'].$settings['favicon'];
		
	} else {
	    $logo = "$settings[url]assets/logo/favicon.png";
	}
		// Convert product price to cent
	$stripeAmount = round($productPrice*100, 2);
	$_SESSION['pw_stripe_productName'] = $productName;
	$_SESSION['pw_stripe_productNumber'] = $productNumber;
	$_SESSION['pw_stripe_productPrice'] = $productPrice;
	$_SESSION['pw_stripe_gateway'] = $gateway;
	$_SESSION['pw_stripe_currency'] = $currency;
	$_SESSION['pw_stripe_stripeAmount'] = $stripeAmount;
	$return .= '<script type="text/javascript" src="'.$settings['url'].'assets/js/jquery-1.12.4.min.js"></script>';
	$return .= '<script src="https://checkout.stripe.com/checkout.js"></script>';
	$return .= '<script>$(document).ready(function() { $("#payButton").click(); });</script>';
	$return .= '<div id="buynow">
	<center><button class="stripe-button btn btn-primary" id="payButton">Deposit via VISA/MasterCard</button></center>
	<input type="hidden" id="payProcess" value="0"/>
</div>';
	$return .= '<script>
	var handler = StripeCheckout.configure({
		key: "'.STRIPE_PUBLISHABLE_KEY.'",
		image: "'.$logo.'",
		locale: "auto",
		token: function(token) {
			// You can access the token ID with `token.id`.
			// Get the token ID to your server-side code for use.
			
			$("#paymentDetails").hide();
			$("#payProcess").val(1);
			$.ajax({
				url: "'.$settings['url'].'callbacks/checkPayment.php?a=Stripe",
				type: "POST",
				data: {stripeToken: token.id, stripeEmail: token.email},
				dataType: "json",
				beforeSend: function(){
					$("body").prepend("<div class=\'overlay\'></div>");
					$("#payButton").prop("disabled", true);
					$("#payButton").html("Please wait...");
				},
				success: function(data){
					$(".overlay").remove();
					$("#payProcess").val(0);
					if(data.status == 1){
						var paidAmount = (data.txnData.amount/100);
						$("#buynow").hide();
						$(location).attr("href", "'.$settings['url'].'deposit/'.$id.'/success");
					}else{
						$("#payButton").prop("disabled", false);
						$("#payButton").html("Deposit via VISA/MasterCard");
						alert("Some problem occurred, please try again.");
					}
				},
				error: function() {
					$("#payProcess").val(0);
					$("#payButton").prop("disabled", false);
					$("#payButton").html("Deposit via VISA/MasterCard");
					alert("Some problem occurred, please try again.");
				}
			});
		}
	});
	
	var stripe_closed = function(){
		var processing = $("#payProcess").val();
		if (processing == 0){
			$("#payButton").prop("disabled", false);
			$("#payButton").html("Deposit via VISA/MasterCard");
		}
	};
	
	var eventTggr = document.getElementById("payButton");
	if(eventTggr){
		eventTggr.addEventListener("click", function(e) {
			$("#payButton").prop("disabled", true);
			$("#payButton").html("Please wait...");
			
			// Open Checkout with further options:
			handler.open({
				name: "'.$settings['name'].'",
				description: "'.$productName.'",
				amount: "'.$stripeAmount.'",
				currency: "'.$currency.'",
				closed:	stripe_closed
			});
			e.preventDefault();
		});
	}
	
	// Close Checkout on page navigation:
	window.addEventListener("popstate", function() {
		handler.close();
	});
	</script>';
	return $return;
}

function PF_Paytm($id,$gateway) {
	global $db, $settings;
	include("payment_src/encdec_paytm.php");
	$GetDeposit = $db->query("SELECT * FROM deposits WHERE id='$id'");
	$get = $GetDeposit->fetch_assoc();
	$gateway = $get['method'];
	define('PAYTM_ENVIRONMENT', 'PROD'); // PROD
    define('PAYTM_MERCHANT_KEY',  gatewayinfo($gateway,"a_field_1")); //Change this constant's value with Merchant key received from Paytm.
    define('PAYTM_MERCHANT_MID', gatewayinfo($gateway,"a_field_2")); //Change this constant's value with MID (Merchant ID) received from Paytm.
    define('PAYTM_MERCHANT_WEBSITE',  gatewayinfo($gateway,"a_field_3")); //Change this constant's value with Website name received from Paytm.
    $PAYTM_STATUS_QUERY_NEW_URL='https://securegw-stage.paytm.in/order/status';
    $PAYTM_TXN_URL='https://securegw-stage.paytm.in/order/process';
    if (PAYTM_ENVIRONMENT == 'PROD') {
    	$PAYTM_STATUS_QUERY_NEW_URL='https://securegw.paytm.in/order/status';
    	$PAYTM_TXN_URL='https://securegw.paytm.in/order/process';
    }
    define('PAYTM_REFUND_URL', '');
    define('PAYTM_STATUS_QUERY_URL', $PAYTM_STATUS_QUERY_NEW_URL);
    define('PAYTM_STATUS_QUERY_NEW_URL', $PAYTM_STATUS_QUERY_NEW_URL);
    define('PAYTM_TXN_URL', $PAYTM_TXN_URL);
    $checkSum = "";
    $paramList = array();
    $ORDER_ID = $get['id'];
    $CUST_ID = $get['uid'];
    $INDUSTRY_TYPE_ID = 'Retail';
    $CHANNEL_ID = 'WEB';
    $TXN_AMOUNT = $get['amount'];
    
    // Create an array having all required parameters for creating checksum.
    $paramList["MID"] = PAYTM_MERCHANT_MID;
    $paramList["ORDER_ID"] = (int) $ORDER_ID;
    $paramList["CUST_ID"] = (int) $get['uid'];
    $paramList["INDUSTRY_TYPE_ID"] = $INDUSTRY_TYPE_ID;
    $paramList["CHANNEL_ID"] = $CHANNEL_ID;
    $paramList["TXN_AMOUNT"] = (int) $TXN_AMOUNT;
    $paramList["WEBSITE"] = PAYTM_MERCHANT_WEBSITE;
    $paramList["CALLBACK_URL"] = $settings['url']."callbacks/checkPayment.php?a=Paytm";
    $return = '<form method="post" action="'.PAYTM_TXN_URL.'" name="f1" id="paytm_form">';
    foreach($paramList as $name => $value) {
				$return .= '<input type="hidden" name="' . $name .'" value="' . $value . '">';
			}
 $checkSum = getChecksumFromArray($paramList,PAYTM_MERCHANT_KEY);
 $return .= '<input type="hidden" name="CHECKSUMHASH" value="'.$checkSum.'"></form>';
    //Here checksum string will return by getChecksumFromArray() function.

	$return .= '<script type="text/javascript" src="'.$settings[url].'assets/js/jquery-1.12.4.min.js"></script>';
	$return .= '<script type="text/javascript">$(document).ready(function() { $("#paytm_form")[0].submit();; });</script>';
	$return .= '<br><center><i class="fa fa-spin fa-spinner"></i><br/>Redirecting...</center><br>';
	return $return;
	
}

function PF_AdvCash($id,$gateway) {
	global $db, $settings;
	$merchant = gatewayinfo($gateway,"a_field_1");
	$secret = gatewayinfo($gateway,"a_field_2");
	$n = gatewayinfo($gateway,"a_field_3");
	$GetDeposit = $db->query("SELECT * FROM deposits WHERE id='$id'");
	$get = $GetDeposit->fetch_assoc();
	$amount = $get['amount'];
	$currency = $get['currency'];
	$url = $settings['url'];
	$uid = $get['uid'];
	$payment_note = 'Deposit #'.$id.' to '.idinfo($uid,"email").' wallet';
	$arHash = array(
			$merchant,
			$n,
			$amount,
			$currency,
			$secret,
			$id
		);
	$sign = strtoupper(hash('sha256', implode(':', $arHash)));
	$return = '<div style="display:none;">
					<form method="GET" id="advcash_form" action="https://wallet.advcash.com/sci/">
					<input type="hidden" name="ac_account_email" value="'.$merchant.'">
					<input type="hidden" name="ac_sci_name" value="'.$n.'">
					<input type="hidden" name="ac_amount" value="'.$amount.'">
					<input type="hidden" name="ac_currency" value="'.$currency.'">
					<input type="hidden" name="ac_order_id" value="'.$id.'">
					<input type="hidden" name="ac_sign" value="'.$sign.'">
					<input type="hidden" name="ac_success_url" value="'.$settings[url].'deposit/'.$id.'/success" />
					 <input type="hidden" name="ac_success_url_method" value="GET" />
					 <input type="hidden" name="ac_fail_url" value="'.$settings[url].'deposit/'.$id.'/fail" />
					 <input type="hidden" name="ac_fail_url_method" value="GET" />
					 <input type="hidden" name="ac_status_url" value="'.$settings[url].'callbacks/checkPayment.php?a=AdvCash" />
					 <input type="hidden" name="ac_status_url_method" value="GET" />
					<input type="hidden" name="ac_comments" value="'.$payment_note.'">
					</form>
					</div>';
	$return .= '<script type="text/javascript" src="'.$settings[url].'assets/js/jquery-1.12.4.min.js"></script>';
	$return .= '<script type="text/javascript">$(document).ready(function() { $("#advcash_form")[0].submit();; });</script>';
	$return .= '<br><center><i class="fa fa-spin fa-spinner"></i><br/>Redirecting...</center><br>';
	return $return;
}

function PF_2Checkout($id,$gateway) {
	global $db, $settings;
	$merchant = gatewayinfo($gateway,"a_field_1");
	$secret = gatewayinfo($gateway,"a_field_2");
	
	$GetDeposit = $db->query("SELECT * FROM deposits WHERE id='$id'");
	$get = $GetDeposit->fetch_assoc();
	$amount = $get['amount'];
	$currency = $get['currency'];
	$url = $settings['url'];
	$uid = $get['uid'];
	$payment_note = 'Deposit #'.$id.' to '.idinfo($uid,"email").' wallet';
	$seller_id = $merchant;
	$email = idinfo($uid,"email");
	$sign = base64_encode($payment_note);
	header("Location: https://www.2checkout.com/checkout/purchase?sid=$seller_id&mode=2CO&li_0_name=Deposit&li_0_price=$amount&currency_code=$currency&email=$email&tx=$sign&oid=$id");
	return $return;
}

function PF_Flutterwave($id,$gateway) {
	global $db, $settings;
	define('FLUTTER_PUBLISHABLE_KEY', gatewayinfo($gateway,"a_field_1")); 
	
	$GetDeposit = $db->query("SELECT * FROM deposits WHERE id='$id'");
	$get = $GetDeposit->fetch_assoc();
	
	$amount = $get['amount'];
	$currency = $get['currency'];
	$url = $settings['url'];
	$uid = $get['uid'];
	$payment_note = 'Deposit #'.$id.' to '.idinfo($uid,"email").' wallet';
	$m_curr = $currency;
	$m_amount = $amount;
	$desc = $payment_note;
	$reference_number = $get['reference_number'];
	if($settings['favicon']) {
	    $logo = $settings['url'].$settings['favicon'];
		
	} else {
	    $logo = "$settings[url]assets/logo/favicon.png";
	}
	$return .= '<form><script src="https://checkout.flutterwave.com/v3.js"></script>
	
	<div id="buynow">
	<center><button class="stripe-button btn btn-primary" id="payButton" type="button" onClick="makePayment()">Deposit via VISA/MasterCard</button></center>
	<br>
	
    </div>
	</form>';
    $return .= '
    <script>
  function makePayment() {
    FlutterwaveCheckout({
      public_key: "'.FLUTTER_PUBLISHABLE_KEY.'",
      tx_ref: "'.$reference_number.'",
      amount: "'.$amount.'",
      currency: "'.$m_curr.'",
      
      payment_options: "card",
      redirect_url: "'.$settings[url].'callbacks/checkPayment.php?a=Flutterwave",
     
      customer: {
        email: "'.idinfo($uid,"email").'",
        phone_number: "'.idinfo($uid,"phone").'",
        name: "'.idinfo($uid,"first_name").'",
      },
      callback: function (data) {
        console.log(data);
      },
      onclose: function() {
        // close modal
        location.href = "'.$settings[url].'account/money/deposit";
	
      },
      customizations: {
        title: "'.$settings[name].' ",
        description: "'.$desc.'",
        logo: "'.$logo.'",
      },
    });
  }
</script>';
    
	return $return;
}

function PF_Payeer($id,$gateway) {
	global $db, $settings;
	$merchant = gatewayinfo($gateway,"a_field_1");
	$secret = gatewayinfo($gateway,"a_field_2");
	$GetDeposit = $db->query("SELECT * FROM deposits WHERE id='$id'");
	$get = $GetDeposit->fetch_assoc();
	$amount = $get['amount'];
	$currency = $get['currency'];
	$url = $settings['url'];
	$uid = $get['uid'];
	$payment_note = 'Deposit #'.$id.' to '.idinfo($uid,"email").' wallet';
	$m_shop = $merchant;
	$m_orderid = $id;
	$m_amount = number_format($amount, 2, '.', '');
	$m_curr = $currency;
	$desc = $payment_note;
	$m_desc = base64_encode($desc);
	$m_key = $secret;
	
	$arHash = array(
        $m_shop,
        $m_orderid,
        $m_amount,
        $m_curr,
        $m_desc
        );
        
    $arParams = array(
        'success_url' => "$settings[url]callbacks/checkPayment.php?a=Payeer",
    	'fail_url' => "$settings[url]deposit/$id/fail",
    	'status_url' => "$settings[url]deposit/$id/success",
    // Forming an array for additional fields
        'reference' => array(
            'var1' => '1',
            'var2' => '2',
            'var3' => '3',
            'var4' => '4',
            'var5' => '5',
        ),
        //'submerchant' => 'mail.com',
    );
    
    $key = md5($m_key.$m_orderid);

    $m_params = @urlencode(base64_encode(openssl_encrypt(json_encode($arParams), 'AES-256-CBC', $key, OPENSSL_RAW_DATA)));

    $arHash[] = $m_params;

    $arHash[] = $m_key;
    
	$sign = strtoupper(hash('sha256', implode(':', $arHash)));
	
	$arGetParams = array(
	'm_shop' => $m_shop,
	'm_orderid' => $m_orderid,
	'm_amount' => $m_amount,
	'm_curr' => $m_curr,
	'm_desc' => $m_desc,
	'm_sign' => $sign,
	'm_params' => $params,
	'm_cipher_method' => 'AES-256-CBC',
	
    );
    
    $url = 'https://payeer.com/merchant/?'.http_build_query($arGetParams);
	$return = '<div style="display:none;"><form method="POST" id="payeer_form" action="https://payeer.com/merchant/">
		<input type="hidden" name="m_shop" value="'.$m_shop.'">
        <input type="hidden" name="m_orderid" value="'.$m_orderid.'">
        <input type="hidden" name="m_amount" value="'.$m_amount.'">
        <input type="hidden" name="m_curr" value="'.$m_curr.'">
        <input type="hidden" name="m_desc" value="'.$m_desc.'">
        <input type="hidden" name="m_sign" value="'.$sign.'">
		
		<input type="hidden" name="m_params" value="'.$m_params.'">
        <input type="hidden" name="m_cipher_method" value="AES-256-CBC">
		
		
		</form></div>';
	$return .= '<script type="text/javascript" src="'.$settings[url].'assets/js/jquery-1.12.4.min.js"></script>';
	$return .= '<script type="text/javascript">$(document).ready(function() { $("#payeer_form")[0].submit();; });</script>';
	$return .= '<br><center><i class="fa fa-spin fa-spinner"></i><br/>Redirecting...</center><br>';
	return $return;
}

function PF_PerfectMoney($id,$gateway) {
	global $db, $settings;
	$merchant = gatewayinfo($gateway,"a_field_1");
	$GetDeposit = $db->query("SELECT * FROM deposits WHERE id='$id'");
	$get = $GetDeposit->fetch_assoc();
	$amount = $get['amount'];
	$currency = $get['currency'];
	$url = $settings['url'];
	$uid = $get['uid'];
	$payment_note = 'Deposit #'.$id.' to '.idinfo($uid,"email").' wallet';
	$return = '<div style="display:none;">
				<form action="https://perfectmoney.is/api/step1.asp" id="pm_form" method="POST">
					<input type="hidden" name="PAYEE_ACCOUNT" value="'.$merchant.'">
					<input type="hidden" name="PAYEE_NAME" value="'.$settings[name].'">
					<input type="hidden" name="PAYMENT_ID" value="'.$id.'">
					<input type="text"   name="PAYMENT_AMOUNT" value="'.$amount.'"><BR>
					<input type="hidden" name="PAYMENT_UNITS" value="'.$currency.'">
					<input type="hidden" name="STATUS_URL" value="'.$settings[url].'callbacks/checkPayment.php?a=PerfectMoney">
					<input type="hidden" name="PAYMENT_URL" value="'.$settings[url].'deposit/'.$id.'/success">
					<input type="hidden" name="PAYMENT_URL_METHOD" value="POST">
					<input type="hidden" name="NOPAYMENT_URL" value="'.$settings[url].'deposit/'.$id.'/fail">
					<input type="hidden" name="NOPAYMENT_URL_METHOD" value="POST">
					<input type="hidden" name="SUGGESTED_MEMO" value="'.$payment_note.'">
					<input type="hidden" name="BAGGAGE_FIELDS" value="IDENT"><br>
					<input type="submit" name="PAYMENT_METHOD" value="Pay Now!" class="tabeladugme"><br><br>
					</form></div>';
	$return .= '<script type="text/javascript" src="'.$settings[url].'assets/js/jquery-1.12.4.min.js"></script>';
	$return .= '<script type="text/javascript">$(document).ready(function() { $("#pm_form")[0].submit();; });</script>';
	$return .= '<br><center><i class="fa fa-spin fa-spinner"></i><br/>Redirecting...</center><br>';
	return $return;	
}

function PF_Skrill($id,$gateway) {
	global $db, $settings;
	$merchant = gatewayinfo($gateway,"a_field_1");
	$GetDeposit = $db->query("SELECT * FROM deposits WHERE id='$id'");
	$get = $GetDeposit->fetch_assoc();
	$amount = $get['amount'];
	$currency = $get['currency'];
	$url = $settings['url'];
	$uid = $get['uid'];
	$payment_note = 'Deposit #'.$id.' to '.idinfo($uid,"email").' wallet';
	$return = '<div style="display:none;"><form action="https://www.moneybookers.com/app/payment.pl" method="post" id="skrill_form">
					  <input type="hidden" name="pay_to_email" value="'.$merchant.'"/>
					  <input type="hidden" name="status_url" value="'.$settings[url].'callbacks/checkPayment.php?a=Skrill"/> 
					  <input type="hidden" name="language" value="EN"/>
					  <input type="hidden" name="amount" value="'.$amount.'"/>
					  <input type="hidden" name="currency" value="'.$currency.'"/>
					  <input type="hidden" name="detail1_description" value="'.$payment_note.'"/>
					  <input type="hidden" name="detail1_text" value="'.$id.'"/>
					  <input type="submit" class="btn btn-primary" value="Click to pay."/>
					</form></div>';
	$return .= '<script type="text/javascript" src="'.$settings[url].'assets/js/jquery-1.12.4.min.js"></script>';
	$return .= '<script type="text/javascript">$(document).ready(function() { $("#skrill_form")[0].submit();; });</script>';
	$return .= '<br><center><i class="fa fa-spin fa-spinner"></i><br/>Redirecting...</center><br>';
	return $return;
}

function PF_BankTransfer($id,$gateway) {
	global $db, $settings;
	$GetDeposit = $db->query("SELECT * FROM deposits WHERE id='$id'");
	$get = $GetDeposit->fetch_assoc();
	$amount = $get['amount'];
	$currency = $get['currency'];
	$url = $settings['url'];
	$uid = $get['uid'];
	$payment_note = $get['reference_number'];	
	$return = '<div class="table-responsive">
				<table class="table table-striped" style="width:50%;">
					<tbody>';
	$QueryFields = $db->query("SELECT * FROM gateways_fields WHERE gateway_id='$gateway'");
	if($QueryFields->num_rows>0) {
		while($field = $QueryFields->fetch_assoc()) {
			$return .= '<tr>
					<td style="color:white;"><b>'.$field['field_name'].'</b></td>
					<td style="color:white;">'.$field['field_value'].'</td>
				</tr>';
		}
	}				
	$return .= '</tbody>
			</table>
			</div>';
			$process_type = gatewayinfo($gateway,"process_type");
			$process_time = gatewayinfo($gateway,"process_time");
			if($process_type == "1") {
				if($process_time == "1") {
					$prcoessed_time = '1 minute';
				} else {
					$processed_time = $process_time.' minutes'; 
				}
			} elseif($process_type == "2") {
				if($process_time == "1") {
					$processed_time = '1 hour';
				} else {
					$processed_time = $process_time.' hours';
				}
			} elseif($process_type == "3") {
				if($process_time == "1") {
					$processed_time = '1 working day';
				} else {
					$processed_time = $process_time.' working days';
				}   
			} else {
				$processed_time = '';
			}
	$return .= '<br><div class="table-responsive">
				<table class="table table-striped">
					<tbody>
						<tr>
							<td style="color:white;"><b>Amount</b></td>
							<td style="color:white;">'.$amount.' '.$currency.'</td>
						</tr>
						<tr>
							<td style="color:white;"><b>Reference Number</b></td>
							<td style="color:white;">'.$payment_note.'</td>
						</tr>
						<tr>
							<td colspan="2" style="color:white;">
								<center>Enter as payment description this unique reference number, <br>this will help to us to verify your deposit!</center>
							</td>
						</tr>
					</tbody>
				</table>
				</div>';
	$return .= '<br><center  style="color:white;">Deposit will be processed up to '.$processed_time.' after payment.</center><br/>';
	return $return;
}
function PF_m_1($id,$gateway)
{
	global $db, $settings;
	$GetDeposit = $db->query("SELECT * FROM deposits WHERE id='$id'");
	$get = $GetDeposit->fetch_assoc();
	$txidee = $get['txid'];
	$amount = $get['amount'];
	$currency = $get['currency'];
	$url = $settings['url'];
	$uid = $get['uid'];
	$payment_note = $get['reference_number'];
	$chk_deposit = $db->query("SELECT * FROM gateways WHERE id=$gateway");
    if($chk_deposit->num_rows < 0) {
        error("Gateways error");    
    }
    
    
    $return = '
	<div class="table-responsive">
				<table class="table table-striped">
					<tbody><br>';
	$QueryFields = $db->query("SELECT * FROM gateways_fields WHERE gateway_id='$gateway'");
	if($QueryFields->num_rows>0) {
		while($field = $QueryFields->fetch_assoc()) {
			$return .= '<tr>
					<td><b style="color:white;">'.$field['field_name'].'</b></td>
					<td style="color:white;">'.$field['field_value'].'</td>
				</tr>';
		    
		}
	}	
			
	
	$process_type = gatewayinfo($gateway,"process_type");
			$process_time = gatewayinfo($gateway,"process_time");
			if($process_type == "1") {
				if($process_time == "1") {
					$prcoessed_time = '1 minute';
				} else {
					$processed_time = $process_time.' minutes'; 
				}
			} elseif($process_type == "2") {
				if($process_time == "1") {
					$processed_time = '1 hour';
				} else {
					$processed_time = $process_time.' hours';
				}
			} elseif($process_type == "3") {
				if($process_time == "1") {
					$processed_time = '1 working day';
				} else {
					$processed_time = $process_time.' working days';
				}   
			} else {
				$processed_time = '';
			}
	        
	            $return .= '
	            <tr>
    				<td style="color:white;"><b>Amount</b></td>
    				<td style="color:white;">'.$amount.' '.$currency.'</td>
    			</tr>';
	        
			$return .= '
				        <tr>
							<td style="color:white;"><b>Reference Number</b></td>
							<td style="color:white;">'.$payment_note.'</td>
						</tr>
						
					</tbody>
				</table>
				</div>';
				$return .='
				<form action="" method="POST">
    				<div class="form-group"> 
        				<label style="color:white;">Enter Transaction ID</label> 
        				<input type="text" class="form-control" name="txid"> 
        				<input type="hidden" class="form-control" name="picic" value="'.$id.'"> 
    				</div>
    				<div class="row">
    				    <div class="col">
        				    <button type="submit" class="btn btn-primary" name="vvv" style="width:100%;">Submit</button>
        				</div>
        				    <div class="form-group"> 
                				<input type="hidden" class="form-control" name="picic" value="'.$id.'"> 
                				<input type="hidden" class="form-control" name="txdee" value="'.$txidee.'">
            				</div>
            			<div class="col">
        				    <button type="submit" class="btn btn-danger" name="canceleded" style="width:100%;">Cancel</button>
        				</div>
    				</div>
				</form>';
				$return .= '<br><center>Deposit will be processed up to '.$processed_time.' after payment.</center><br/>';

	
	return $return;
	
	  
				
	            
}
?>