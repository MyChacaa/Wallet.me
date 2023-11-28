<?php
$query_1 = $db->query("SELECT * FROM merchant_gateways WHERE name='Stripe'");
$row_1 = $query_1->fetch_assoc();

define('STRIPE_API_KEY', $row_1[field_1]); 
define('STRIPE_PUBLISHABLE_KEY', $row_1[field_2]); 
  
$response = array();

// Check whether stripe token is not empty
if(!empty($_POST['stripeToken'])){
    
    $productName = $_SESSION['pw_stripe_productName']; 
    $productNumber = $_SESSION['pw_stripe_productNumber']; 
    $productPrice = $_SESSION['pw_stripe_productPrice']; 
    $currency = $_SESSION['pw_stripe_currency']; 
    
    // Convert product price to cent
    $stripeAmount = round($productPrice*100, 2);
    // Get token and buyer info
    $token  = $_POST['stripeToken'];
    $email  = $_POST['stripeEmail'];
    
    // Include Stripe PHP library 
    require_once '../includes/payment_src/stripe-php/init.php'; 
    
    // Set API key
    \Stripe\Stripe::setApiKey(STRIPE_API_KEY);
	
	// Add customer to stripe 
    $customer = \Stripe\Customer::create(array( 
        'email' => $email, 
        'source'  => $token 
    )); 
    
    // Charge a credit or a debit card
    $charge = \Stripe\Charge::create(array(
        'customer' => $customer->id,
        'amount'   => $stripeAmount,
        'currency' => $currency,
        'description' => $productName,
    ));
    
    $query = $db->query("SELECT * FROM payments WHERE hash='$productNumber'");
    if($query->num_rows==0) {
        die("Wrong ORDER id!");
    }
    $row = $query->fetch_assoc();
    
    // Retrieve charge details
    $chargeJson = $charge->jsonSerialize();

    // Check whether the charge is successful
    if($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1){
        
        // Order details
		$txnID = $chargeJson['balance_transaction']; 
        $paidAmount = ($chargeJson['amount']/100);
        $paidCurrency = $chargeJson['currency']; 
        $status = $chargeJson['status'];
        $orderID = $chargeJson['id'];
        $payerName = $chargeJson['source']['name'];
		
		// Include database connection file  
        $merchant_id = PW_GetUserID($row['merchant_account']);
        $recipient_id = PW_GetUserID($row['merchant_account']);
        $txid = strtoupper(randomHash(10));
        $time = time();
        
        $item_price = $row['item_price'];
        $amount = number_format($item_price, 2, '.', '');
        $currency = $row['item_currency'];
        
        //FEE TAB
        $per_fee = ($amount * $row_1['percentage_fee']) / 100;
        if ($settings[default_currency] !== "$currency") {
                
            $fix_fee = $row_1['fix_fee'];
            $fix_fee = PW_currencyConvertor($fix_fee,$settings[default_currency],$currency);
            
        } else {
            $fix_fee = $row_1['fix_fee'];
        }
        
        $fee = $per_fee + $fix_fee;
        
        if (idinfo($merchant_id,"business_who_pay_fee") == "1") {  //merchant will pay
            $verify_cc = $amount;
        }
        
        if (idinfo($merchant_id,"business_who_pay_fee") == "2") {  // client will pay
            $verify_cc = $amount + $fee;
        }
        
        if ($currency !== "$row_1[currency]") {
            $currency_cc = "$row_1[currency]";
            $verify_cc = $row[convertion];
        } else {
            $currency_cc = $currency;
            $verify_cc = $verify_cc;
        }
        
        
        if (idinfo($merchant_id,"business_who_pay_fee") == "1") { //merchant will pay fee
                
            $amount_with_fee = $amount - $fee;
            // PW_UpdateUserWallet($_SESSION['pw_uid'],$amount,$currency,2);
            PW_UpdateUserWallet($recipient_id,$amount_with_fee,$currency,1);
            
            $create_transaction = $db->query("INSERT transactions (txid,type,sender,recipient,description,amount,currency,fee,status,created,item_id,item_name) 
            VALUES ('$txid','32','','$recipient_id','$description','$amount','$currency','$fee','1','$time','$row[item_number]','$row[item_name]')");
            
            $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
            VALUES ('$txid','32','$recipient_id','','$amount_with_fee','$currency','1','$time')");
            
        }
    
        if (idinfo($merchant_id,"business_who_pay_fee") == "2") { //client will pay fee
            
            $amount_with_fee = $amount + $fee;
            
            // PW_UpdateUserWallet($_SESSION['pw_uid'],$amount_with_fee,$currency,2);
            PW_UpdateUserWallet($recipient_id,$amount,$currency,1);
            
            $create_transaction = $db->query("INSERT transactions (txid,type,sender,recipient,description,amount,currency,fee,status,created,item_id,item_name) 
            VALUES ('$txid','32','','$recipient_id','$description','$amount_with_fee','$currency','$fee','1','$time','$row[item_number]','$row[item_name]')");
            
            $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
            VALUES ('$txid','32','$recipient_id','','$amount','$currency','1','$time')");
            
        }
        
        $update = $db->query("UPDATE payments SET payment_status='4',txid='$txid',gateway_txid='$txnID' WHERE id='$row[id]'");
        $row['payment_status'] = '4';
        $row['txid'] = $txid;
        $time = time();
        
        PW_UpdateAdminWallet($fee,$currency);
        $insert_admin_log = $db->query("INSERT admin_logs (type,time,u_field_1,u_field_2,u_field_3) VALUES ('6','$time','$fee','$currency','$txid')");
        
        $email = idinfo($merchant_id,"email");
        PW_EmailSys_PaymentNotification_P($email,$amount,$currency,$description,$txid);
        
        $last_insert_id = $productNumber;
        // If order inserted successfully
		if($last_insert_id && $status == 'succeeded'){
            $response = array(
                'status' => 1,
                'msg' => 'Your Payment has been Successful!',
                'txnData' => $chargeJson
            );
        }else{
            $response = array(
                'status' => 0,
                'msg' => 'Transaction has been failed.'
            );
        }
    }else{
        $response = array(
            'status' => 0,
            'msg' => 'Transaction has been failed.'
        );
    }
}else{
    $response = array(
        'status' => 0,
        'msg' => 'Form submission error...'
    );
}

// Return response
echo json_encode($response);
?>