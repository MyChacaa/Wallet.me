<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
if(isset($_GET['a'])) {
$a = protect($_GET['a']);
}
if(isset($_GET['b'])) {
$b = protect($_GET['b']);
}
?>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel Panel - <?php echo filter_var($settings['name'], FILTER_SANITIZE_STRING); ?></title>
    <meta name="description" content="Control Panel - <?= $settings['name']; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="<?= $settings['url'] ?>assets/new/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= $settings['url'] ?>assets/new/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= $settings['url'] ?>assets/new/plugins/summernote/summernote-bs4.min.css">
    <link rel="stylesheet" href="<?= $settings['url'] ?>assets/new/plugins/codemirror/codemirror.css">
    <link rel="stylesheet" href="<?= $settings['url'] ?>assets/new/plugins/codemirror/theme/monokai.css">
    <link rel="stylesheet" href="<?= $settings['url'] ?>assets/new/plugins/simplemde/simplemde.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="<?= $settings['url'] ?>assets/new/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="<?= $settings['url'] ?>assets/new/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="<?= $settings['url'] ?>assets/new/plugins/jqvmap/jqvmap.min.css">
    <link rel="stylesheet" href="<?= $settings['url'] ?>assets/new/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="<?= $settings['url'] ?>assets/new/plugins/daterangepicker/daterangepicker.css">
    
</head>
<body class="hold-transition sidebar-mini layout-fixed" style="background:#FEE5FA;">
<div class="wrapper">
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="https://s3.envato.com/files/335098119/80px.png" alt="AdminLTELogo" height="60" width="60">
    </div>
    
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <span class="badge badge-primary"><i class="fa fa-pencil"></i> Version <?= $version ?></span>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
    </ul>
    </nav>
  <!-- /.navbar -->
    
    
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background:#490139;">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <center><span class="brand-text font-weight-light">Admin Panel</span></center>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="https://s3.envato.com/files/335098119/80px.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="./?a=users&b=edit&id=<?php echo filter_var($_SESSION['admin_uid'], FILTER_SANITIZE_STRING); ?>" class="d-block"><?php echo idinfo($_SESSION['admin_uid'],"account_user"); ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item <?php if ($a == "") { echo "menu-open"; } ?>">
                <a href="./" class="nav-link">
                  <i class="nav-icon fas fa-tachometer-alt"></i>
                  <p>Dashboard</p>
                </a>
            </li>
            <li class="nav-item <?php if ($a == "users" or $a == "languages" or $a == "update_logo") { echo "menu-open"; } ?>">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-chart-pie"></i>
                  <p>Manage User<i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./?a=users" class="nav-link <?php if ($a == "users") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All Users</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=languages" class="nav-link <?php if ($a == "languages" and $b == "") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Manage Languages</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=languages&b=add" class="nav-link <?php if ($a == "languages" and $b == "add") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Languages</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=update_logo" class="nav-link <?php if ($a == "update_logo") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Update Logo</p>
                    </a>
                  </li>
                </ul>
            </li>
            <li class="nav-item <?php if ($a == "deposits" or $a == "deposit_methods" or $a == "manual_deposit") { echo "menu-open"; } ?>">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-clinic-medical"></i>
                  <p>Manage Deposits<i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./?a=deposits" class="nav-link <?php if ($a == "deposits" and $b == "" or $a == "deposits" and $b == "view") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All Deposits</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=deposit_methods" class="nav-link <?php if ($a == "deposit_methods" and $b == "" or $a == "deposit_methods" and $b == "edit" or $a == "deposit_methods" and $b == "delete") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Deposit Methods</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=deposit_methods&b=add" class="nav-link <?php if ($a == "deposit_methods" and $b == "add") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Auto Method</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=manual_deposit" class="nav-link <?php if ($a == "manual_deposit" and $b == "" or $a == "manual_deposit" and $b == "edit" or $a == "manual_deposit" and $b == "delete") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Manual Method</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=manual_deposit&b=add" class="nav-link <?php if ($a == "manual_deposit" and $b == "add") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Manual Method</p>
                    </a>
                  </li>
                </ul>
            </li>
            <li class="nav-item <?php if ($a == "withdrawals" or $a == "withdrawal_methods") { echo "menu-open"; } ?>">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-coins"></i>
                  <p>Manage Withdraws<i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./?a=withdrawals" class="nav-link <?php if ($a == "withdrawals" and $b == "" or $a == "withdrawals" and $b == "view") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All Withdraw</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=withdrawal_methods" class="nav-link <?php if ($a == "withdrawal_methods" and $b == "" or $a == "withdrawal_methods" and $b == "edit" or $a == "withdrawal_methods" and $b == "delete") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Withdrawal Methods</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=withdrawal_methods&b=add" class="nav-link <?php if ($a == "withdrawal_methods" and $b == "add") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Method</p>
                    </a>
                  </li>
                </ul>
            </li>
            <li class="nav-item <?php if ($a == "send_request_fee" or $a == "transactions" or $a == "disputes") { echo "menu-open"; } ?>">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-cart-plus"></i>
                  <p>Send/Request<i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./?a=send_request_fee" class="nav-link <?php if ($a == "send_request_fee" and $b == "") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Fee Setup</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=transactions" class="nav-link <?php if ($a == "transactions" and $b == "" or $a == "transactions" and $b == "view") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All Transactions</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=disputes" class="nav-link <?php if ($a == "disputes" and $b == "" or $a == "disputes" and $b == "view") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All Disputes</p>
                    </a>
                  </li>
                </ul>
            </li>
            <li class="nav-item <?php if ($a == "currencies" or $a == "all_currencies" or $a == "curencies_log" or $a == "curencies_fee") { echo "menu-open"; } ?>">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-money-bill-wave"></i>
                  <p>Currencies<i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./?a=currencies" class="nav-link <?php if ($a == "currencies" and $b == "") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Manage Currencies</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=all_currencies" class="nav-link <?php if ($a == "all_currencies" and $b == "") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All Currencies</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=curencies_fee" class="nav-link <?php if ($a == "curencies_fee" and $b == "") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Fee Setup</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=curencies_log" class="nav-link <?php if ($a == "curencies_log" and $b == "" or $a == "curencies_log" and $b == "view") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Currency Convert Logs</p>
                    </a>
                  </li>
                </ul>
            </li>
            <li class="nav-item <?php if ($a == "all_merchant" or $a == "merchant_fee" or $a == "merchant_payments_log") { echo "menu-open"; } ?>">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-store-alt"></i>
                  <p>Manage Merchants<i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./?a=all_merchant" class="nav-link <?php if ($a == "all_merchant" and $b == "" or $a == "all_merchant" and $b == "edit") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All Merchant</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=merchant_fee" class="nav-link <?php if ($a == "merchant_fee" and $b == "") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Merchant Fee Setup</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=merchant_gateways" class="nav-link <?php if ($a == "merchant_gateways" and $b == "") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Merchant Gateways</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=merchant_payments_log" class="nav-link <?php if ($a == "merchant_payments_log" and $b == "" or $a == "merchant_payments_log" and $b == "view") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Merchant Payment logs</p>
                    </a>
                  </li>
                </ul>
            </li>
            <li class="nav-item <?php if ($a == "evoucher_setting" or $a == "evoucher_all") { echo "menu-open"; } ?>">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-gift"></i>
                  <p>Manage Vouchers<i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./?a=evoucher_setting" class="nav-link <?php if ($a == "evoucher_setting" and $b == "") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>E-Voucher Settings</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=evoucher_all" class="nav-link <?php if ($a == "evoucher_all" and $b == "") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All E-Vouchers</p>
                    </a>
                  </li>
                </ul>
            </li>
            <li class="nav-item <?php if ($a == "fixed_deposit" or $a == "fixed_deposit_list") { echo "menu-open"; } ?>">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-money-check-alt"></i>
                  <p>Manage Fixed Deposits<i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./?a=fixed_deposit_list" class="nav-link <?php if ($a == "fixed_deposit_list" and $b == "") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Fixed Deposit List</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=fixed_deposit" class="nav-link <?php if ($a == "fixed_deposit" and $b == "") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Fixed Deposit Plans</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=fixed_deposit&b=add" class="nav-link <?php if ($a == "fixed_deposit" and $b == "add") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Plans</p>
                    </a>
                  </li>
                </ul>
            </li>
            <li class="nav-item <?php if ($a == "escrow") { echo "menu-open"; } ?>">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-dumpster-fire"></i>
                  <p>Escrow<i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./?a=escrow" class="nav-link <?php if ($a == "escrow" and $b == "" or $a == "escrow" and $b == "view") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All Escrow</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=escrow&b=settings" class="nav-link <?php if ($a == "escrow" and $b == "settings") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Escrow Settings</p>
                    </a>
                  </li>
                </ul>
            </li>
            <li class="nav-item <?php if ($a == "link") { echo "menu-open"; } ?>">
                <a href="./?a=link" class="nav-link">
                  <i class="nav-icon fas fa-bacon"></i>
                  <p>Payment Links</p>
                </a>
            </li>
            <li class="nav-item <?php if ($a == "ref") { echo "menu-open"; } ?>">
                <a href="./?a=ref" class="nav-link">
                  <i class="nav-icon fas fa-chalkboard-teacher"></i>
                  <p>Manage Referrals</p>
                </a>
            </li>
            <!-- <li class="nav-item <?php if ($a == "support") { echo "menu-open"; } ?>">
                <a href="./?a=support" class="nav-link">
                  <i class="nav-icon fas fa-headset"></i>
                  <p>Support Tickets</p>
                  <span class="right badge badge-danger"><?php $query = $db->query("SELECT * FROM support WHERE status=1"); echo filter_var($query->num_rows, FILTER_SANITIZE_STRING); ?></span>
                </a>
            </li> -->
            <li class="nav-item <?php if ($a == "module") { echo "menu-open"; } ?>">
                <a href="./?a=module" class="nav-link">
                  <i class="nav-icon fas fa-boxes"></i>
                  <p>Modules</p>
                </a>
            </li>
            <li class="nav-item <?php if ($a == "live_chat") { echo "menu-open"; } ?>">
                <a href="./?a=live_chat" class="nav-link">
                  <i class="nav-icon fas fa-crow"></i>
                  <p>Live Chat</p>
                </a>
            </li>
            <li class="nav-item <?php if ($a == "google_analytics") { echo "menu-open"; } ?>">
                <a href="./?a=google_analytics" class="nav-link">
                  <i class="nav-icon fas fa-chart-bar"></i>
                  <p>Google Analytics</p>
                </a>
            </li>
            <li class="nav-item <?php if ($a == "send_mail") { echo "menu-open"; } ?>">
                <a href="./?a=send_mail" class="nav-link">
                  <i class="nav-icon fas fa-mail-bulk"></i>
                  <p>Send Email</p>
                </a>
            </li>
            <li class="nav-item <?php if ($a == "country") { echo "menu-open"; } ?>">
                <a href="./?a=country" class="nav-link">
                  <i class="nav-icon fas fa-globe"></i>
                  <p>Manage Countries</p>
                </a>
            </li>
            <li class="nav-item <?php if ($a == "pages") { echo "menu-open"; } ?>">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-file-alt"></i>
                  <p>Manage Pages<i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./?a=pages" class="nav-link <?php if ($a == "pages" and $b == "") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All Pages</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=pages&b=add" class="nav-link <?php if ($a == "pages" and $b == "add") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Create Page</p>
                    </a>
                  </li>
                </ul>
            </li>
            <li class="nav-item <?php if ($a == "settings" or $a == "smtp_settings" or $a == "admin_profits" or $a == "admin_profits_logs") { echo "menu-open"; } ?>">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-cogs"></i>
                  <p>Admin Management<i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./?a=settings" class="nav-link <?php if ($a == "settings" and $b == "") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Site Settings</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=smtp_settings" class="nav-link <?php if ($a == "smtp_settings" and $b == "") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Mail Settings</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=admin_profits" class="nav-link <?php if ($a == "admin_profits" and $b == "") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Admin Profit</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?a=admin_profits_logs" class="nav-link <?php if ($a == "admin_profits_logs" and $b == "") { echo "active"; } ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Admin Profit Logs</p>
                    </a>
                  </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="./?a=logout" class="nav-link">
                  <i class="nav-icon fas fa-ban"></i>
                  <p>Logout</p>
                </a>
            </li>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
    
   <div class="content-wrapper" style="background:#FEE5FA;">
        <br>
        <div class="content mt-3">
            