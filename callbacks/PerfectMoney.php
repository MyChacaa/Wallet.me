<?php
$orderid = protect($_POST['PAYMENT_ID']);
$eamount = protect($_POST['PAYMENT_AMOUNT']);
$ecurrency = protect($_POST['PAYMENT_UNITS']);
$buyer = protect($_POST['PAYEE_ACCOUNT']);
$trans_id = protect($_POST['PAYMENT_BATCH_NUM']);
$date = date("d/m/Y H:i:s");
$query = $db->query("SELECT * FROM deposits WHERE id='$orderid'");
if($query->num_rows>0) {
	$row = $query->fetch_assoc();
	$passpharce = gatewayinfo($row['method'],"a_field_2");
	$alternate = strtoupper(md5($passpharce));
	$string=
		$_POST['PAYMENT_ID'].':'.$_POST['PAYEE_ACCOUNT'].':'.
		$_POST['PAYMENT_AMOUNT'].':'.$_POST['PAYMENT_UNITS'].':'.
		$_POST['PAYMENT_BATCH_NUM'].':'.
		$_POST['PAYER_ACCOUNT'].':'.$alternate.':'.
		$_POST['TIMESTAMPGMT'];
	$hash=strtoupper(md5($string));
	if($hash==$hash){ // proccessing payment if only hash is valid

		/* In section below you must implement comparing of data you recieved
		with data you sent. This means to check if $_POST['PAYMENT_AMOUNT'] is
		particular amount you billed to client and so on. */
		if($row['status'] == "3") { 
		if($_POST['PAYMENT_AMOUNT']==$row['amount'] && $_POST['PAYEE_ACCOUNT']==gatewayinfo($row['method'],"a_field_1") && $_POST['PAYMENT_UNITS']==$row['currency']){
			if($check_trans->num_rows==0) {
				$time = time();
					$update = $db->query("UPDATE deposits SET status='1',gateway_txid='$trans_id',processed_on='$time' WHERE id='$row[id]'");
					$update = $db->query("UPDATE activity SET status='1' WHERE type='3' and u_field_1='$row[id]'");
					$update = $db->query("UPDATE transactions SET status='1' WHERE recipient='$row[id]'");
					PW_UpdateUserWallet($row['uid'],$row['amount'],$row['currency'],1);		
			}
		}	
		}						 
	}
}
?>