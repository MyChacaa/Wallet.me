<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
$hash = protect($_GET['hash']);
if(empty($hash)) {
    $hash = strtoupper(randomHash(10));
    $time = time();
    $merchant_account = protect($_POST['merchant_account']);
    $item_number = protect($_POST['item_number']);
    $item_name = protect($_POST['item_name']);
    $item_price = protect($_POST['item_price']);
    $item_currency = protect($_POST['item_currency']);
    $return_success = protect($_POST['return_success']);
    $return_fail = protect($_POST['return_fail']);
    $return_cancel = protect($_POST['return_cancel']);
    $merchant_id = PW_GetUserID($merchant_account);
    $biz_name = idinfo($merchant_id,"business_name");
    $query_curr = $db->query("SELECT * FROM currency WHERE currency='$item_currency' and status='1'");
    
    //FEE TAB
    $per_fee = ($item_price * $settings['merchant_percentage']) / 100;
    if ($settings['default_currency'] !== "$item_currency") {
        $fix_fee = $settings['merchant_fixed'];
        $fix_fee = PW_currencyConvertor($fix_fee,$settings[default_currency],$item_currency);
    } else {
        $fix_fee = $settings['merchant_fixed'];
    }
    $fee = $per_fee + $fix_fee;
    
    if($fee > $item_price && idinfo($merchant_id,"business_who_pay_fee") == "1") {
        $results = error("Fees was higher than amount passes.");
    } else {
    if($merchant_id==false) {
        $results = error("Merchant does not exists.");
    } elseif($m["merchants"] !== "1") {
        $results = error("Merchants system was temprory down.");
    } elseif($query_curr->num_rows==0) {
        $results = error("Currency $item_currency is not supported.");
    } elseif(idinfo($merchant_id,"account_type") !== "2") {
        $results = error("$biz_name cannot accept payments. Only Business accounts can accept payments.");
    } elseif(idinfo($merchant_id,"business_status") !== "1") {
        $results = error("$biz_name cannot accept payments. Business application was not approved yet.");
    } elseif(empty($item_number) or empty($item_name) or empty($item_price) or empty($item_currency) or empty($return_success) or empty($return_fail) or empty($return_cancel)) {
        $devlink = $settings['url']."index.php?a=merchant";
        $devlink = '<a href="'.$devlink.'">'.$devlink.'</a>';
        $results = error("Some data from HTML form is missing. Please read merchant integration page $devlink");
    } else {
        $insert = $db->query("INSERT payments (hash,merchant_account,item_number,item_name,item_price,item_currency,return_success,return_fail,return_cancel,payment_time,payment_status) VALUES ('$hash','$merchant_account','$item_number','$item_name','$item_price','$item_currency','$return_success','$return_fail','$return_cancel','$time','1')");
		$_SESSION['bb_payment_hash'] = $hash;
	    $_SESSION['bb_payment_time'] = $time+600;
		$redirect = $settings['url']."index.php?a=payment&hash=".$hash;
		header("Location: $redirect");
    }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Error - <?php echo filter_var($settings['name']); ?></title>
    <meta name="description" content="<?php echo filter_var($settings['description']); ?>">
    <meta name="keywords" content="<?php echo filter_var($settings['keywords']); ?>">
    
    <script src="https://use.fontawesome.com/32efc5ddb7.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo filter_var($settings['url']); ?>assets/front/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo filter_var($settings['url']); ?>assets/front/css/custom_styles.css">
	<script src="<?php echo filter_var($settings['url']); ?>assets/front/js/jquery.min.js"></script>
	<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
	<?php if($settings['favicon']) { ?>
		<link rel="icon" type="image/png" href="<?= $settings['url'].$settings['favicon'] ?>">
	<?php } else { ?>
		<link rel="icon" type="image/png" href="<?= $settings['url'] ?>assets/logo/favicon.png">
	<?php } ?>
    </head>
    <body style="background: #F1F0F5">
        <script type="text/javascript">
        $(document).ready(function(){
        });
        </script>
        
        <section class="payContainer flex ai jc">
        	<div class="payBox mb20" style="padding: 20px 40px">
        		<div class="row">
        			<div class="col-md-4">
        				<?php if($settings['logo']) { ?>
        					<img src="<?= $settings['url'].$settings['logo'] ?>" width="120px">
        				<?php } else { ?>
        					<img src="<?= $settings['url'] ?>assets/logo/logo_red.png" width="120px">
        				<?php } ?>
        			</div>
        		</div>
            </div>
            <div class="payBox">
                <div class="flex jcb w100">
        			<h2 class="fs24 fw3" style="color: var(--red">Payment Error </h2>
        		</div>
        		<br>
        		<?php echo filter_var($results); ?>
                <br>
            </div>
        </section>
    </body>
</html>
    <?php
} else {
$hash = protect($_GET['hash']);
$query = $db->query("SELECT * FROM payments WHERE hash='$hash'");
if($query->num_rows==0) {
    header("Location: $settings[url]");
}
$row = $query->fetch_assoc();
$merchant_id = PW_GetUserID($row['merchant_account']);
$results = '';
$hide_info = '0';
$FormBTN = protect($_POST['action']);
if($FormBTN == "pay") {
    if($row['payment_status'] == "1") {
        $item_price = $row['item_price'];
        $amount = number_format($item_price, 2, '.', '');
        $currency = $row['item_currency'];
        
        //FEE TAB
        $per_fee = ($amount * $settings['merchant_percentage']) / 100;
        if ($settings['default_currency'] !== "$currency") {
                
            $fix_fee = $settings['merchant_fixed'];
            $fix_fee = PW_currencyConvertor($fix_fee,$settings[default_currency],$currency);
            
        } else {
            $fix_fee = $settings['merchant_fixed'];
        }
        
        $fee = $per_fee + $fix_fee;
        
        $user_balance = get_wallet_balance($_SESSION['pw_uid'],$row['item_currency']);
        
        
        if (idinfo($merchant_id,"business_who_pay_fee") == "2") {
            $verify = $item_price + $fee;
        } else {
            $verify = $item_price;
        }
        
        if($user_balance>$verify) {
            
            
            $recipient_id = PW_GetUserID($row['merchant_account']);
            $txid = strtoupper(randomHash(10));
            $time = time();
            
            
            
            
            if (idinfo($merchant_id,"business_who_pay_fee") == "1") {
                
                $amount_with_fee = $amount - $fee;
                PW_UpdateUserWallet($_SESSION['pw_uid'],$amount,$currency,2);
                PW_UpdateUserWallet($recipient_id,$amount_with_fee,$currency,1);
                
                $create_transaction = $db->query("INSERT transactions (txid,type,sender,recipient,description,amount,currency,fee,status,created,item_id,item_name) 
                VALUES ('$txid','1','$_SESSION[pw_uid]','$recipient_id','$description','$amount','$currency','$fee','1','$time','$row[item_number]','$row[item_name]')");
                
                $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                VALUES ('$txid','2','$_SESSION[pw_uid]','$recipient_id','$amount','$currency','1','$time')");
                
                $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                VALUES ('$txid','1','$recipient_id','$_SESSION[pw_uid]','$amount_with_fee','$currency','1','$time')");
                
            }
            
            if (idinfo($merchant_id,"business_who_pay_fee") == "2") {
                
                $amount_with_fee = $amount + $fee;
                
                PW_UpdateUserWallet($_SESSION['pw_uid'],$amount_with_fee,$currency,2);
                PW_UpdateUserWallet($recipient_id,$amount,$currency,1);
                
                $create_transaction = $db->query("INSERT transactions (txid,type,sender,recipient,description,amount,currency,fee,status,created,item_id,item_name) 
                VALUES ('$txid','1','$_SESSION[pw_uid]','$recipient_id','$description','$amount_with_fee','$currency','$fee','1','$time','$row[item_number]','$row[item_name]')");
                
                $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                VALUES ('$txid','2','$_SESSION[pw_uid]','$recipient_id','$amount_with_fee','$currency','1','$time')");
                
                $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                VALUES ('$txid','1','$recipient_id','$_SESSION[pw_uid]','$amount','$currency','1','$time')");
                
                
                
            }
           
            
            
            
            
            
            
            
            $update = $db->query("UPDATE payments SET payment_status='4',txid='$txid' WHERE id='$row[id]'");
            $row['payment_status'] = '4';
            $row['txid'] = $txid;
            PW_UpdateAdminWallet($fee,$currency);
            $insert_admin_log = $db->query("INSERT admin_logs (type,time,u_field_1,u_field_2,u_field_3) VALUES ('5','$time','$fee','$currency','$txid')");
            $email = idinfo($merchant_id,"email");
            PW_EmailSys_PaymentNotification($email,$amount,$currency,$description,$txid);
            $results = '<form id="PW_Payment_Success_Form" method="POST" action="'.$row['return_success'].'">
                    <input type="hidden" name="merchant_account" value="'.$row['merchant_account'].'">
                    <input type="hidden" name="item_number" value="'.$row['item_number'].'">
                    <input type="hidden" name="item_name" value="'.$row['item_name'].'">
                    <input type="hidden" name="item_price" value="'.$row['item_price'].'">
                    <input type="hidden" name="item_currency" value="'.$row['item_currency'].'">
                    <input type="hidden" name="txid" value="'.$row['txid'].'">
                    <input type="hidden" name="payment_time " value="'.$time.'">
                    <input type="hidden" name="payee_account " value="'.idinfo($_SESSION['pw_uid'],"email").'">
                </form><script src="'.$settings['url'].'assets/js/jquery-1.12.4.min.js"></script>';
            $results .= " <script type='text/javascript'>
                $(document).ready(function() {
                    $('#PW_Payment_Success_Form')[0].submit();
                }); 
                </script>";
            $results .= success("Payment was successful. Redirecting to merchant website, please wait...");
        } else {
            $update = $db->query("UPDATE payments SET payment_status='3' WHERE id='$row[id]'");
            $row['payment_status'] = '3';
            $results = '<meta http-equiv="refresh" content="0;URL='.$row[return_fail].'" />';
            $results .= error("Payment was failed. You do not have enough funds. Redirecting to merchant website...");
        }
    }
} 
if($FormBTN == "cancel") {
    if($row['payment_status'] == "1") {
        $update = $db->query("UPDATE payments SET payment_status='2' WHERE id='$row[id]'");
        $row['payment_status'] = '2';
        $results = '<meta http-equiv="refresh" content="0;URL='.$row[return_cancel].'" />';
        $results .= success("Payment was canceled. Redirecting to merchant website...");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Payment - <?php echo filter_var($settings['name']); ?></title>
        <meta name="description" content="<?php echo filter_var($settings['description']); ?>">
        <meta name="keywords" content="<?php echo filter_var($settings['keywords']); ?>">
    	<meta name="viewport" content="width=1100px">
    	<link href="<?php echo $settings['url']; ?>assets/payment_page/css/api.css" rel="stylesheet" type="text/css">
    	<link href="https://fonts.googleapis.com/css?family=Quicksand:400,500,700" rel="stylesheet">
    	<?php if($settings['favicon']) { ?>
    		<link rel="icon" type="image/png" href="<?= $settings['url'].$settings['favicon'] ?>">
    	<?php } else { ?>
    		<link rel="icon" type="image/png" href="<?= $settings['url'] ?>assets/logo/favicon.png">
    	<?php } ?>
    </head>
    <body>
        <div class="header-ati-sli">
        	<div class="container">
        		<div id="logo-area">
        		    <?php if($settings['logo']) { ?>
    					<img src="<?= $settings['url'].$settings['logo'] ?>">
    				<?php } else { ?>
    					<img src="<?= $settings['url'] ?>assets/logo/logo_red.png">
    				<?php } ?>
        		</div>
        		<div id="right-links">
        			<p style="float:right;margin-top:18px;">&nbsp;&nbsp;&nbsp;
        		        <?php 
						    if(checkSession()) {
						        if(idinfo($_SESSION['pw_uid'],"account_type") == "1") { 
						            echo idinfo($_SESSION['pw_uid'],"first_name"); 
						        } else { 
						            echo idinfo($_SESSION['pw_uid'],"business_name");
						        }
						    } else {
						    $_SESSION['pw_payorder_url'] = $settings['url']."payment&hash=".$row['hash']; 
						    ?>
						    <a href="<?php echo filter_var($settings['url']); ?>index.php?a=login">Login</a>
						    <?php 
						    } 
				        ?>
        		    </p>
        		    <img src="<?= $settings['url']; ?>assets/front/img/userImg.png" style="margin-top:15px;width:22px;float:right;">
        		</div>
        	</div>
        </div>
        <div class="content-ati-sli">
        	<div class="container">
		        <div id="shop-detail">
            		<h1>Invoice</h1>
            		<div id="space"></div>
            		<p>You just got the invoice from <a target="_blank" href="<?php echo idinfo($merchant_id,"business_website"); ?>"><?php echo idinfo($merchant_id,"business_name"); ?></a></p>
                </div>
                <div id="shop-main2">
                    <div id="shop-main">
            	        <div id="shop-main-top">
            	            <div id="shop-chil">
            					<h3>Recipient : </h3>
            					<h4><?php echo idinfo($merchant_id,"business_name"); ?></h4>
            				</div>
            		        <div id="shop-chil">
            					<h3>Item ID / Item Name : </h3>
            					<h4><?php echo $row['item_number']; ?>&nbsp; - &nbsp;<?php echo $row['item_name']; ?></h4>
            				</div>
            		        <div id="shop-chil">
                				<h3>Payment ID : </h3>
                				<h4><?php echo $row['hash']; ?></h4>
                			</div>
            	        </div>
            		    <div id="shop-main-bottom">
            	            <h2>Amount : </h2>
            			    <h1><?php echo filter_var($row['item_price']." ".$row['item_currency']); ?></h1>
            	        </div>
                    </div>	
            	</div>
            	<?php if($results) { echo filter_var($results); } ?>
	        	<?php if($row['txid']) { ?>
	        	    <div id="cont">
            		    <p>Transaction ID: <?php echo filter_var($row['txid']); ?></p>
            		</div>
            	<?php } ?>
	        	<div id="shop-ot">
            		<?php if(checkSession()) {
                    if($_SESSION['pw_payorder_url']) {
                        $_SESSION['pw_payorder_url'] = false;
                     }
                    ?>
                    <?php if($_SESSION['pw_uid'] !== $merchant_id) { ?>
                    <?php if($row['payment_status'] == "1") { ?>
                    
                    <div id="cont">
            		    <p>Pay with <?php echo $settings['name']; ?> Account</p>
            		    <form action="" method="POST">
            			    <div class="row">
            			        <div class="col">
            			            <button type="submit" name="action" value="pay"><i class="fas fa-lock"></i>&nbsp;&nbsp;Pay Now <?php echo filter_var($row['item_price']." ".$row['item_currency']); ?></button>
            			        </div>
            			    </div>
            		    </form>
            		</div>
            		<br>
                    
                    <?php }?>
                    <?php } ?>
                    <?php } else {
                    $_SESSION['pw_payorder_url'] = $settings['url']."payment&hash=".$row['hash'];
                    ?>
                    <?php if($row['payment_status'] == "1") { ?>
                    <div id="cont">
            		    <p>Login to Pay with <?php echo $settings['name']; ?> Account</p>
            		    <a href="<?php echo filter_var($settings['url']); ?>index.php?a=login"><i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;Login Now</a>
            		</div>
    		        <br>
    		        <?php } ?>
                    <?php } ?>
                    
                    
                    <?php if ($row['payment_status'] == "1") { ?>
                    <div class="alert info">
            		    <strong><i class="fa fa-info-circle"></i></strong> Please choose the 3rd party payment option to pay this order (No Login/Registration Required):
            		</div>
            		<?php
            		    $query_1 = $db->query("SELECT * FROM merchant_gateways WHERE name='Perfect Money'");
                        $row_1 = $query_1->fetch_assoc();
            		?>
            		<?php if ($row_1['status'] == "1") { ?>
            		<a href="<?php echo $settings['url'] ?>payments/check_payment.php?a=PerfectMoney&b=<?php echo $row['hash']; ?>">
                        <div id="shop-ot-ch">
                			<div id="shop-ot-img">
                			    <img src="<?= $settings['url']; ?>assets/payment_page/img/pm.png">
                			</div>
                			<div id="shop-t">
                			    <center><h3>Perfect Money</h3></center>
                			</div>
                	    </div>
            		</a>
            		<?php } ?>
            		<?php
                        $query_2 = $db->query("SELECT * FROM merchant_gateways WHERE name='Payeer'");
                        $row_2 = $query_2->fetch_assoc();
                    ?>
                    <?php if ($row_2['status'] == "1") { ?>
            		<a href="<?php echo $settings['url'] ?>payments/check_payment.php?a=Payeer&b=<?php echo $row['hash']; ?>">
                		<div id="shop-ot-ch">
                			<div id="shop-ot-img">
                			    <img src="<?= $settings['url']; ?>assets/payment_page/img/py.png">
                			</div>
                			<div id="shop-t">
                			    <center><h3>Payeer</h3></center>
                			</div>
                	    </div>
                    </a>
                    <?php } ?>
                    <?php
                        $query_3 = $db->query("SELECT * FROM merchant_gateways WHERE name='Stripe'");
                        $row_3 = $query_3->fetch_assoc();
                    ?>
                    <?php if ($row_3['status'] == "1") { ?>
                    <a href="<?= $settings['url'] ?>payments/check_payment.php?a=Stripe&b=<?= $row['hash']; ?>">
                		<div id="shop-ot-ch">
                			<div id="shop-ot-img">
                			    <img src="<?= $settings['url']; ?>assets/payment_page/img/st.png">
                			</div>
                			<div id="shop-t">
                			    <center><h3>Stripe</h3></center>
                			</div>
                	    </div>
                    </a>
                    <?php } ?>
                    <?php
                        $query_4 = $db->query("SELECT * FROM merchant_gateways WHERE name='Flutterwave'");
                        $row_4 = $query_4->fetch_assoc();
                    ?>
                    <?php if ($row_4['status'] == "1") { ?>
                    <a href="<?= $settings['url'] ?>payments/check_payment.php?a=Flutterwave&b=<?= $row['hash']; ?>">
                		<div id="shop-ot-ch">
                			<div id="shop-ot-img">
                			    <img src="<?= $settings['url']; ?>assets/payment_page/img/fw.png">
                			</div>
                			<div id="shop-t">
                			    <center><h3>Flutterwave</h3></center>
                			</div>
                	    </div>
                    </a>
                    <?php } ?>
                    
                    <?php } ?>
                    
                </div>
                <?php if($row['payment_status'] == "1") { ?>
                <?php if($_SESSION['pw_uid'] == $merchant_id) { ?>
                <div class="alert info">
                  <span class="closebtn">&times;</span>  
                  <strong>Info!</strong> Pay via <?php echo $settings['name']; ?> account is not available due to same receiver account...
                </div>
                <?php } ?>
                <?php } ?>
                <?php if($row['payment_status'] == "3") { ?>
                <div class="alert">
                  <span class="closebtn">&times;</span>  
                  <strong>Failed!</strong> Payment was failed...
                </div>
                <?php } ?>
                <?php if($row['payment_status'] == "2") { ?>
                <div class="alert">
                  <span class="closebtn">&times;</span>  
                  <strong>Cancelled!</strong> Payment was cancelled...
                </div>
                <?php } ?>
                <?php if($row['payment_status'] == "4") { ?>
                <div class="alert success">
                  <span class="closebtn">&times;</span>  
                  <strong>Success!</strong> Payment was Successful...
                </div>
                <?php } ?>
                <?php if($row['payment_status'] == "1") { ?>
                <div id="cont">
                    <p>Not willing to pay? </p>
        		    <form action="" method="POST">
        			    <div class="row">
        			        <div class="col">
        			            <button type="submit" name="action" value="cancel"><i class="fas fa-ban"></i>&nbsp;&nbsp;Cancel Payment</button>
        			        </div>
        			    </div>
        		    </form>
        		</div>
		        <?php } ?>
		    </div>
	    </div>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
    </body>
</html>
<?php
}
?>