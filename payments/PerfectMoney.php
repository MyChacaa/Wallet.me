<?php 
// eWallet - PHP Script
// Author: DeluxeScript

$query_1 = $db->query("SELECT * FROM merchant_gateways WHERE name='Perfect Money'");
$row_1 = $query_1->fetch_assoc();

$query = $db->query("SELECT * FROM payments WHERE hash='$b'");
$row = $query->fetch_assoc();
$merchant_id = PW_GetUserID($row['merchant_account']);
$results = '';

$item_price = $row['item_price'];
$amount = number_format($item_price, 2, '.', '');
$currency = $row['item_currency'];

//Fee Tab
$per_fee = ($amount * $row_1['percentage_fee']) / 100;
if ($settings[default_currency] !== "$currency") {
        
    $fix_fee = $row_1['fix_fee'];
    $fix_fee = PW_currencyConvertor($fix_fee,$settings[default_currency],$currency);
    
} else {
    $fix_fee = $row_1['fix_fee'];
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

if ($currency !== $row_1[currency]) {
    $amount_with_fee = PW_currencyConvertor($amount_with_fee,$currency,"$row_1[currency]");
    $amount_with_fee = number_format($amount_with_fee, 2, '.', '');
    $currency = "$row_1[currency]";
    $update = $db->query("UPDATE payments SET convertion='$amount_with_fee' WHERE id='$row[id]'");
}


            		
?>
<div style="display:none;">
    <form action="https://perfectmoney.is/api/step1.asp" id="pm_form" method="POST">
        <input type="hidden" name="PAYEE_ACCOUNT" value="<?= $row_1[field_1] ?>">
        <input type="text"   name="PAYMENT_AMOUNT" value="<?php echo $amount_with_fee ?>">
        <input type="hidden" name="PAYEE_NAME" value="<?php echo $settings[name] ?>">
        <input type="hidden" name="PAYMENT_ID" value="<?php echo $b ?>">
        <input type="hidden" name="PAYMENT_UNITS" value="<?php echo $currency ?>">
        <input type="hidden" name="STATUS_URL" value="<?php echo filter_var($row['return_success'], FILTER_SANITIZE_STRING) ?>">
        <input type="hidden" name="PAYMENT_URL" value="<?php echo $settings[url] ?>callbacks/checkPayment.php?a=PerfectMoney_P">
        <input type="hidden" name="PAYMENT_URL_METHOD" value="POST">
        <input type="hidden" name="NOPAYMENT_URL" value="<?php echo filter_var($row['return_cancel'], FILTER_SANITIZE_STRING) ?>">
        <input type="hidden" name="NOPAYMENT_URL_METHOD" value="POST">
        <input type="hidden" name="SUGGESTED_MEMO" value="<?php echo $payment_note ?>">
        <input type="hidden" name="BAGGAGE_FIELDS" value="IDENT"><br>
        <input type="submit" name="PAYMENT_METHOD" value="Pay Now!" class="tabeladugme"><br><br>
    </form>
</div>

<?php
    
    $return .= '<script type="text/javascript" src="'.$settings[url].'assets/js/jquery-1.12.4.min.js"></script>';
	$return .= '<script type="text/javascript">$(document).ready(function() { $("#pm_form")[0].submit();; });</script>';
	$return .= '<br><center><i class="fa fa-spin fa-spinner"></i><br/>Redirecting to Perfect Money...</center><br>';
	echo $return;
    
?>