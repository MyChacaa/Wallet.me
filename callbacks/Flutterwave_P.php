<?php
$tx_ref= protect($_GET['tx_ref']); // Referrence Id
$transaction_id = protect($_GET['transaction_id']); // Transaction Id
$status= protect($_GET['status']); // Status


$query_1 = $db->query("SELECT * FROM merchant_gateways WHERE name='Flutterwave'");
$row_1 = $query_1->fetch_assoc();

$query = $db->query("SELECT * FROM payments WHERE hash='$tx_ref'");
if($query->num_rows>0) {
$row = $query->fetch_assoc();
	if($status == "successful"){
        if(!empty($transaction_id)){
            $chk = $db->query("SELECT gateway_txid FROM payments WHERE gateway_txid='$transaction_id'");
            if($chk->num_rows > 0) {
                error("TID already used");
            } else {
                $Secret_key = $row_1['field_1'];
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/$transaction_id/verify",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "GET",
                  CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: Bearer $Secret_key"
                  ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);
                $obj = json_decode($response);
                $api_status=$obj->status;  //Status
                $api_id=$obj->data->id;  // TID
                $api_tx_ref=$obj->data->tx_ref;  // Reference Number
                $api_amount=$obj->data->amount;  // Amount
                $api_currency=$obj->data->currency;  // Currency
                $api_status=$obj->data->status;  // Data Status


                $merchant_id = PW_GetUserID($row['merchant_account']);
                $recipient_id = PW_GetUserID($row['merchant_account']);
                $txid = strtoupper(randomHash(10));
                $time = time();
                
                $item_price = $row['item_price'];
                $amount = number_format($item_price, 2, '.', '');
                $currency = $row['item_currency'];
                
                //FEE TAB
                $per_fee = ($amount * $row_1['percentage_fee']) / 100;
                if ($settings['default_currency'] !== "$currency") {
                        
                    $fix_fee = $row_1['fix_fee'];
                    $fix_fee = PW_currencyConvertor($fix_fee,$settings['default_currency'],$currency);
                    
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
                    $verify_cc = $row['convertion'];
                } else {
                    $currency_cc = $currency;
                    $verify_cc = $verify_cc;
                }

                if ($api_status == $status && $api_currency == $currency_cc && $api_amount == $verify_cc && $api_tx_ref == $tx_ref && $api_id == $transaction_id) {
                    if($row['payment_status'] == "1") {
                		if($api_status == "successful" && !empty($transaction_id)){
                			
                			if (idinfo($merchant_id,"business_who_pay_fee") == "1") { //merchant will pay fee
                
                                $amount_with_fee = $amount - $fee;
                                PW_UpdateUserWallet($recipient_id,$amount_with_fee,$currency,1);
                                
                                $create_transaction = $db->query("INSERT transactions (txid,type,sender,recipient,description,amount,currency,fee,status,created,item_id,item_name) 
                                VALUES ('$txid','33','','$recipient_id','$description','$amount','$currency','$fee','1','$time','$row[item_number]','$row[item_name]')");
                                
                                $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                                VALUES ('$txid','33','$recipient_id','','$amount_with_fee','$currency','1','$time')");
                                
                            }
                        
                            if (idinfo($merchant_id,"business_who_pay_fee") == "2") { //client will pay fee
                                
                                $amount_with_fee = $amount + $fee;
                                
                                // PW_UpdateUserWallet($_SESSION['pw_uid'],$amount_with_fee,$currency,2);
                                PW_UpdateUserWallet($recipient_id,$amount,$currency,1);
                                
                                $create_transaction = $db->query("INSERT transactions (txid,type,sender,recipient,description,amount,currency,fee,status,created,item_id,item_name) 
                                VALUES ('$txid','33','','$recipient_id','$description','$amount_with_fee','$currency','$fee','1','$time','$row[item_number]','$row[item_name]')");
                                
                                $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                                VALUES ('$txid','33','$recipient_id','','$amount','$currency','1','$time')");
                                
                            }
                			
                			$update = $db->query("UPDATE payments SET payment_status='4',txid='$txid',gateway_txid='$transaction_id' WHERE id='$row[id]'");
                            $row['payment_status'] = '4';
                            $row['txid'] = $txid;
                            $time = time();
                            
                            PW_UpdateAdminWallet($fee,$currency);
                            $insert_admin_log = $db->query("INSERT admin_logs (type,time,u_field_1,u_field_2,u_field_3) VALUES ('6','$time','$fee','$currency','$txid')");
                            
                            $email = idinfo($merchant_id,"email");
                            PW_EmailSys_PaymentNotification_P($email,$amount,$currency,$description,$txid);
                			
                			$results = '
        				    <form id="Payment_Success_Form" method="POST" action="'.$row['return_success'].'">
                                <input type="hidden" name="merchant_account" value="'.$row['merchant_account'].'">
                                <input type="hidden" name="item_number" value="'.$row['item_number'].'">
                                <input type="hidden" name="item_name" value="'.$row['item_name'].'">
                                <input type="hidden" name="item_price" value="'.$row['item_price'].'">
                                <input type="hidden" name="item_currency" value="'.$row['item_currency'].'">
                                <input type="hidden" name="txid" value="'.$row['txid'].'">
                                <input type="hidden" name="payment_time " value="'.$time.'">
                                <input type="hidden" name="payee_account " value="'.idinfo($_SESSION['pw_uid'],"email").'">
                            </form>
                            <script src="'.$settings[url].'assets/js/jquery-1.12.4.min.js"></script>';
                            
                            $results .= " 
                            <script type='text/javascript'>
                            $(document).ready(function() {
                                $('#Payment_Success_Form')[0].submit();
                            }); 
                            </script>";
                            echo $results;
                	    }	
                	} else {
            		    echo "Error! Payment not received. Please pay again.";
		            }
                } else {
                    echo "Error! Payment not received. Please pay again.";
                }
            }
        }else{
            echo "Error! Payment not received. Please pay again.";
        }
    }else{
        echo "Error! Payment not received. Please pay again.";
}
}else{
    echo "Error! Payment not received. Please pay again.";
}
?>


                        