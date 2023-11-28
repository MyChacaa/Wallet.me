<?php
if (isset($_GET['m_operation_id']) && isset($_GET['m_sign'])) {
	$m_operation_id = protect($_GET['m_operation_id']);
	$m_operation_date = protect($_GET['m_operation_date']);
	$m_orderid = protect($_GET['m_orderid']);
	$m_amount = protect($_GET['m_amount']);
	$m_currency = protect($_GET['m_curr']);
	$query = $db->query("SELECT * FROM deposits WHERE id='$m_orderid'");
		if($query->num_rows>0) {
			$row = $query->fetch_assoc();
			$m_key = gatewayinfo($row['method'],"a_field_2");
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
			if($row['status'] == "3") { 
				if ($_GET['m_sign'] == $sign_hash && $_GET['m_status'] == 'success') {
				if($m_amount == $row['amount'] or $m_currency == $row['currency']) {
					$time = time();
					$update = $db->query("UPDATE deposits SET status='1',gateway_txid='$m_operation_id',processed_on='$time' WHERE id='$row[id]'");
					$update = $db->query("UPDATE activity SET status='1' WHERE type='3' and u_field_1='$row[id]'");
					$update = $db->query("UPDATE transactions SET status='1' WHERE recipient='$row[id]'");
					PW_UpdateUserWallet($row['uid'],$row['amount'],$row['currency'],1);		
					header("Location: $settings[url]deposit/$row[id]/success");
				} 
				} 
			} 
	} 
}
?>