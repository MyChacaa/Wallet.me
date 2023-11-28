<!DOCTYPE html>
<html lang="en">
<head>
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
    <title>
        <?php
		  
          $b = protect($_GET['b']);
          if($b == "summary") { echo filter_var($lang['title_summary']); } 
          elseif($b == "transaction") { echo filter_var($lang['title_transaction_details']); }
          elseif($b == "ref") { echo "Referrals"; }
          elseif($b == "settings") { echo filter_var($lang['title_account_settings']); } 
		  elseif($b == "disputes") { echo "Disputes"; }
		  elseif($b == "escrow") { echo "Escrow Payments"; }
		  elseif($b == "supports") { echo "Support"; } 
          elseif($b == "money") {
              $c = protect($_GET['c']);
              if($c == "deposit") { echo filter_var($lang['title_deposit_money']); }
              elseif($c == "request") { echo filter_var($lang['title_request_money']); }
              elseif($c == "send") { echo filter_var($lang['title_send_money']); } 
              elseif($c == "withdrawal") { echo filter_var($lang['title_withdrawal_funds']); }
              elseif($c == "link") { echo "Payment Link"; }
              elseif($c == "converter") { echo "Converter"; }
              elseif($c == "evoucher") { echo "E-Vouchers"; }
              elseif($c == "fixed_deposit") { echo "Fixed Deposit"; }
              else { }
           } elseif($b == "prepaid_card") {
              $c = protect($_GET['c']);
              if($c == "card") { echo "Prepaid VISA Credit Card"; }
              
              else { }
          } elseif($b == "activity") { echo filter_var($lang['title_activity']); } 
          elseif($b == "disputes") { 
			 if (isset($_GET['c'])) { 
              $c = protect($_GET['c']);
              if($c == "open") { echo filter_var($lang['title_open_dispute']); }
              elseif($c == "close") { echo filter_var($lang['title_close_dispute']); }
              elseif($c == "escalate") { echo filter_var($lang['title_escalate_for_review']); }
              elseif($c == "dispute") { echo filter_var($lang['title_dispute_details']); }
              elseif($c == "disputes") { echo filter_var($lang['title_disputes']); }
			 else { echo filter_var($lang['title_disputes']); } }
             } elseif($b == "supports") { 
              if (isset($_GET['c'])) { 
              $c = protect($_GET['c']);
              if($c == "open") { echo 'Open Support Ticket'; }
              elseif($c == "close") { echo 'Close Support Ticket'; }
              elseif($c == "escalate") { echo 'Sended For Review'; }
              elseif($c == "dispute") { echo 'Support Ticket Details'; }
              elseif($c == "disputes") { echo 'Support Ticket'; }
              else { echo 'Support Ticket'; } }
          } else {}
         ?> - <?php echo filter_var($settings['name']); ?>
  </title>
  <meta name="description" content="<?php echo filter_var($settings['description']); ?>">
  <meta name="keywords" content="<?php echo filter_var($settings['keywords']); ?>">
</head>
<body class="g-sidenav-show  bg-gray-100">