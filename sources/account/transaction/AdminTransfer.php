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
?>
<div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6><?php echo $lang['head_transaction_details']; ?></h6>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p><b>
                        Activity Performed by Administrator
                        </b>    <span class="float-right"><?php echo $lang['gross_amount']; ?></span><p>
                        <p><?php echo $lang['payment_status']; ?>: <?php echo PW_DecodeTXStatus($row['status']); ?> <span class="float-right"><span style="font-size:22px;"><?php echo $row['amount']; ?> <?php echo $row['currency']; ?></span></span></p>
                        <p><?php echo $lang['transaction_id']; ?>: <?php echo $row['txid']; ?></p>
                        <p><?php echo $lang['payment_date']; ?>: <?php echo date("d M Y H:i",$row['created']); ?></p>
                        <?php if($row['description']) { ?>
                        <p><?php echo $lang['payment_description']; ?>: <?php echo $row['description']; ?>
                        <?php } ?>
                        
                    </div>
                                                
                                                
                                                <?php if($row['item_id']) { ?>
                                                    <div class="col-md-12">
                                                        <br>
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <td><?php echo $lang['item_number']; ?></td>
                                                            <td><?php echo $lang['item_name']; ?></td>
                                                            <td><?php echo $lang['amount']; ?></td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><?php echo $row['item_id']; ?></td>
                                                            <td><?php echo $row['item_name']; ?></td>
                                                            <td><?php echo $row['amount']; ?> <?php echo $row['currency']; ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table></div>
                                                <?php } ?>
                                                <div class="col-md-12">
                                                    <hr/>
                                                </div>
                                                <div class="col-md-12">
                                                    <h4><?php echo $lang['payment_details']; ?></h4>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <table class="table table-striped">
                                                                <tbody>
                                                                    <tr>
                                                                        <td><?php echo $lang['gross_amount']; ?>: <span class="float-right"><?php echo $row['amount']; ?> <?php echo $row['currency']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?php echo $settings['name']; ?> <?php echo $lang['transaction_fee']; ?>: <span class="float-right"><?php echo $row['fee']; ?> <?php echo $row['currency']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?php echo $lang['net_amount']; ?>: <span class="float-right"><?php echo $row['amount']-$row['fee']; ?> <?php echo $row['currency']; ?></td>
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