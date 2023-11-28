<?php
if (isset($_GET['b'])) {
$b = protect($_GET['b']);
}
if (isset($_GET['c'])) {
$c = protect($_GET['c']); 
} else {
	$c = "";
}
?>
<script src="https://use.fontawesome.com/08d0c47985.js"></script>
<style type="text/css">
.iconBack{
    background: #E8ECEF;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    width: 35px;
    height: 35px;
    box-shadow: 0 3px 6px rgb(0 0 0 / 15%);
    justify-content: center;
}
.icon{
    color: #2E3235;
    font-size: 18px;
}
.iconCurrent{
	background: #CB1897
}
.iconCurrent .icon{
	color: white
}
</style>
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-left ms-3 bg-white" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute right-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="<?php echo filter_var($settings['url']); ?>index.php">
        <?php if($settings['favicon']) { ?>
    		<img src="<?= $settings['url'].$settings['favicon'] ?>" class="navbar-brand-img h-100 w-15" alt="...">
    	<?php } else { ?>
    		<img src="<?= $settings['url'] ?>assets/logo/favicon.png" class="navbar-brand-img h-100 w-15" alt="...">
    	<?php } ?>
        
        <span class="ms-1 font-weight-bold"><?php echo filter_var($settings['name']); ?> Dashboard</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div >
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link  <?php if($b == "summary") { echo 'active'; } ?>" href="<?= $settings['url']; ?>index.php?a=account&b=summary">
            <span class="iconBack <?php if($b == "summary") { echo 'iconCurrent'; } ?> text-center me-2 d-flex align-items-center justify-content-center">
            	<center><i class="fa fa-igloo" style="color:<?php if($b == "summary") { echo 'white'; } ?>;"></i></center>
            </span>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        <?php if ($m["send_money"] == 1) { ?>
        <li class="nav-item">
          <a class="nav-link  <?php if($c == "send") { echo 'active'; } ?>" href="<?= $settings['url']; ?>index.php?a=account&b=money&c=send">
            <span class="iconBack <?php if($c == "send") { echo 'iconCurrent'; } ?> text-center me-2 d-flex align-items-center justify-content-center">
            	<center><i class="far fa-paper-plane" style="color:<?php if($c == "send") { echo 'white'; } ?>;margin-left:-3px;"></i></center>
            </span>
            <span class="nav-link-text ms-1">Send Money</span>
          </a>
        </li>
        <?php } ?>
        
        <?php if ($m["request_money"] == 1) { ?>
        <li class="nav-item">
          <a class="nav-link  <?php if($c == "request") { echo 'active'; } ?>" href="<?= $settings['url']; ?>index.php?a=account&b=money&c=request">
            <span class="iconBack <?php if($c == "request") { echo 'iconCurrent'; } ?> text-center me-2 d-flex align-items-center justify-content-center">
            	<i class="fa fa-hands-helping" style="color:<?php if($c == "request") { echo 'white'; } ?>;"></i>
            </span>
            <span class="nav-link-text ms-1">Request Money</span>
          </a>
        </li>
        <?php } ?>
        <!--
        <li class="nav-item">
          <a class="nav-link  <?php if($c == "card") { echo 'active'; } ?>" href="<?= $settings['url']; ?>index.php?a=account&b=prepaid_card/card">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <svg width="12px" height="12px" viewBox="0 0 43 36" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <title>credit-card</title>
                <g id="Basic-Elements" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <g id="Rounded-Icons" transform="translate(-2169.000000, -745.000000)" fill="#FFFFFF" fill-rule="nonzero">
                    <g id="Icons-with-opacity" transform="translate(1716.000000, 291.000000)">
                      <g id="credit-card" transform="translate(453.000000, 454.000000)">
                        <path class="color-background" d="M43,10.7482083 L43,3.58333333 C43,1.60354167 41.3964583,0 39.4166667,0 L3.58333333,0 C1.60354167,0 0,1.60354167 0,3.58333333 L0,10.7482083 L43,10.7482083 Z" id="Path" opacity="0.593633743"></path>
                        <path class="color-background" d="M0,16.125 L0,32.25 C0,34.2297917 1.60354167,35.8333333 3.58333333,35.8333333 L39.4166667,35.8333333 C41.3964583,35.8333333 43,34.2297917 43,32.25 L43,16.125 L0,16.125 Z M19.7083333,26.875 L7.16666667,26.875 L7.16666667,23.2916667 L19.7083333,23.2916667 L19.7083333,26.875 Z M35.8333333,26.875 L28.6666667,26.875 L28.6666667,23.2916667 L35.8333333,23.2916667 L35.8333333,26.875 Z" id="Shape"></path>
                      </g>
                    </g>
                  </g>
                </g>
              </svg>
            </div>
            <span class="nav-link-text ms-1">Prepaid Credit Cards</span>
          </a>
        </li>
        -->
        <?php if ($m["deposit"] == 1) { ?>
        <li class="nav-item">
          <a class="nav-link  <?php if($c == "deposit") { echo 'active'; } ?>" href="<?= $settings['url']; ?>index.php?a=account&b=money&c=deposit">
            <span class="iconBack <?php if($c == "deposit") { echo 'iconCurrent'; } ?> text-center me-2 d-flex align-items-center justify-content-center">
            	<i class="fa fa-plus-square" style="color:<?php if($c == "deposit") { echo 'white'; } ?>;"></i>
            </span>
            <span class="nav-link-text ms-1">Deposit</span>
          </a>
        </li>
        <?php } ?>
        <?php if ($m["withdrawal"] == 1) { ?>
        <li class="nav-item">
          <a class="nav-link  <?php if($c == "withdrawal") { echo 'active'; } ?>" href="<?= $settings['url']; ?>index.php?a=account&b=money&c=withdrawal">
            <span class="iconBack <?php if($c == "withdrawal") { echo 'iconCurrent'; } ?> text-center me-2 d-flex align-items-center justify-content-center">
            	<i class="fa fa-minus-square" style="color:<?php if($c == "withdrawal") { echo 'white'; } ?>;"></i>
            </span>
            <span class="nav-link-text ms-1">Withdraw</span>
          </a>
        </li>
        <?php } ?>
        <?php if ($m["currency_convert"] == 1) { ?>
        <li class="nav-item">
          <a class="nav-link  <?php if($c == "converter") { echo 'active'; } ?>" href="<?= $settings['url']; ?>index.php?a=account&b=money&c=converter">
            <span class="iconBack <?php if($c == "converter") { echo 'iconCurrent'; } ?> text-center me-2 d-flex align-items-center justify-content-center">
            	<i class="fa fa-exchange-alt" style="color:<?php if($c == "converter") { echo 'white'; } ?>;margin-bottom:-3px;"></i>
            </span>
            <span class="nav-link-text ms-1">Exchange</span>
          </a>
        </li>
        <?php } ?>
        <?php 
            $evoucher_settingsQuery = $db->query("SELECT * FROM evoucher_settings ORDER BY id DESC LIMIT 1");
            $evoucher_settings = $evoucher_settingsQuery->fetch_assoc();
            if ($evoucher_settings["status"] == "1") { 
        ?>
        <li class="nav-item">
          <a class="nav-link  <?php if($c == "evoucher") { echo 'active'; } ?>" href="<?= $settings['url']; ?>index.php?a=account&b=money&c=evoucher">
            <span class="iconBack <?php if($c == "evoucher") { echo 'iconCurrent'; } ?> text-center me-2 d-flex align-items-center justify-content-center">
            	<i class="fa fa-gift" style="color:<?php if($c == "evoucher") { echo 'white'; } ?>;margin-bottom:-3px;"></i>
            </span>
            <span class="nav-link-text ms-1">E-Vouchers</span>
          </a>
        </li>
        <?php } ?>
        <?php if(idinfo($_SESSION['pw_uid'],"account_type") == "2") { ?>    
        <?php if ($m["payment_link"] == 1) { ?>
        <li class="nav-item">
          <a class="nav-link  <?php if($c == "link") { echo 'active'; } ?>" href="<?= $settings['url']; ?>index.php?a=account&b=money&c=link">
            <span class="iconBack <?php if($c == "link") { echo 'iconCurrent'; } ?> text-center me-2 d-flex align-items-center justify-content-center">
            	<i class="fa fa-link" style="color:<?php if($c == "link") { echo 'white'; } ?>;margin-bottom:-3px;"></i>
            </span>
            <span class="nav-link-text ms-1">Payment Links</span>
          </a>
        </li>
        <?php } ?>
        <?php } ?>
        <?php if ($m['escrow'] == 1) { ?>
        <li class="nav-item">
          <a class="nav-link  <?php if($b == "escrow") { echo 'active'; } ?>" href="<?= $settings['url']; ?>index.php?a=account&b=escrow&c=open">
            <span class="iconBack <?php if($b == "escrow") { echo 'iconCurrent'; } ?> text-center me-2 d-flex align-items-center justify-content-center">
            	<i class="fa fa-dumpster-fire" style="color:<?php if($b == "escrow") { echo 'white'; } ?>;margin-bottom:-3px;"></i>
            </span>
            <span class="nav-link-text ms-1">Escrow Payments</span>
          </a>
        </li>
        <?php } ?>
        <?php if ($m['fixed_deposit'] == 1) { ?>
        <li class="nav-item">
          <a class="nav-link  <?php if($c == "fixed_deposit") { echo 'active'; } ?>" href="<?= $settings['url']; ?>index.php?a=account&b=money&c=fixed_deposit">
            <span class="iconBack <?php if($c == "fixed_deposit") { echo 'iconCurrent'; } ?> text-center me-2 d-flex align-items-center justify-content-center">
            	<i class="fa fa-hand-holding-usd" style="color:<?php if($c == "fixed_deposit") { echo 'white'; } ?>;margin-bottom:-3px;"></i>
            </span>
            <span class="nav-link-text ms-1">Fixed Deposit</span>
          </a>
        </li>
        <?php } ?>
        <?php if ($m["referral_system"] == 1) { ?>
        <li class="nav-item">
          <a class="nav-link  <?php if($b == "ref") { echo 'active'; } ?>" href="<?php echo filter_var($settings['url']); ?>index.php?a=account&b=ref">
            <span class="iconBack <?php if($b == "ref") { echo 'iconCurrent'; } ?> text-center me-2 d-flex align-items-center justify-content-center">
            	<i class="fa fa-users" style="color:<?php if($b == "ref") { echo 'white'; } ?>;margin-bottom:-3px;"></i>
            </span>
            <span class="nav-link-text ms-1">Invite & Earn</span>
          </a>
        </li>
        <?php } ?>
      </ul>
    </div>
    <!-- <div class="sidenav-footer mx-3 mt-3 pt-3" style="height:40%;">
      <div class="card card-background shadow-none card-background-mask-secondary" id="sidenavCard">
        <div class="full-background" style="background-image: url('<?php echo filter_var($settings['url']); ?>assets/wallet/img/curved-images/white-curved.jpeg')"></div>
        <div class="card-body text-left p-3 w-100">
          <div class="icon icon-shape icon-sm bg-white shadow text-center mb-3 d-flex align-items-center justify-content-center border-radius-md">
            <i class="ni ni-headphones text-dark text-gradient text-lg top-0" aria-hidden="true" id="sidenavCardIcon"></i>
          </div>
          <h6 class="text-white up mb-0">Need help?</h6>
          <p class="text-xs font-weight-bold">Contact Our Support</p>
          <p></p>
          <a href="<?php echo filter_var($settings['url']); ?>index.php?a=account&b=supports" class="btn btn-white btn-sm w-100 mb-0">Contact Us</a>
        </div>
      </div>
    </div> -->
  </aside>