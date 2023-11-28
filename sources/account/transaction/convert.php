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

$ConvertQuery = $db->query("SELECT * FROM users_converts WHERE id='$row[recipient]'");
$cnv = $ConvertQuery->fetch_assoc();
?>
            <div class="col-md-12">
                    
                        <div class="user-wallet-wrap">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <div class="user-connected-form-block">
                                            <h3><?php echo $lang['head_transaction_details']; ?></h3>
                                            <hr/>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p><b>
                                                    <?php echo $lang['convert']; ?> <?php echo $cnv['from_amount']." ".$cnv['from_currency']; ?> to <?php echo $cnv['to_amount']." ".$cnv['to_currency']; ?>
                                                    </b>    <span class="float-right"><?php echo $lang['amount']; ?></span><p>
                                                    <p><?php echo $lang['rate']; ?>: <?php echo $cnv['from_rate']." ".$cnv['from_currency']." = ".$cnv['to_rate']." ".$cnv['to_currency']; ?> <span class="float-right"><span style="font-size:22px;"><?php echo $row['amount']; ?> <?php echo $row['currency']; ?></span></span></p>
                                                    <p><?php echo $lang['converted_on']; ?>: <?php echo date("d M Y H:i",$row['created']); ?></p>
                                                    <hr/>
                                                </div>
                                            </div>  

                                        </div><!-- create-account-block -->
                                    </div>
                                </div>
                            </div><!-- user-login-signup-form-wrap -->
                </div>