<?php

$hash = protect($_GET['hash']);

$query = $db->query("SELECT * FROM payments WHERE hash='$hash'");
if($query->num_rows>0) {
	$row = $query->fetch_assoc();
	if($row['payment_status'] == "4") {
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
} else { echo "404 Page Not Found"; }