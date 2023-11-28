<?php
$query_2 = $db->query("SELECT * FROM merchant_gateways WHERE name='Payeer'");
$row_2 = $query_2->fetch_assoc();
    
if (isset($_GET['m_operation_id']) && isset($_GET['m_sign'])) {
	$m_operation_id = protect($_GET['m_operation_id']);
	$m_operation_date = protect($_GET['m_operation_date']);
	$m_orderid = protect($_GET['m_orderid']);
	$m_amount = protect($_GET['m_amount']);
	$m_currency = protect($_GET['m_curr']);
	$query = $db->query("SELECT * FROM payments WHERE hash='$m_orderid'");
		if($query->num_rows>0) {
			$row = $query->fetch_assoc();
			
			$m_key = $row_2[field_2];
        	$arHash = array(
        		$_GET['m_operation_id'],
        		$_GET['m_operation_ps'],
        		$_GET['m_operation_date'],
        		$_GET['m_operation_pay_date'],
        		$_GET['m_shop'],
        		$_GET['m_orderid'],
        		$_GET['m_amount'],
        		$_GET['m_curr'],
        		$_GET['m_desc'],
        		$_GET['m_status']
        	);
        
        	if (isset($_GET['m_params']))
        	{
        		$arHash[] = $_GET['m_params'];
        	}
        
        	$arHash[] = $m_key;
        
        	$sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));
			
			if($row['payment_status'] == "1") {
			    
			    $merchant_id = PW_GetUserID($row['merchant_account']);
                $recipient_id = PW_GetUserID($row['merchant_account']);
                $txid = strtoupper(randomHash(10));
                $time = time();
                
                $item_price = $row['item_price'];
                $amount = number_format($item_price, 2, '.', '');
                $currency = $row['item_currency'];
			    
			    //FEE TAB
                $per_fee = ($amount * $row_2['percentage_fee']) / 100;
                if ($settings['default_currency'] !== "$currency") {
                        
                    $fix_fee = $row_2['fix_fee'];
                    $fix_fee = PW_currencyConvertor($fix_fee,$settings['default_currency'],$currency);
                    
                } else {
                    $fix_fee = $row_2['fix_fee'];
                }
                
                $fee = $per_fee + $fix_fee;
                
                if (idinfo($merchant_id,"business_who_pay_fee") == "1") {  //merchant will pay
                    $verify_cc = $amount;
                }
                
                if (idinfo($merchant_id,"business_who_pay_fee") == "2") {  // client will pay
                    $verify_cc = $amount + $fee;
                }
                
                if ($currency !== "$row_2['currency']") {
                    $currency_cc = "$row_2['currency']";
                    $verify_cc = $row['convertion'];
                } else {
                    $currency_cc = $currency;
                    $verify_cc = $verify_cc;
                }
			    
				
				if ($_GET['m_sign']==$sign_hash && $_GET['m_status'] == 'success') {
				    
				    if($m_amount == $verify_cc or $m_currency == $currency_cc) {
				    
				        if (idinfo($merchant_id,"business_who_pay_fee") == "1") { //merchant will pay fee
                
                            $amount_with_fee = $amount - $fee;
                            // PW_UpdateUserWallet($_SESSION['pw_uid'],$amount,$currency,2);
                            PW_UpdateUserWallet($recipient_id,$amount_with_fee,$currency,1);
                            
                            $create_transaction = $db->query("INSERT transactions (txid,type,sender,recipient,description,amount,currency,fee,status,created,item_id,item_name) 
                            VALUES ('$txid','31','','$recipient_id','$description','$amount','$currency','$fee','1','$time','$row[item_number]','$row[item_name]')");
                            
                            $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                            VALUES ('$txid','31','$recipient_id','','$amount_with_fee','$currency','1','$time')");
                            
                        }
            
                        if (idinfo($merchant_id,"business_who_pay_fee") == "2") { //client will pay fee
                            
                            $amount_with_fee = $amount + $fee;
                            
                            // PW_UpdateUserWallet($_SESSION['pw_uid'],$amount_with_fee,$currency,2);
                            PW_UpdateUserWallet($recipient_id,$amount,$currency,1);
                            
                            $create_transaction = $db->query("INSERT transactions (txid,type,sender,recipient,description,amount,currency,fee,status,created,item_id,item_name) 
                            VALUES ('$txid','31','','$recipient_id','$description','$amount_with_fee','$currency','$fee','1','$time','$row[item_number]','$row[item_name]')");
                            
                            $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                            VALUES ('$txid','31','$recipient_id','','$amount','$currency','1','$time')");
                            
                        }
                
                        $update = $db->query("UPDATE payments SET payment_status='4',txid='$txid',gateway_txid='$m_operation_id' WHERE id='$row[id]'");
                        $row['payment_status'] = '4';
                        $row['txid'] = $txid;
                        $time = time();
                        
                        PW_UpdateAdminWallet($fee,$currency);
                        $insert_admin_log = $db->query("INSERT admin_logs (type,time,u_field_1,u_field_2,u_field_3) VALUES ('6','$time','$fee','$currency','$txid')");
                        
    				    $email = idinfo($merchant_id,"email");
                        PW_EmailSys_PaymentNotification_P($email,$amount,$currency,$description,$txid);
                        
                        $results = '
    				    <form id="PW_Payment_Success_Form" method="POST" action="'.$row['return_success'].'">
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
                                $('#PW_Payment_Success_Form')[0].submit();
                            }); 
                            </script>";
                        echo $results;
                        
				    }
				}
			}
	}
}
?>