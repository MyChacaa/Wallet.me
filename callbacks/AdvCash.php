<?php
$ac_src_wallet = protect($_GET['ac_src_wallet']);
$ac_dest_wallet = protect($_GET['ac_dest_wallet']);
$ac_amount = protect($_GET['ac_amount']);
$ac_merchant_currency = protect($_GET['ac_merchant_currency']);
$ac_transfer = protect($_GET['ac_transfer']);
$ac_start_date = protect($_GET['ac_start_date']);
$ac_order_id = protect($_GET['ac_order_id']);
$query = $db->query("SELECT * FROM deposits WHERE id='$ac_order_id'");
if($query->num_rows>0) {
	$row = $query->fetch_assoc();
	if($row['status'] == "3") { 
		if($ac_dest_wallet == gatewayinfo($row['method'],"a_field_4")) {
			if($ac_amount == $row['amount'] or $ac_merchant_currency == $row['currency']) {
				$time = time();
				$update = $db->query("UPDATE deposits SET status='1',gateway_txid='$ac_transfer',processed_on='$time' WHERE id='$row[id]'");
				$update = $db->query("UPDATE activity SET status='1' WHERE type='3' and u_field_1='$row[id]'");
				$update = $db->query("UPDATE transactions SET status='1' WHERE recipient='$row[id]'");
				PW_UpdateUserWallet($row['uid'],$row['amount'],$row['currency'],1);                 
			}
		}
	}
}
?>