<!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;"><?php echo filter_var($settings['name']); ?></a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Portal</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">
		  <?php 
		  $b = protect($_GET['b']);
		  if (isset($_GET['c'])) {
			$c = protect($_GET['c']); ?>
			  <?php if($c == "send") { echo 'Send Money'; } ?>
			  <?php if($c == "request") { echo 'Request Money'; } ?>
              <?php if($c == "deposit") { echo 'Deposit Fund'; } ?>
              <?php if($c == "withdrawal") { echo 'Withdraw Fund'; } ?>
              <?php if($c == "card") { echo 'Prepaid VISA Credit Card'; } ?>
              <?php if($c == "evoucher") { echo 'E-Vouchers'; } ?>
		  <?php } ?>
              <?php if($b == "summary") { echo 'Dashboard'; } ?>
              <?php if($b == "escrow") { echo 'Escrow Payments'; } ?>
              
          </h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group">
              
            </div>
          </div>
          <ul class="navbar-nav  justify-content-end">
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                </div>
              </a>
            </li>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <li class="nav-item d-flex align-items-center">
              <a href="<?php echo filter_var($settings['url']); ?>index.php?a=account&b=activity" class="nav-link text-body font-weight-bold px-0">
                <i class="ni ni-collection me-sm-1"></i>
                <span class="d-sm-inline d-none">All Activity</span>
              </a>
            </li>
            <?php if ($m["disputes"] == 1) { ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <li class="nav-item d-flex align-items-center">
              <a href="<?php echo filter_var($settings['url']); ?>index.php?a=account&b=disputes" class="nav-link text-body font-weight-bold px-0">
                <i class="ni ni-email-83 me-sm-1"></i>
                <span class="d-sm-inline d-none">Disputes</span>
              </a>
            </li>
            <?php } ?>
            <?php if ($m["support_ticket"] == 1) { ?>
            <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <li class="nav-item d-flex align-items-center">
              <a href="<?php echo filter_var($settings['url']); ?>index.php?a=account&b=supports" class="nav-link text-body font-weight-bold px-0">
                <i class="ni ni-headphones me-sm-1"></i>
                <span class="d-sm-inline d-none">Support</span>
              </a>
            </li> -->
            <?php } ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <li class="nav-item d-flex align-items-center">
              <a href="<?php echo filter_var($settings['url']); ?>index.php?a=account&b=settings" class="nav-link text-body font-weight-bold px-0">
                <i class="ni ni-settings me-sm-1"></i>
                <span class="d-sm-inline d-none">Setting</span>
              </a>
            </li>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <li class="nav-item d-flex align-items-center">
              <a href="<?php echo filter_var($settings['url']); ?>index.php?a=logout" class="nav-link text-body font-weight-bold px-0">
                <i class="ni ni-button-power me-sm-1"></i>
                <span class="d-sm-inline d-none">Logout</span>
              </a>
            </li>
            
            
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->  