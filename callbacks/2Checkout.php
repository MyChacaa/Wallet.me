<?php
$sid = $_GET["sid"];    /* SID - Seller ID received */
$email = $_GET["email"];    /* Email received */
$order_number = $_GET["order_number"];    /* Order Number */
$currency_code = $_GET["currency_code"];    /* Currency Code */
$invoice_id = $_GET["invoice_id"];    /* Invoice Id */
$li_0_price = $_GET["li_0_price"];    /* Price */
$total = $_GET["total"];    /* Total Charge */
$oid = $_GET["oid"];    /* Order ID of Website */
$tx = $_GET["tx"];    /* User ID of Website */
$PTCOID = $_GET["PTCOID"];    /* PTCOID */

$query = $db->query("SELECT * FROM deposits WHERE id='$oid'");
if($query->num_rows>0) {
    $row = $query->fetch_assoc();
    $pass = gatewayinfo($row['method'],"a_field_2");    /* pass to compute HASH */
    $uid = $row['uid'];
    $payment_note = 'Deposit #'.$oid.' to '.idinfo($uid,"email").' wallet';
    $sign = base64_encode($payment_note);
    $v_sid = gatewayinfo($row['method'],"a_field_1");
    $chk = $db->query("SELECT gateway_txid FROM deposits WHERE gateway_txid='$PTCOID'");
    if($chk->num_rows > 0) {
        echo error("TID already used");
    } else {
    if ($sign == $tx && $sid == $v_sid && $currency_code == $row['currency'] && $li_0_price == $row['amount']) {
        if($row['status'] == "3") {
            
        $time = time();
		$update = $db->query("UPDATE deposits SET status='1',gateway_txid='$PTCOID',processed_on='$time' WHERE id='$row[id]'");
		$update = $db->query("UPDATE activity SET status='1' WHERE u_field_1='$row[id]'");
		$update = $db->query("UPDATE transactions SET status='1' WHERE recipient='$row[id]'");
		PW_UpdateUserWallet($row['uid'],$row['amount'],$row['currency'],1);
		
	    	
        header("Location: $settings[url]deposit/$row[id]/success");
        
        }
    } else {
        echo error("Not Match Found.");
    }

} 
} else {
    echo error("Data not found");
}
?>