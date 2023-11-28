<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
if(!checkSession()) {
    $redirect = $settings['url']."index.php?a=login";
    header("Location: $redirect");
}
$evoucher_settingsQuery = $db->query("SELECT * FROM evoucher_settings ORDER BY id DESC LIMIT 1");
$evoucher_settings = $evoucher_settingsQuery->fetch_assoc();
if ($evoucher_settings["status"] !== "1") {
    $redirect = $settings['url']."index.php?a=account&b=summary";
    header("Location: $redirect");
}
?>
<div class="container-fluid py-4">                
    <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>E Vouchers <small style="float:right;">Use for sharing, send money and gift to your friends.</small></h6>
              <div class="row">
                  <div class="col-md">
                      <form method="POST" action="">
                          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Create e-Voucher</button>
                          <button type="submit" name="redeem" value="redeem" class="btn btn-warning">Redeem e-Voucher</button>
                          <button type="submit" name="send" value="send" class="btn btn-info">Send Money</button>
                      </form>
                      
                        <?php
                            if(isset($_POST['redeem'])) {
                                $FormBTN = protect($_POST['redeem']);
                                if ($FormBTN == "redeem") {
                                 $_SESSION['redeem'] = "1";
                                }
                            }
                            if(isset($_POST['send'])) {
                                $FormBTN = protect($_POST['send']);
                                if ($FormBTN == "send") {
                                 $_SESSION['send'] = "1";
                                }
                            }
                        ?>
                        <?php 
                            if (!empty($_SESSION['redeem_success'])) {
                                echo $_SESSION['redeem_success'];
                                $_SESSION['redeem_success'] = "";
                                $_SESSION['redeem'] = "";
                            }
                        ?>
                        <?php 
                            if (!empty($_SESSION['send_success'])) {
                                echo $_SESSION['send_success'];
                                $_SESSION['send_success'] = "";
                                $_SESSION['send'] = "";
                            }
                        ?>
                        <?php 
						if (isset($_SESSION['send'])){
							$_SESSION['send'] = $_SESSION['send'];
						} else {
							$_SESSION['send'] = "";
						}
						if ($_SESSION['send'] == "1") { ?>
                        <!-- Send Money Code -->
                        <div class="card bg-secondary text-white" style="background-image: url('<?= $settings['url']; ?>assets/wallet/img/ivancik.jpg');">
                            <div class="card-body">
                                <?php
                                	if(isset($_POST['send_f'])) {
                                        $FormBTN = protect($_POST['send_f']);
                                        if($FormBTN == "send_f") {
                                            $amount = protect($_POST['amount']);
                                            $amount = number_format($amount, 2, '.', '');
                                            $currency = protect($_POST['currency']);
                                            $email = protect($_POST['email']);
                                            $description = protect($_POST['description']);
                                            $number = protect($_POST['number']);
                                            $activation = protect($_POST['activation']);
                                            
                                            $redeemQuery = $db->query("SELECT * FROM evoucher Where number='$number'");
                                            $redeem = $redeemQuery->fetch_assoc();
                                            $verify_key = $redeem['activation'];
                                            
                                            $sha1_activation = sha1($verify_key);
                                            $sha2_activation = sha1($activation);
                                            if(empty($amount)) {
                                                echo error($lang['error_6']);
                                            } elseif(empty($currency) or empty($email) or empty($currency)) {
                                                echo error("Some fields are empty.");
                                            } elseif(!is_numeric($amount)) {
                                                echo error($lang['error_7']);
                                            } elseif($amount<0) {
                                                echo error($lang['error_7']);
                                            } elseif($amount == "0") {
                                                echo error($lang['error_7']);
                                            } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $amount)) {
                                                 echo error("Invalid Amount");
                                            } elseif (empty($number) or empty($activation)) {
                                                echo error("Some fields are empty.");
                                            } elseif ($redeemQuery->num_rows == 0) {
                                                echo error("No any E-Voucher with this number.");
                                            } elseif ($redeem['status']  !== "1") {
                                                echo error("E-Voucher was not active to use.");
                                            } elseif ($redeem['amount']  == "0") {
                                                echo error("E-Voucher have insufficent balance.");
                                            } elseif ($redeem['amount']  < "0") {
                                                echo error("E-Voucher have insufficent balance.");
                                            } elseif ($verify_key !== $activation) {
                                                echo error("Activation key not matched.");
                                            } elseif ($sha1_activation !== $sha2_activation) {
                                                echo error("Activation key not matched.");
                                            } elseif(idinfo($_SESSION['pw_uid'],"email") == $email) {
                                                echo error($lang['error_9']);
                                            } elseif(PW_CheckUser($email)==false) {
                                                echo error($lang['error_11']);
                                            } elseif($redeem['amount'] < $amount) {
                                                echo error("Insufficent amount in E-Voucher.");
                                            } else {
                                                $recipient_id = PW_GetUserID($email);
                                                $txid = strtoupper(randomHash(10));
                                                $time = time();
                                                $description = "E-Voucher Money.";
                                                $balance = $redeem['amount'];
                                                $currency = $redeem['currency'];
                                                    
                                                PW_UpdateUserWallet($recipient_id,$amount,$currency,1); //Receiver will be debited by
                                                
                                                $update_balance = $balance - $amount;
                                                $update = $db->query("UPDATE evoucher SET amount='$update_balance' WHERE id='$redeem[id]'");
                                                
                                                $create_transaction = $db->query("INSERT transactions (txid,type,sender,recipient,description,amount,currency,status,created) 
                                                VALUES ('$txid','44','$_SESSION[pw_uid]','$recipient_id','$description','$amount','$currency','1','$time')");
                                                
                                                $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                                                VALUES ('$txid','45','$_SESSION[pw_uid]','$recipient_id','$amount','$currency','1','$time')");
                                                
                                                $insert_activity = $db->query("INSERT activity (txid,type,uid,u_field_1,amount,currency,status,created) 
                                                VALUES ('$txid','44','$recipient_id','$_SESSION[pw_uid]','$amount','$currency','1','$time')");
                                                
                                                $success_7 = str_ireplace("%amount%",$amount,$lang['success_7']);
                                                $success_7 = str_ireplace("%currency%",$currency,$success_7);
                                                $success_7 = str_ireplace("%email%",$email,$success_7);
                                                
                                                PW_EmailSys_PaymentNotification($email,$amount,$currency,$description,$txid);
                                                
                                                $_SESSION['send'] = "0";
                                                $_SESSION['send_success'] = success($success_7);
                                                header("Refresh:0");
                                                
                                            }
             
                                        }
                                	}
                                ?>
                                <form class="user-connected-from user-login-form" action="" method="POST">
                                    <div class="row">
                                        <div class="col-md">
                                            <div class="input-group input-pw-amount">
                                                <input type="text" class="form-control" name="amount" placeholder="0.00" aria-label="Amount (to the nearest dollar)">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <select class="form-control" name="currency">
                                                            <?php
                                            				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='1'");
                                        		            while($curr = $curr_Query->fetch_assoc()) {
                                            						
                                                                echo '<option value="'.$curr['currency'].'">'.$curr['currency'].'</option>';
                                                            }
                                                            ?>
                                                            <?php
                                            				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='2'");
                                        		            while($curr = $curr_Query->fetch_assoc()) {
                                            						
                                                                echo '<option value="'.$curr['currency'].'">'.$curr['currency'].'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="number" placeholder="Enter E Vocher Number">
                                            </div>
                                        </div>
                                    </div>
                                    <p></p>
                                    <div class="row">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <input type="email" class="form-control" id="exampleInputEmail1" name="email" placeholder="<?= $lang['placeholder_5']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="activation" placeholder="Activation Key">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="form-group">
                                        <textarea class="form-control" name="description" rows="2" placeholder="<?= $lang['placeholder_4']; ?>"></textarea>
                                    </div>
                                        
                                    <button type="submit" name="send_f" value="send_f" class="btn btn-primary"><?= $lang['btn_12']; ?> Money</button>
                                </form>
                            </div>
                        </div>
                        <br>
                        <!-- Send Money Code -->
                        <?php } ?>
                        
                        <?php 
						if (isset($_SESSION['redeem'])){
							$_SESSION['redeem'] = $_SESSION['redeem'];
						} else {
							$_SESSION['redeem'] = "";
						}
						if ($_SESSION['redeem'] == "1") { ?>
                        <!-- Redeem Form Code -->
                        <div class="card bg-secondary text-white" style="background-image: url('<?= $settings['url']; ?>assets/wallet/img/ivancik.jpg');">
                            <div class="card-body">
                                <?php
                                    if(isset($_POST['redeem_done'])) {
                                        $FormBTN = protect($_POST['redeem_done']);
                                        if ($FormBTN == "redeem_done") {
                                            $number = protect($_POST['number']);
                                            $activation = protect($_POST['activation']);
                                            
                                            $redeemQuery = $db->query("SELECT * FROM evoucher Where number='$number'");
                                            $redeem = $redeemQuery->fetch_assoc();
                                            $verify_key = $redeem['activation'];
                                            $sha1_activation = sha1($verify_key);
                                            $sha2_activation = sha1($activation);
                                            if (empty($number) or empty($activation)) {
                                                echo error("Some fields are empty.");
                                            } elseif ($redeemQuery->num_rows == 0) {
                                                echo error("No any E-Voucher with this number.");
                                            } elseif ($redeem['status']  !== "1") {
                                                echo error("E-Voucher was not active to use.");
                                            } elseif ($redeem['amount']  == "0") {
                                                echo error("E-Voucher have insufficent balance.");
                                            } elseif ($redeem['amount']  < "0") {
                                                echo error("E-Voucher have insufficent balance.");
                                            } elseif ($sha1_activation !== $sha2_activation) {
                                                echo error("Activation key not matched.");
                                            } else {
                                                if ($verify_key !== $activation) {
                                                    echo error("Activation key not matched.");
                                                } else {
                                                    
                                                    $txid = strtoupper(randomHash(15));
                                                    $time = time();
                                                    $description = "E-Voucher Redeem.";
                                                    $balance = $redeem['amount'];
                                                    $currency = $redeem['currency'];
                                                    
                                                    PW_UpdateUserWallet($_SESSION['pw_uid'],$balance,$currency,1);
                                                    
                                                    // Create Transaction
                                                    $create_transaction = $db->query("INSERT transactions (txid,type,sender,description,amount,currency,status,created) 
                                                    VALUES ('$txid','43','$_SESSION[pw_uid]','$description','$balance','$currency','1','$time')");
                                                    
                                                    // Create Activity
                                                    $insert_activity = $db->query("INSERT activity (txid,type,uid,amount,currency,status,created) 
                                                    VALUES ('$txid','43','$_SESSION[pw_uid]','$balance','$currency','1','$time')");
                                                    
                                                    
                                                    $update = $db->query("UPDATE evoucher SET status='3',amount='0.00' WHERE id='$redeem[id]'");
                                                    
                                                    $_SESSION['redeem'] = "0";
                                                    $_SESSION['redeem_success'] = success("You have redeemed E-Voucher of $currency $balance has been debited to your account.");
                                                    header("Refresh:0");
                                                }
                                                
                                            }
                                        
                                        }
                                    }
                                ?>
                                <form class="user-connected-from user-login-form" action="" method="POST">
                                    <div class="row">
                                        <div class="col-md">
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="number" placeholder="Enter E Vocher Number">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="activation" placeholder="Activation Key">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="input-group">
                                                <button type="submit" name="redeem_done" value="redeem_done" class="btn btn-info" style="float:right;">Redeem E-Voucher</button>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                </form>
                            </div>
                        </div>
                        <br>
                        <!-- Redeem Form Code -->
                        <?php } ?>
                  </div>
              </div>
            <?php
			if(isset($_POST['delete'])) {
            $FormBTN = protect($_POST['delete']);
                $time = time();
                $description = "E-Voucher Terminated.";
                $txid = strtoupper(randomHash(15));
                $evoucher_settingsQuery_d = $db->query("SELECT * FROM evoucher WHERE id='$FormBTN'");
                $evoucher_settings_d = $evoucher_settingsQuery_d->fetch_assoc();
                                 
                // Create Transaction
                $create_transaction = $db->query("INSERT transactions (txid,type,sender,description,amount,currency,status,created) 
                VALUES ('$txid','42','$_SESSION[pw_uid]','$description','$evoucher_settings_d[amount]','$evoucher_settings_d[currency]','1','$time')");
                
                // Create Activity
                $insert_activity = $db->query("INSERT activity (txid,type,uid,amount,currency,status,created) 
                VALUES ('$txid','42','$_SESSION[pw_uid]','$evoucher_settings_d[amount]','$evoucher_settings_d[currency]','1','$time')");
                
                // Add Remaing fund In wallet
                PW_UpdateUserWallet($_SESSION['pw_uid'],$evoucher_settings_d['amount'],$evoucher_settings_d['currency'],1);
                
                $delete = $db->query("DELETE FROM evoucher WHERE id='$FormBTN'");
                echo "<br><br>";
                echo  info("E-Voucher Deleted.");
            
			}
			if(isset($_POST['view'])) {
                $FormBTN = protect($_POST['view']);
                $evoucher_v = $db->query("SELECT * FROM evoucher WHERE id='$FormBTN'");
                $view = $evoucher_v->fetch_assoc();
            ?>                     
                <!-- VIEW CARD CODE -->
                <br>
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <tbody>
                            <tr>
                                <td>E Voucher #</td>
                                <td><?= $view['number'] ?></td>
                            </tr><br>
                            <tr>
                                <td>Activation Code :</td>
                                <td><?= $view['activation'] ?></td>
                            </tr><br>
                            <tr>
                                <td>Balance :</td>
                                <td><?= $view['currency'] ?> <?= $view['amount'] ?></td>
                            </tr><br>
                            <tr>
                                <td>Created On :</td>
                                <td><?= date("d M Y H:i",$view['created']) ?></td>
                            </tr>
                        </tbody>
                         <br>
                        
                    </div>
                </div>
                <br>
                <!-- VIEW CARD CODE -->
            <?php } ?>
            </div>
            <!-- The Modal -->
            <div class="modal" id="myModal">
              <div class="modal-dialog">
                <div class="modal-content">
            
                  <!-- Modal Header -->
                  <div class="modal-header">
                    <h4 class="modal-title">Create E Voucher</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                    
                  <!-- Modal body -->
                  <div class="modal-body">
                      <div class="overflow-hidden position-relative border-radius-lg bg-cover h-100" style="background-image: url('<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>assets/miltipay/img/ivancik.jpg');">
                          <span class="mask bg-gradient-dark"></span>
                          <div class="card-body position-relative z-index-1 d-flex flex-column h-100 p-3">
                            <?php
							if(isset($_POST['create'])) {
                                $FormBTN = protect($_POST['create']);
                                if($FormBTN == "create") {
                                    
                                    $txid = strtoupper(randomHash(15));
                                    $time = time();
                                    $lable = protect($_POST['lable']);
                                    $amount = protect($_POST['amount']);
                                    $currency = protect($_POST['currency']);
                                    
                                    $fix_fee = $evoucher_settings['creation_fee_fix'];
                                    $per_fee = ($amount * $evoucher_settings['creation_fee_per'])/100;
                                    $fee = $fix_fee + $per_fee;
                                    $verify = $amount + $fee;
                                    
                                    if(empty($amount) or empty($currency) or empty($lable)) {
                                        echo error("Some fields are empty.");
                                    } elseif(!is_numeric($amount)) {
                                        echo error($lang['error_7']);
                                    } elseif($amount<0) {
                                        echo error($lang['error_7']);
                                    } elseif($amount == "0") {
                                        echo error($lang['error_7']);
                                    }else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $amount)) {
                                         echo error("Invalid Amount");   
                                    } elseif(get_wallet_balance($_SESSION['pw_uid'],$currency) < $verify) {
                                        echo error($lang['error_8']);
                                    } else {
                                        
                                        $description = "E-Voucher Creation.";
                                        
                                        // Create Transaction
                                        $create_transaction = $db->query("INSERT transactions (txid,type,sender,description,amount,currency,fee,status,created) 
                                        VALUES ('$txid','41','$_SESSION[pw_uid]','$description','$verify','$currency','$fee','1','$time')");
                                        
                                        // Create Activity
                                        $insert_activity = $db->query("INSERT activity (txid,type,uid,amount,currency,status,created) 
                                        VALUES ('$txid','41','$_SESSION[pw_uid]','$verify','$currency','1','$time')");
                                        
                                        // Deduct fund from wallet
                                        PW_UpdateUserWallet($_SESSION['pw_uid'],$verify,$currency,2);
                                        
                                        // Record Admin Profit
                                        PW_UpdateAdminWallet($fee,$currency);
                                        $insert_admin_log = $db->query("INSERT admin_logs (type,time,u_field_1,u_field_2,u_field_3) 
                                        VALUES ('7','$time','$fee','$currency','$txid')");
                                        
                                        
                                        $digit_evoucher = generateCode($evoucher_settings['digit']);
                                        $activation_evoucher = rand(1, 9999);
                                        
                                        $insert = $db->query("INSERT evoucher (uid,lable,txid,number,activation,currency,amount,created,status) 
                                        VALUES ('$_SESSION[pw_uid]','$lable','$txid','$digit_evoucher','$activation_evoucher','$currency','$amount','$time','1')"); // Status 1 means active
                                        
                                        echo success("E Vouncher $lable has been created.");
                                    }
                                }
                            }
                            ?>
                        <form class="user-connected-from user-login-form" action="" method="POST">
                            <div class="row">
                                <div class="col">
                                    <div class="input-group" style="height:45px;">
                                        <input type="text" class="form-control" id="" name="lable" placeholder="Enter E Vocher Lable">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <input type="number"  style="height:45px;" class="form-control" name="amount" placeholder="0.00" aria-label="Amount (to the nearest dollar)" required minimum="1.00" step="0.01">
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="height:45px;float:right;">
                                                <select class="form-control" name="currency" required style="height:36px;">
                                                    <?php
                                    				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='1'");
                                		            while($curr = $curr_Query->fetch_assoc()) {
                                    						
                                                        echo '<option value="'.$curr['currency'].'">'.$curr['currency'].'</option>';
                                                    }
                                                    ?>
                                                    <?php
                                    				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='2'");
                                		            while($curr = $curr_Query->fetch_assoc()) {
                                    						
                                                        echo '<option value="'.$curr['currency'].'">'.$curr['currency'].'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <br>
                        <button type="submit" name="create" value="create" class="btn btn-info" style="float:right;">Create E Voucher</button>
                    </form>
                    <small class="text-white">*Creation fee will be charge by USD <?= $evoucher_settings['creation_fee_fix'] ?> + <?= $evoucher_settings['creation_fee_per'] ?>%.</small>
                  </div>
            </div>
          </div>
        <!-- Modal footer -->
        </div>
      </div>
    </div>
            
    <div class="card-body px-0 pt-0 pb-2">
    <div class="table-responsive p-0">
        <table class="table align-items-center justify-content-center mb-0">
          <thead>
            <tr>
              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Voucher Lable</th>
              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount</th>
              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created On</th>
              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
            </tr>
          </thead>
          <tbody>
              <?php
                $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
                $limit = 15;
                $startpoint = ($page * $limit) - $limit;
                if($page == 1) {
                    $i = 1;
                } else {
                    $i = $page * $limit;
                }
                
				$statement = "evoucher WHERE uid='$_SESSION[pw_uid]'";
				$query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                
                if($query->num_rows>0) {
                    while($row = $query->fetch_assoc()) {
                        ?>
                    <tr>
                      <td class="align-middle text-center text-sm"><?= $row['lable'] ?></td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold"><?= $row['amount']; ?> <?= $row['currency'] ?></span>
                      </td>
                      <td class="align-middle text-center text-sm"><?= date("d M Y H:i",$row['created']); ?></td>
                      <td class="align-middle text-center text-sm">
                        <?php if ($row['status'] == "1") { ?>
                                <span class="badge badge-success">Active</span><!--1-->
                        <?php } elseif ($row['status'] == "3") { ?>
                                <span class="badge badge-danger">Redeemed</span><!--3-->
                        <?php } else { ?>
                                <span class="badge badge-danger">Block</span> <!--2-->
                        <?php } ?>
                      </td>
                      <td>
                        <div class="align-middle text-center text-sm">
                            <form action="" method="POST">
                                <button type="submit" name="view" value="<?= $row['id'] ?>" class="btn btn-info">View</button>
                                <button type="submit" name="delete" value="<?= $row['id'] ?>" class="btn btn-danger">Delete</button>
                            </form>
                            
                        </div>
                      </td>
                    </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="6"><center>You have no E Voucher yet.</center></td></tr>';
                }
                ?>
          </tbody>
        </table>
        <center>
        <?php
        
		$ver = $settings['url']."index.php?a=account&b=money/evoucher";
		if(web_pagination($statement,$ver,$limit,$page)) {
			echo web_pagination($statement,$ver,$limit,$page);
		}
        
        ?>
        </center>
      </div>
    </div>
</div>
</div>
</div>
</div>