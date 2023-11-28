<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
function generateCode($limit){
    $code = 1;
    for($i = 1; $i < $limit; $i++) { $code .= mt_rand(1, 9); }
    return $code;
}
function Create_Payment_Link($merchant_account,$hash,$item_number,$item_name,$item_price,$item_currency,$return_success,$return_fail,$return_cancel,$time) {
	global $db;
	$insert = $db->query("INSERT payment_link (user_id,merchant_email,hash,item_number,item_name,item_price,item_currency,return_success,return_fail,return_cancel,time) 
	VALUES ('$_SESSION[pw_uid]','$merchant_account','$hash','$item_number','$item_name','$item_price','$item_currency','$return_success','$return_fail','$return_cancel','$time')");
}

function Delete_Payment_Link($hash) {
	global $db;
	$delete = $db->query("DELETE FROM payment_link WHERE hash='$hash'");
}

function idinfo($uid,$value) {
	global $db;
	$query = $db->query("SELECT * FROM users WHERE id='$uid'");
	$row = $query->fetch_assoc();
	return $row[$value];
}	

function gatewayinfo($gid,$value) {
	global $db;
	$query = $db->query("SELECT * FROM gateways WHERE id='$gid'");
	$row = $query->fetch_assoc();
	return $row[$value];
}

function PW_CheckUser($email) {
	global $db;
	$query = $db->query("SELECT * FROM users WHERE email='$email'");
	if($query->num_rows>0) {
		// user with this email address is exists
		return true;
	} else {
		// user with this email address does not exists
		return false;
	}
}

function PW_GetFieldName($id) {
	global $db;
	$query = $db->query("SELECT * FROM gateways_fields WHERE id='$id'");
	$row = $query->fetch_assoc();
	return $row['field_name'];
}

function PW_GetUserID($email) {
	global $db;
	$query = $db->query("SELECT * FROM users WHERE email='$email'");
	if($query->num_rows>0) {
		// user with this email address is exists
		$row = $query->fetch_assoc();
		return $row['id'];
	} else {
		// user with this email address does not exists
		return false;
	}
}

function PW_currencyConvertor($amount,$from_Currency,$to_Currency) {
	global $db, $settings;
	error_reporting(0);
	$am = urlencode($amount);
	$prefix = $from_Currency.'_'.$to_Currency;
	$from_Currency = strtolower($from_Currency);
	$to_Currency = strtolower($to_Currency);
	$ch = curl_init();
	$url = "https://www.floatrates.com/daily/$from_Currency.json"; //From Currency
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);
	$result=curl_exec($ch);
	curl_close($ch);
	$json = json_decode($result, true);
	
	$obj = json_decode($result);
    $rate=$obj->$to_Currency->rate;  //To Currency
	
	$converted_amount = $rate;
	if ($from_Currency == $to_Currency) {
        $converted_amount = 1;
        $converted_amount = $amount * $converted_amount;
        $converted_amount = number_format($converted_amount, 2, '.', '');
        return number_format($converted_amount, 2, '.', '');
        
    } else {
        $converted_amount = $amount * $converted_amount;
        $converted_amount = number_format($converted_amount, 4, '.', '');
        return number_format($converted_amount, 4, '.', '');
        
    }
}

function PW_GetRates($from_currency,$to_currency) {
	global $db, $settings;
	$data = array();
	if(empty($from_currency) or empty($to_currency)) {
		$data['status'] = 'error';
		$data['msg'] = 'Some of currencies are missing.';
	} else {
		$fee = 0;
		$rate_from = 1;
		$calculate = PW_currencyConvertor($rate_from,$from_currency,$to_currency);
		$calculate1 = ($calculate * $fee) / 100;
		$calculate2 = $calculate - $calculate1;
		if($calculate2 < 1) { 
			$calculate = PW_currencyConvertor($rate_from,$to_currency,$from_currency);
			$calculate1 = ($calculate * $fee) / 100;
			$calculate2 = $calculate - $calculate1;
			$rate_from = number_format($calculate2, 2, '.', '');
			$rate_to = 1;
		} else {
			$rate_to = number_format($calculate2, 2, '.', '');
		}
		$data['status'] = 'success';
		$data['rate_from'] = $rate_from;
		$data['rate_to'] = $rate_to;
		$data['currency_from'] = $from_currency;
		$data['currency_to'] = $to_currency;
		$data['fee'] = $calculate1;
	}
	return $data;
}

function PW_UpdateUserWallet($uid,$amount,$currency,$type) {
	global $db;
	$CheckWallet = $db->query("SELECT * FROM users_wallets WHERE uid='$uid' and currency='$currency'");
	if($CheckWallet->num_rows>0) {
		$row = $CheckWallet->fetch_assoc();
		if($type == "1") {
			$balance = $row['amount'] + $amount;
		} else {
			$balance = $row['amount'] - $amount;
		}
		$update = $db->query("UPDATE users_wallets SET amount='$balance' WHERE id='$row[id]'");
	} else {
		if($type == "1") {
			$balance = $amount;
		} else {
			$balance = '-'.$amount;
		}
		$insert = $db->query("INSERT users_wallets (uid,amount,currency) VALUES ('$uid','$amount','$currency')");
	}
}

function PW_DecodeUserActivity($activity_id) {
	global $db, $lang;
	$ActivityQuery = $db->query("SELECT * FROM activity WHERE id='$activity_id'");
	if($ActivityQuery->num_rows>0) {
		$row = $ActivityQuery->fetch_assoc();
		$type = $row['type'];
		if($type == "1") {
			// payment from ...
			if(idinfo($row['u_field_1'],"account_type") == "1") {
				$from = idinfo($row['u_field_1'],"first_name").' '.idinfo($row['u_field_1'],"last_name");
			} else {
				$from = idinfo($row['u_field_1'],"business_name");
			}
			return $lang['activity_1']." ".$from;
		} elseif($type == "2") {
			// payment to ...
			if(idinfo($row['u_field_1'],"account_type") == "1") {
				$from = idinfo($row['u_field_1'],"first_name").' '.idinfo($row['u_field_1'],"last_name");
			} else {
				$from = idinfo($row['u_field_1'],"business_name");
			}
			return $lang['activity_2']." ".$from;
		} elseif($type == "3") {
			// deposit via ...
			$via = gatewayinfo($row['deposit_via'],"name");
			return $lang['activity_3']." ".$via;
		} elseif($type == "4") {
			// withdrawal to ...
			$to = gatewayinfo($row['withdrawal_via'],"name");
			return $lang['activity_4']." ".$to;
		} elseif($type == "5") {
			// refund from ...
			if(idinfo($row['u_field_1'],"account_type") == "1") {
				$from = idinfo($row['u_field_1'],"first_name").' '.idinfo($row['u_field_1'],"last_name");
			} else {
				$from = idinfo($row['u_field_1'],"business_name");
			}
			return $lang['activity_5']." ".$from;
		} elseif($type == "6") {
			// refund to ...
			if(idinfo($row[u_field_1],"account_type") == "1") {
				$from = idinfo($row['u_field_1'],"first_name").' '.idinfo($row['u_field_1'],"last_name");
			} else {
				$from = idinfo($row['u_field_1'],"business_name");
			}
			return $lang['activity_6']." ".$from;
		} elseif($type == "7") {
			// fee reversal
			return $lang['activity_7'];
		} elseif($type == "8") {
			// convert amount from 	
			$QueryConvert = $db->query("SELECT * FROM users_converts WHERE id='$row[u_field_1]'");
			$cnv = $QueryConvert->fetch_assoc();
			$activity_8 = str_ireplace("%from_currency%",$cnv['from_currency'],$lang['activity_8']);
			$activity_8 = str_ireplace("%to_currency%",$cnv['to_currency'],$activity_8);
			return $activity_8;
		} elseif($type == "9") {
			// convert amount to
			$QueryConvert = $db->query("SELECT * FROM users_converts WHERE id='$row[u_field_1]'");
			$cnv = $QueryConvert->fetch_assoc();
			$activity_8 = str_ireplace("%from_currency%",$cnv['from_currency'],$lang['activity_8']);
			$activity_8 = str_ireplace("%to_currency%",$cnv['to_currency'],$activity_8);
			return $activity_8;
		} elseif($type == "28") {
			// manual transaction (+)	
			$activity_8 = "Fund Added by Administration";
			return $activity_8;
		} elseif($type == "29") {
			// manual transaction (-)	
			$activity_8 = "Fund Deducted by Administration";
			return $activity_8;
		} elseif($type == "30") {
			// perfect money api deposit (+)	
			$activity_8 = "Payment From Perfect Money User";
			return $activity_8;
		} elseif($type == "31") {
			// payeer api deposit (+)	
			$activity_8 = "Payment From Payeer User";
			return $activity_8;
		} elseif($type == "32") {
			// Stripe api deposit (+)	
			$activity_8 = "Payment From Stripe User";
			return $activity_8;
		} elseif($type == "33") {
			// Flutterwave api deposit (+)	
			$activity_8 = "Payment From Flutterwave User";
			return $activity_8;
		} elseif($type == "41") {
			$activity_8 = "E-Voucher Creation";
			return $activity_8;
		} elseif($type == "42") {
			$activity_8 = "E-Voucher Termination";
			return $activity_8;
		} elseif($type == "43") {
			$activity_8 = "E-Voucher Redeem";
			return $activity_8;
		} elseif($type == "45") {
			$activity_8 = "Send money via E-Voucher";
			return $activity_8;
		} elseif($type == "44") {
			$activity_8 = "Receive money via E-Voucher";
			return $activity_8;
		} elseif($type == "51") {
			$activity_8 = "Move fund to Fixed Deposit";
			return $activity_8;
		} elseif($type == "52") {
			$activity_8 = "Fixed Deposit Returned";
			return $activity_8;
		} elseif($type == "61") {
			// payment from ...
			if(idinfo($row['u_field_1'],"account_type") == "1") {
				$from = idinfo($row['u_field_1'],"first_name").' '.idinfo($row['u_field_1'],"last_name");
			} else {
				$from = idinfo($row['u_field_1'],"business_name");
			}
			return $lang['activity_1']." ".$from;
		} elseif($type == "62") {
			// payment to ...
			if(idinfo($row['u_field_1'],"account_type") == "1") {
				$from = idinfo($row['u_field_1'],"first_name").' '.idinfo($row['u_field_1'],"last_name");
			} else {
				$from = idinfo($row['u_field_1'],"business_name");
			}
			return $lang['activity_2']." ".$from;
		} elseif($type == "10") {
			// referral earning	
		} else {
			// unknown
		}
	}
}

function DecodeUserActivity_admin($activity_id) {
	global $db, $lang;
	$ActivityQuery = $db->query("SELECT * FROM activity WHERE id='$activity_id'");
	if($ActivityQuery->num_rows>0) {
		$row = $ActivityQuery->fetch_assoc();
		$type = $row['type'];
		if($type == "1") {
			// payment from ...
			if(idinfo($row['u_field_1'],"account_type") == "1") {
				$from = idinfo($row['u_field_1'],"first_name").' '.idinfo($row['u_field_1'],"last_name");
			} else {
				$from = idinfo($row['u_field_1'],"business_name");
			}
			return "Payment from $from";
		} elseif($type == "2") {
			// payment to ...
			if(idinfo($row['u_field_1'],"account_type") == "1") {
				$from = idinfo($row['u_field_1'],"first_name").' '.idinfo($row['u_field_1'],"last_name");
			} else {
				$from = idinfo($row['u_field_1'],"business_name");
			}
			return "Payment to $from";
		} elseif($type == "3") {
			// deposit via ...
			$via = gatewayinfo($row['deposit_via'],"name");
			return "Deposit via $via";
		} elseif($type == "4") {
			// withdrawal to ...
			$to = gatewayinfo($row['withdrawal_via'],"name");
			return "Withdraw via $to";
		} elseif($type == "5") {
			// refund from ...
			if(idinfo($row[u_field_1],"account_type") == "1") {
				$from = idinfo($row[u_field_1],"first_name").' '.idinfo($row[u_field_1],"last_name");
			} else {
				$from = idinfo($row['u_field_1'],"business_name");
			}
			return $lang['activity_5']." ".$from;
		} elseif($type == "6") {
			// refund to ...
			if(idinfo($row[u_field_1],"account_type") == "1") {
				$from = idinfo($row[u_field_1],"first_name").' '.idinfo($row[u_field_1],"last_name");
			} else {
				$from = idinfo($row['u_field_1'],"business_name");
			}
			return $lang['activity_6']." ".$from;
		} elseif($type == "7") {
			// fee reversal
			return $lang['activity_7'];
		} elseif($type == "8") {
			// convert amount from 	
			$QueryConvert = $db->query("SELECT * FROM users_converts WHERE id='$row[u_field_1]'");
			$cnv = $QueryConvert->fetch_assoc();
			$activity_8 = str_ireplace("%from_currency%",$cnv['from_currency'],"Convert from %from_currency% to %to_currency%");
			$activity_8 = str_ireplace("%to_currency%",$cnv['to_currency'],$activity_8);
			return $activity_8;
		} elseif($type == "9") {
			// convert amount to
			$QueryConvert = $db->query("SELECT * FROM users_converts WHERE id='$row[u_field_1]'");
			$cnv = $QueryConvert->fetch_assoc();
			$activity_8 = str_ireplace("%from_currency%",$cnv['from_currency'],"Convert from %from_currency% to %to_currency%");
			$activity_8 = str_ireplace("%to_currency%",$cnv['to_currency'],$activity_8);
			return $activity_8;
		} elseif($type == "28") {
			// manual transaction (+)	
			$activity_8 = "Fund Added by Administration";
			return $activity_8;
		} elseif($type == "29") {
			// manual transaction (-)	
			$activity_8 = "Fund Deducted by Administration";
			return $activity_8;
		} elseif($type == "30") {
			// perfect money api deposit (+)	
			$activity_8 = "Payment From Perfect Money User";
			return $activity_8;
		} elseif($type == "31") {
			// payeer api deposit (+)	
			$activity_8 = "Payment From Payeer User";
			return $activity_8;
		} elseif($type == "32") {
			// Stripe api deposit (+)	
			$activity_8 = "Payment From Stripe User";
			return $activity_8;
		} elseif($type == "33") {
			// Flutterwave api deposit (+)	
			$activity_8 = "Payment From Flutterwave User";
			return $activity_8;
		} elseif($type == "41") {
			$activity_8 = "E-Voucher Creation";
			return $activity_8;
		} elseif($type == "42") {
			$activity_8 = "E-Voucher Termination";
			return $activity_8;
		} elseif($type == "43") {
			$activity_8 = "E-Voucher Redeem";
			return $activity_8;
		} elseif($type == "45") {
			$activity_8 = "Send money via E-Voucher";
			return $activity_8;
		} elseif($type == "44") {
			$activity_8 = "Receive money via E-Voucher";
			return $activity_8;
		} elseif($type == "51") {
			$activity_8 = "Move fund to Fixed Deposit";
			return $activity_8;
		} elseif($type == "52") {
			$activity_8 = "Fixed Deposit Returned";
			return $activity_8;
		} elseif($type == "53") {
			$activity_8 = "Fixed Deposit Cancelled";
			return $activity_8;
		} elseif($type == "61") {
			// payment from ...
			if(idinfo($row['u_field_1'],"account_type") == "1") {
				$from = idinfo($row['u_field_1'],"first_name").' '.idinfo($row['u_field_1'],"last_name");
			} else {
				$from = idinfo($row['u_field_1'],"business_name");
			}
			return $lang['activity_1']." ".$from;
		} elseif($type == "62") {
			// payment to ...
			if(idinfo($row['u_field_1'],"account_type") == "1") {
				$from = idinfo($row['u_field_1'],"first_name").' '.idinfo($row['u_field_1'],"last_name");
			} else {
				$from = idinfo($row['u_field_1'],"business_name");
			}
			return $lang['activity_2']." ".$from;
		} elseif($type == "10") {
			// referral earning	
		} else {
			// unknown
		}
	}
}

function PW_ActivityDate($time) {
	global $db;
	$date = date("d M Y",$time);
	$date_today = date("d M Y");
	$act_time = date("H:i",$time);
	if($date_today == $date) {
		return $act_time;
	} else {
		return $date;
	}
}

function PW_DecodeTXStatus($status) {
	global $db, $lang;
	if($status == "1") {
		return '<span class="badge badge-sm bg-gradient-success">'.$lang['status_payment_1'].'</span>';
	} elseif($status == "2") {
		return '<span class="badge badge-sm bg-gradient-danger">'.$lang['status_payment_2'].'</span>';
	} elseif($status == "3") { 
		return '<span class="badge badge-sm bg-gradient-warning">'.$lang['status_payment_3'].'</span>';
	} elseif($status == "4") {
		return '<span class="badge badge-sm bg-gradient-danger">'.$lang['status_payment_4'].'</span>';
	} elseif($status == "5") {
		return '<span class="badge badge-sm bg-gradient-success">'.$lang['status_payment_5'].'</span>';
	} else {
		return '<span class="badge badge-sm bg-gradient-default">Unknown</span>';
	}
}

function PW_UpdateAdminWallet($amount,$currency) {
	global $db;
	$time = time();
	$CheckWallet = $db->query("SELECT * FROM admin_earnings WHERE currency='$currency'");
	if($CheckWallet->num_rows>0) {
		$row = $CheckWallet->fetch_assoc();
		$update = $db->query("UPDATE admin_earnings SET amount=amount+$amount,updated='$time' WHERE currency='$currency'"); 
	} else {
		$insert = $db->query("INSERT admin_earnings (amount,currency,created,updated) VALUES ('$amount','$currency','$time','$time')");
	}
}

function get_wallet_balance($uid,$currency) {
	global $db;
	$query = $db->query("SELECT * FROM users_wallets WHERE uid='$uid' and currency='$currency'");
	if($query->num_rows>0) {
		$row = $query->fetch_assoc();
		$balance = $row['amount'];
		$CheckDisputes = $db->query("SELECT * FROM disputes WHERE recipient='$uid' and currency='$currency' and status='1' or recipient='$uid' and currency='$currency' and status='2'");
		if($CheckDisputes->num_rows>0) {
			while($dispute = $CheckDisputes->fetch_assoc()) {
				$balance = $balance - $dispute['amount'];
			}
		}
		return $balance;
	} else {
		return '0';
	}
}

function get_user_balance($uid) {
	global $db, $settings;
	$query = $db->query("SELECT * FROM users_wallets WHERE uid='$uid' and currency='$settings[default_currency]'");
	if($query->num_rows>0) {
		$row = $query->fetch_assoc();
		$balance = $row['amount'];
		$CheckDisputes = $db->query("SELECT * FROM disputes WHERE recipient='$uid' and currency='$settings[default_currency]' and status='1' or recipient='$uid' and currency='$settings[default_currency]' and status='2'");
		if($CheckDisputes->num_rows>0) {
			while($dispute = $CheckDisputes->fetch_assoc()) {
				$balance = $balance - $dispute['amount'];
			}
		}
		$bal = array();
		$bal['balance'] = $balance." <sup style='font-size:30px;'>".$settings['default_currency']."</sup>";
		if($balance < 0) { 
			$bal['class'] = 'danger';
		} elseif($balance > 0) {
			$bal['class'] = 'success';
		} else {
			$bal['class'] = 'muted';
		}
	} else {
		$bal = array();
		$bal['balance'] = "0 ".$settings['default_currency'];
		$bal['class'] = 'muted';
	}
	return $bal;
}

function currencyConvertor($amount,$from_Currency,$to_Currency) {
	return PW_currencyConvertor($amount,$from_Currency,$to_Currency);
}


/**
 * Function to see if a string is a UK postcode or not. The postcode is also 
 * formatted so it contains no strings. Full or partial postcodes can be used.
 * 
 * @param string $toCheck
 * @return boolean 
 */
function postcode_check(&$toCheck) {
	// Permitted letters depend upon their position in the postcode.
	$alpha1 = "[abcdefghijklmnoprstuwyz]";                          // Character 1
	$alpha2 = "[abcdefghklmnopqrstuvwxy]";                          // Character 2
	$alpha3 = "[abcdefghjkstuw]";                                   // Character 3
	$alpha4 = "[abehmnprvwxy]";                                     // Character 4
	$alpha5 = "[abdefghjlnpqrstuwxyz]";                             // Character 5
   
	// Expression for postcodes: AN NAA, ANN NAA, AAN NAA, and AANN NAA with a space
	// Or AN, ANN, AAN, AANN with no whitespace
	$pcexp[0] = '^(' . $alpha1 . '{1}' . $alpha2 . '{0,1}[0-9]{1,2})([[:space:]]{0,})([0-9]{1}' . $alpha5 . '{2})?$';
   
	// Expression for postcodes: ANA NAA
	// Or ANA with no whitespace
	$pcexp[1] = '^(' . $alpha1 . '{1}[0-9]{1}' . $alpha3 . '{1})([[:space:]]{0,})([0-9]{1}' . $alpha5 . '{2})?$';
   
	// Expression for postcodes: AANA NAA
	// Or AANA With no whitespace
	$pcexp[2] = '^(' . $alpha1 . '{1}' . $alpha2 . '[0-9]{1}' . $alpha4 . ')([[:space:]]{0,})([0-9]{1}' . $alpha5 . '{2})?$';
   
	// Exception for the special postcode GIR 0AA
	// Or just GIR
	$pcexp[3] = '^(gir)([[:space:]]{0,})?(0aa)?$';
   
	// Standard BFPO numbers
	$pcexp[4] = '^(bfpo)([[:space:]]{0,})([0-9]{1,4})$';
   
	// c/o BFPO numbers
	$pcexp[5] = '^(bfpo)([[:space:]]{0,})(c\/o([[:space:]]{0,})[0-9]{1,3})$';
   
	// Overseas Territories
	$pcexp[6] = '^([a-z]{4})([[:space:]]{0,})(1zz)$';
   
	// Anquilla
	$pcexp[7] = '^(ai\-2640)$';
   
	// Load up the string to check, converting into lowercase
	$postcode = strtolower($toCheck);
   
	// Assume we are not going to find a valid postcode
	$valid = false;
   
	// Check the string against the six types of postcodes
	foreach ($pcexp as $regexp) {
	  if (preg_match('/' . $regexp . '/i', $postcode, $matches)) {
   
		// Load new postcode back into the form element
		$postcode = strtoupper($matches[1]);
		if (isset($matches[3])) {
		  $postcode .= ' ' . strtoupper($matches[3]);
		}
   
		// Take account of the special BFPO c/o format
		$postcode = preg_replace('/C\/O/', 'c/o ', $postcode);
   
		// Remember that we have found that the code is valid and break from loop
		$valid = true;
		break;
	  }
	}
   
	// Return with the reformatted valid postcode in uppercase if the postcode was 
	// valid
	if ($valid) {
	  $toCheck = $postcode;
	  return true;
	} else {
	  return false;
	}
}
?>