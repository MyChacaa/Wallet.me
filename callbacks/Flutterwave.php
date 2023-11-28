<?php
$tx_ref= protect($_GET['tx_ref']); // Referrence Id
$transaction_id = protect($_GET['transaction_id']); // Transaction Id
$status= protect($_GET['status']); // Status

$query = $db->query("SELECT * FROM deposits WHERE reference_number='$tx_ref'");
if($query->num_rows>0) {
$row = $query->fetch_assoc();
	if($status == "successful"){
        if(!empty($transaction_id)){
            $chk = $db->query("SELECT gateway_txid FROM deposits WHERE gateway_txid='$transaction_id'");
            if($chk->num_rows > 0) {
                error("TID already used");
                header("Location: $settings[url]deposit/$row[id]/fail");
            } else {
                $Secret_key = gatewayinfo($row['method'],"a_field_2");
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


                $fee_with_Amount= $api_amount; // Received Amount With Fee 
                $fee = $row['fee'];         // System Deposit Fee
                $Amount = $fee_with_Amount - $fee; // Pure Received Amount 

                if ($api_status == $status && $api_currency == $row['currency'] && $api_amount == $row['amount'] && $api_tx_ref == $tx_ref && $api_id == $transaction_id) {
                    if($row['status'] == "3") {
                		if($api_tx_ref==$row['reference_number'] && $api_status == "successful" && !empty($transaction_id && $api_id == $transaction_id)){
                			if($check_trans->num_rows==0) {
                				$time = time();
            					$update = $db->query("UPDATE deposits SET status='1',gateway_txid='$api_id',processed_on='$time' WHERE id='$row[id]'");
            					$update = $db->query("UPDATE activity SET status='1' WHERE u_field_1='$row[id]'");
            					$update = $db->query("UPDATE transactions SET status='1' WHERE recipient='$row[id]'");
            					PW_UpdateUserWallet($row['uid'],$row['amount'],$row['currency'],1);
            					
            				    	
                                header("Location: $settings[url]deposit/$row[id]/success");
                                echo'sucess';
                			}
                	    }	
                	} else {
            		    $update = $db->query("UPDATE deposits SET status='$row[status]',gateway_txid='$transaction_id',processed_on='$time' WHERE id='$row[id]'");
                        $update = $db->query("UPDATE activity SET status='$row[status]' WHERE u_field_1='$row[id]'");
                        $update = $db->query("UPDATE transactions SET status='$row[status]' WHERE recipient='$row[id]'");
                        header("Location: $settings[url]account/summary");
		            }
                } else {
                    $update = $db->query("UPDATE deposits SET status='2',gateway_txid='$transaction_id',processed_on='$time' WHERE id='$row[id]'");
                    $update = $db->query("UPDATE activity SET status='2' WHERE u_field_1='$row[id]'");
                    $update = $db->query("UPDATE transactions SET status='2' WHERE recipient='$row[id]'");
                    header("Location: $settings[url]deposit/$row[id]/fail");
                }
            }
        }else{
            $update = $db->query("UPDATE deposits SET status='2',gateway_txid='$transaction_id',processed_on='$time' WHERE id='$row[id]'");
            $update = $db->query("UPDATE activity SET status='2' WHERE u_field_1='$row[id]'");
            $update = $db->query("UPDATE transactions SET status='2' WHERE recipient='$row[id]'");
            header("Location: $settings[url]deposit/$row[id]/fail");
        }
    }else{
        $update = $db->query("UPDATE deposits SET status='2',gateway_txid='$transaction_id',processed_on='$time' WHERE id='$row[id]'");
        $update = $db->query("UPDATE activity SET status='2' WHERE u_field_1='$row[id]'");
        $update = $db->query("UPDATE transactions SET status='2' WHERE recipient='$row[id]'");
        echo "Error! Payment not received. Please pay again.";
        header("Location: $settings[url]deposit/$row[id]/fail");
}
}else{
    $update = $db->query("UPDATE deposits SET status='2',gateway_txid='$transaction_id',processed_on='$time' WHERE id='$row[id]'");
    $update = $db->query("UPDATE activity SET status='2' WHERE u_field_1='$row[id]'");
    $update = $db->query("UPDATE transactions SET status='2' WHERE recipient='$row[id]'");
    echo "Error! Payment not received. Please pay again.";
    header("Location: $settings[url]deposit/$row[id]/fail");
}
?>


                        