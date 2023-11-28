<?php 
// eWallet - PHP Script
// Author: DeluxeScript

$query_1 = $db->query("SELECT * FROM merchant_gateways WHERE name='Flutterwave'");
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
    $fix_fee = PW_currencyConvertor($fix_fee,$settings['default_currency'],$currency);
    
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

<?php
define('FLUTTER_PUBLISHABLE_KEY', $row_1['field_2']); 
if($settings['favicon']) {
    $logo = $settings['url'].$settings['favicon'];
	
} else {
    $logo = "$settings[url]assets/logo/favicon.png";
}
?>
    <?php if ($m["google_analytics"] == "1") { ?>
        <?= $settings['google_analytics_code'] ?>
    <?php } ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if($settings['favicon']) { ?>
		<link rel="icon" type="image/png" href="<?= $settings['url'].$settings['favicon'] ?>">
	<?php } else { ?>
		<link rel="icon" type="image/png" href="<?= $settings['url'] ?>assets/logo/favicon.png">
	<?php } ?>
    <link rel="stylesheet" href="<?php echo filter_var($settings['url']); ?>assets/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="<?= $settings['url'] ?>assets/wallet/css/nucleo-icons.css" rel="stylesheet" />
    <link href="<?= $settings['url'] ?>assets/wallet/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="<?= $settings['url'] ?>assets/wallet/css/nucleo-svg.css" rel="stylesheet" />
    <link id="pagestyle" href="<?= $settings['url'] ?>assets/wallet/css/soft-ui-dashboard.css?v=1.0.1" rel="stylesheet" />
    <title>Pay via Flutterwave - <?=$settings['url']; ?></title>
    <meta name="description" content="<?php echo filter_var($settings['description']); ?>">
    <meta name="keywords" content="<?php echo filter_var($settings['keywords']); ?>">
</head>
<body>
    <br>
    <?php
    if (empty($_SESSION['email'])) {
    ?>
    <center>
        <p>
          <a class="btn bg-gradient-warning" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
            Pay Via Flutterwave
          </a>
        </p>
    </center>
    <?php } ?>
    <p></p>
    <div class="collapse" id="collapseExample">
      <div class="card-body">
        <?php
        if (isset($_POST['email_submit'])) {
            
            $_SESSION['email'] = $_POST['email'];
            header("Refresh:0");
        }
        ?>
        <form action="" method="POST">
            <center>
            <div class="form-group">
                <label for="example-text-input" class="form-control-label">Enter Email Address</label>
                <center><input class="form-control" type="email" placeholder="johnsnow@gmail.com" name="email" style="width:25%;"></center>
            </div>
            
            <button class="stripe-button btn btn-success" style="width:25%;" type="submit" name="email_submit" value="email_submit">Pay Now</button>
            </center>
        </form>
      </div>
    </div>
    <?php
    if (!empty($_SESSION['email'])) {
    ?>
    <form>
	    <script src="https://checkout.flutterwave.com/v3.js"></script>
	    <div id="buynow">
        	<center><button class="stripe-button btn btn-primary" id="payButton" type="button" onClick="makePayment()">Confirm</button></center>
        	<br>
	    </div>
	</form>
	<?php } ?>
	<?php
	echo '
    <script>
      function makePayment() {
        FlutterwaveCheckout({
          public_key: "'.FLUTTER_PUBLISHABLE_KEY.'",
          tx_ref: "'.$b.'",
          amount: "'.$amount_with_fee.'",
          currency: "'.$currency.'",
          
          payment_options: "card",
          redirect_url: "'.$settings['url'].'callbacks/checkPayment.php?a=Flutterwave_P",
         
          customer: {
            email: "'.$settings['supportemail'].'",
            phone_number: "",
            name: "",
          },
          callback: function (data) {
            console.log(data);
          },
          onclose: function() {
            // close modal
            location.href = "'.$row['return_cancel'].'";
    	
          },
          customizations: {
            title: "'.$settings['name'].'",
            description: "'.$payment_note.'",
            logo: "'.$logo.'",
          },
        });
      }
    </script>';
    ?>
    <script src="<?php echo filter_var($settings['url']); ?>assets/wallet/js/core/popper.min.js"></script>
    <script src="<?php echo filter_var($settings['url']); ?>assets/wallet/js/core/bootstrap.min.js"></script>
    <script src="<?php echo filter_var($settings['url']); ?>assets/wallet/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="<?php echo filter_var($settings['url']); ?>assets/wallet/js/soft-ui-dashboard.min.js?v=1.0.1"></script>

    <script src="<?php echo filter_var($settings['url']); ?>assets/wallet/js/flatpickr.min.js"></script> 
    <script src="<?php echo filter_var($settings['url']); ?>assets/js/jquery-1.12.4.min.js"></script>
    <script src="<?php echo filter_var($settings['url']); ?>assets/js/popper.min.js"></script>
    <script src="<?php echo filter_var($settings['url']); ?>assets/js/bootstrap.min.js"></script>
    <script src="<?php echo filter_var($settings['url']); ?>assets/js/slick.min.js"></script>
    <script src="<?php echo filter_var($settings['url']); ?>assets/js/jquery.peity.min.js"></script>
    <script src="<?php echo filter_var($settings['url']); ?>assets/js/jquery.slimscroll.min.js"></script>
    <script src="<?php echo filter_var($settings['url']); ?>assets/js/custom.js"></script>
    <script src="<?php echo filter_var($settings['url']); ?>assets/js/wallet.js"></script>
    <script src="<?php echo filter_var($settings['url']); ?>assets/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript">
    $('#datepicker1').datepicker({ dateFormat: "dd-mm-yy"});
    $('#datepicker2').datepicker({ dateFormat: "dd-mm-yy"});
    if ( window.history.replaceState ) {
      window.history.replaceState( null, null, window.location.href );
    }
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
    </script>
</body>
</html>