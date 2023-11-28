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
                        E Voucher Creation
                        </b>    <span class="float-right"><?php echo $lang['gross_amount']; ?></span><p>
                        <p><?php echo $lang['payment_status']; ?>: <?php echo PW_DecodeTXStatus($row['status']); ?> <span class="float-right"><span style="font-size:22px;"><?php echo $row['amount']; ?> <?php echo $row['currency']; ?></span></span></p>
                        <p><?php echo $lang['transaction_id']; ?>: <?php echo $row['txid']; ?></p>
                        <p><?php echo $lang['payment_date']; ?>: <?php echo date("d M Y H:i",$row['created']); ?></p>
                        <?php if($row['description']) { ?>
                        <p><?php echo $lang['payment_description']; ?>: <?php echo $row['description']; ?>
                        <?php } ?>
                        <hr/>
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>
