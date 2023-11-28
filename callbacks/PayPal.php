<?php
$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

// assign posted variables to local variables
$item_name = protect($_POST['item_name']);
$item_number = protect($_POST['item_number']);
$payment_status = protect($_POST['payment_status']);
$payment_amount = protect($_POST['mc_gross']);
$payment_currency = protect($_POST['mc_currency']);
$txn_id = protect($_POST['txn_id']);
$receiver_email = protect($_POST['receiver_email']);
$payer_email = protect($_POST['payer_email']);
$query = $db->query("SELECT * FROM deposits WHERE id='$item_number'");
if($query->num_rows>0) {
	$row = $query->fetch_assoc();
	
	if (!$fp) {
		echo error("Cant connect to PayPal server.");
	} else {
		fputs ($fp, $header . $req);
		while (!feof($fp)) {
		$res = fgets ($fp, 1024);
		if (strcmp ($res, "VERIFIED") == 1) {
			if ($payment_status == 'Completed') {
				if($row['status'] !== "3") { 
				if ($receiver_email==gatewayinfo($row['method'],"a_field_1")) {
					if($payment_amount == $row['amount'] && $payment_currency == $row['currency']) {
						$time = time();
						$update = $db->query("UPDATE deposits SET status='1',gateway_txid='$txn_id',processed_on='$time' WHERE id='$row[id]'");
						$update = $db->query("UPDATE activity SET status='1' WHERE type='3' and u_field_1='$row[id]'");
						$update = $db->query("UPDATE transactions SET status='1' WHERE recipient='$row[id]'");
						PW_UpdateUserWallet($row['uid'],$row['amount'],$row['currency'],1);
					}
													
				} 
				}
			}

	}

	else if (strcmp ($res, "INVALID") == 0) {
		echo 'Invalid payment.';
	}
	}
	fclose ($fp);
	}  
}
?>