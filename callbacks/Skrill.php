<?php
$transaction_id = protect($_POST['transaction_id']);
$merchant_id = protect($_POST['pay_to_email']);
$item_number = protect($_POST['detail1_text']);
$item_name = protect($_POST['detail1_description']);
$mb_amount = protect($_POST['mb_amount']);
$mb_currency = protect($_POST['mb_currency']);
$date = date("d/m/Y H:i:s");
$query = $db->query("SELECT * FROM deposits WHERE id='$item_number'");
if($query->num_rows>0) {
	$row = $query->fetch_assoc();
	$a_field_1 = gatewayinfo($row['method'],"a_field_1");
	$a_field_2 = gatewayinfo($row['method'],"a_field_2");
	$concatFields = $_POST['merchant_id']
		.$_POST['transaction_id']
		.strtoupper(md5($a_field_2))
		.$_POST['mb_amount']
		.$_POST['mb_currency']
		.$_POST['status'];

	$MBEmail = $a_field_1;

	// Ensure the signature is valid, the status code == 2,
	// and that the money is going to you
	if (strtoupper(md5($concatFields)) == $_POST['md5sig']
		&& $_POST['status'] == 2
		&& $_POST['pay_to_email'] == $MBEmail)
	{
		// payment successfully...
		if($mb_amount == $row['amount'] && $mb_currency == $row['currency']) {
			if($row['status'] !== "3") { 
				$time = time();
					$update = $db->query("UPDATE deposits SET status='1',gateway_txid='$transaction_id',processed_on='$time' WHERE id='$row[id]'");
					$update = $db->query("UPDATE activity SET status='1' WHERE type='3' and u_field_1='$row[id]'");
					$update = $db->query("UPDATE transactions SET status='1' WHERE recipient='$row[id]'");
					PW_UpdateUserWallet($row['uid'],$row['amount'],$row['currency'],1);		
			}
		}
	}
}			
header("Location: $settings[url]");			
?>