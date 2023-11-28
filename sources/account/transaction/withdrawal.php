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
$WithdrawalQuery = $db->query("SELECT * FROM withdrawals WHERE txid='$row[txid]'");
$w = $WithdrawalQuery->fetch_assoc();
?>
            <div class="col-md-12">
                    
                        <div class="user-wallet-wrap">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <div class="user-connected-form-block">
                                            <h3>Transaction Details</h3>
                                            <hr/>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p><b>
                                                    <?php
                                                    echo 'Withdrawal to '.gatewayinfo($row['withdrawal_via'],"name");
                                                    ?>
                                                    </b>    <span class="float-right">Amount</span><p>
                                                    <p>Payment Status: <?php echo PW_DecodeTXStatus($row['status']); ?> <span class="float-right"><span style="font-size:22px;"><?php echo $row['amount']; ?> <?php echo $row['currency']; ?></span></span></p>
                                                    <p>Transaction ID: <?php echo $row['txid']; ?></p>
                                                    <p>Payment date: <?php echo date("d M Y H:i",$row['created']); ?></p>
                                                    <?php if($row['description']) { ?>
                                                    <p>Payment Description: <?php echo $row['description']; ?>
                                                    <?php } ?>
                                                    <hr/>
                                                </div>
                                                <div class="col-md-6">
                                                    <h4>Sender</h4>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <?php if(idinfo($row['sender'],"account_type") == "1") { echo idinfo($row['sender'],"first_name")." ".idinfo($row['sender'],"last_name"); } else { echo idinfo($row['sender'],"business_name"); } ?> - <?php if(idinfo($row['sender'],"document_verified") == "1") { echo 'Verified'; } else { echo 'Not Verified'; } ?><br/>
                                                            <?php echo idinfo($row['sender'],"email"); ?><br/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h4>Recipient</h4>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <?php
                                                            $GetValues = $db->query("SELECT * FROM withdrawals_values WHERE withdrawal_id='$w[id]'");
                                                            if($GetValues->num_rows>0) {
                                                                while($gv = $GetValues->fetch_assoc()) {
                                                                    echo "<p>".PW_GetFieldName($gv['field_id']).": <b>".$gv['value']."</b></p>";
                                                                }
                                                            } else { 
                                                                echo 'No data.';
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <hr/>
                                                </div>
                                                <div class="col-md-12">
                                                    <h4>Payment Details</h4>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <table class="table table-striped">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>Gross amount: <span class="float-right"><?php echo $row['amount']; ?> <?php echo $row['currency']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?php echo $settings['name']; ?> transaction fee: <span class="float-right"><?php echo $row['fee']; ?> <?php echo $row['currency']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Net amount: <span class="float-right"><?php echo $row['amount']-$row['fee']; ?> <?php echo $row['currency']; ?></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>  

                                        </div><!-- create-account-block -->
                                    </div>
                                </div>
                            </div><!-- user-login-signup-form-wrap -->
                </div>