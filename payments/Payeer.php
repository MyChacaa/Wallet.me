<?php 
// eWallet - PHP Script
// Author: DeluxeScript
$query_2 = $db->query("SELECT * FROM merchant_gateways WHERE name='Payeer'");
$row_2 = $query_2->fetch_assoc();
    
$query = $db->query("SELECT * FROM payments WHERE hash='$b'");
$row = $query->fetch_assoc();
$merchant_id = PW_GetUserID($row['merchant_account']);
$results = '';

$item_price = $row['item_price'];
$amount = number_format($item_price, 2, '.', '');
$currency = $row['item_currency'];

//FEE TAB
$per_fee = ($amount * $row_2['percentage_fee']) / 100;
if ($settings[default_currency] !== "$currency") {
        
    $fix_fee = $row_2['fix_fee'];
    $fix_fee = PW_currencyConvertor($fix_fee,$settings[default_currency],$currency);
    
} else {
    $fix_fee = $row_2['fix_fee'];
}

$fee = $per_fee + $fix_fee;

$biz_name = idinfo($merchant_id,"business_name");
$payment_note = "Item no . $row[item_number] | Item name : $row[item_name] | Business name : $biz_name | Hash : $b";

if (idinfo($merchant_id,"business_who_pay_fee") == "1") { //merchant will pay
    $amount_with_fee = $amount;
}
            
if (idinfo($merchant_id,"business_who_pay_fee") == "2") { //client will pay
    $amount_with_fee = $amount + $fee;
}

if ($currency !== $row_2['currency']) {
    $amount_with_fee = PW_currencyConvertor($amount_with_fee,$currency,"$row_2[currency]");
    $amount_with_fee = number_format($amount_with_fee, 2, '.', '');
    $currency = "$row_2[currency]";
    $update = $db->query("UPDATE payments SET convertion='$amount_with_fee' WHERE id='$row[id]'");
}

// PAYEER
    

    $merchant = $row_2[field_1];
	$secret = $row_2[field_2];
	
	$amount = $amount_with_fee;
    $m_shop = $merchant;
	$m_orderid = $b;
	$m_amount = number_format($amount, 2, '.', '');
	$m_curr = $currency;
	$desc = $payment_note;
	$m_desc = base64_encode($desc);
	$m_key = $secret;
	
	$arHash = array(
        $m_shop,
        $m_orderid,
        $m_amount,
        $m_curr,
        $m_desc
        );
        
    $arParams = array(
        'success_url' => "$settings[url]callbacks/checkPayment.php?a=Payeer_P",
    	'fail_url' => "$row[return_cancel]",
    	'status_url' => "$row[return_success]",
    // Forming an array for additional fields
        'reference' => array(
            'var1' => '1',
            'var2' => '2',
            'var3' => '3',
            'var4' => '4',
            'var5' => '5',
        ),
        //'submerchant' => 'mail.com',
    );
    
    $key = md5($row_2[field_2].$m_orderid);

    $m_params = @urlencode(base64_encode(openssl_encrypt(json_encode($arParams), 'AES-256-CBC', $key, OPENSSL_RAW_DATA)));

    $arHash[] = $m_params;

    $arHash[] = $m_key;
    
	$sign = strtoupper(hash('sha256', implode(':', $arHash)));
	
	$arGetParams = array(
	'm_shop' => $m_shop,
	'm_orderid' => $m_orderid,
	'm_amount' => $m_amount,
	'm_curr' => $m_curr,
	'm_desc' => $m_desc,
	'm_sign' => $sign,
	'm_params' => $params,
	'm_cipher_method' => 'AES-256-CBC',
	
    );
    
    $url = 'https://payeer.com/merchant/?'.http_build_query($arGetParams);

echo '
	<form method="POST" id="payeer_form" action="https://payeer.com/merchant/">
		<input type="hidden" name="m_shop" value="'.$m_shop.'">
        <input type="hidden" name="m_orderid" value="'.$m_orderid.'">
        <input type="hidden" name="m_amount" value="'.$m_amount.'">
        <input type="hidden" name="m_curr" value="'.$m_curr.'">
        <input type="hidden" name="m_desc" value="'.$m_desc.'">
        <input type="hidden" name="m_sign" value="'.$sign.'">
		
		<input type="hidden" name="m_params" value="'.$m_params.'">
        <input type="hidden" name="m_cipher_method" value="AES-256-CBC">
		
		
		</form>
		
		
	<script type="text/javascript" src="'.$settings[url].'assets/js/jquery-1.12.4.min.js"></script>
	<script type="text/javascript">$(document).ready(function() { $("#payeer_form")[0].submit();; });</script>
	<br><center><i class="fa fa-spin fa-spinner"></i><br/>Redirecting to Payeer...</center><br>';
	
	?>