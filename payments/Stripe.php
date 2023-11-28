<?php 
// eWallet - PHP Script
// Author: DeluxeScript
$query_1 = $db->query("SELECT * FROM merchant_gateways WHERE name='Stripe'");
$row_1 = $query_1->fetch_assoc();
$query = $db->query("SELECT * FROM payments WHERE hash='$b'");
$row = $query->fetch_assoc();
$merchant_id = PW_GetUserID($row['merchant_account']);
$results = '';
if($settings['favicon']) {
    $logo = $settings['url'].$settings['favicon'];
	
} else {
    $logo = "$settings[url]assets/logo/favicon.png";
}
$item_price = $row['item_price'];
$amount = number_format($item_price, 2, '.', '');
$currency = $row['item_currency'];

//Fee Tab
$per_fee = ($amount * $row_1['percentage_fee']) / 100;
if ($settings[default_currency] !== "$currency") {
        
    $fix_fee = $row_1['fix_fee'];
    $fix_fee = PW_currencyConvertor($fix_fee,$settings[default_currency],$currency);
    
} else {
    $fix_fee = $row_1['fix_fee'];
}

$fee = $per_fee + $fix_fee;

$biz_name = idinfo($merchant_id,"business_name");
$payment_note = "Item no . $row[item_number] | Item name : $row[item_name] | Business name : $biz_name | Hash : $b";


if (idinfo($merchant_id,"business_who_pay_fee") == "1") { //merchant will pay
    $amount_with_fee = $amount;
}
            
if (idinfo($merchant_id,"business_who_pay_fee") == "2") { //client will pay
    $amount_with_fee = $amount + $fee;
}

if ($currency !== $row_1[currency]) {
    $amount_with_fee = PW_currencyConvertor($amount_with_fee,$currency,"$row_1[currency]");
    $amount_with_fee = number_format($amount_with_fee, 2, '.', '');
    $currency = "$row_1[currency]";
    $update = $db->query("UPDATE payments SET convertion='$amount_with_fee' WHERE id='$row[id]'");
}


            		
?>
<?php

    define('STRIPE_API_KEY', $row_1[field_1]); 
	define('STRIPE_PUBLISHABLE_KEY', $row_1[field_2]); 
	$productName = $payment_note; 
	$productNumber = $b; 
	$productPrice = $amount_with_fee;
	$currency = strtolower($currency);
		// Convert product price to cent
	$stripeAmount = round($productPrice*100, 2);
	$_SESSION['pw_stripe_productName'] = $productName;
	$_SESSION['pw_stripe_productNumber'] = $productNumber;
	$_SESSION['pw_stripe_productPrice'] = $productPrice;
	$_SESSION['pw_stripe_gateway'] = 3;
	$_SESSION['pw_stripe_currency'] = $currency;
	$_SESSION['pw_stripe_stripeAmount'] = $stripeAmount;
	$return .= '<script type="text/javascript" src="'.$settings[url].'assets/js/jquery-1.12.4.min.js"></script>';
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
				url: "'.$settings[url].'callbacks/checkPayment.php?a=Stripe_P",
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
						$(location).attr("href", "'.$settings[url].'callbacks/checkPayment.php?a=Stripe_P_S&hash='.$b.'");
					}else{
						$("#payButton").prop("disabled", false);
						$("#payButton").html("Deposit via VISA/MasterCard");
						alert("Some problem occurred1, please try again.");
					}
				},
				error: function() {
					$("#payProcess").val(0);
					$("#payButton").prop("disabled", false);
					$("#payButton").html("Deposit via VISA/MasterCard");
					alert("Some problem occurred2, please try again.");
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
				name: "'.$settings[name].'",
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
	echo $return;


?>