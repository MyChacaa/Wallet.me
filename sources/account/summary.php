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
<div class="container-fluid py-4">
        
    <div class="row">
        
        <?php
        $GetUserWallets = $db->query("SELECT * FROM users_wallets WHERE uid='$_SESSION[pw_uid]' ORDER BY id");
        if($GetUserWallets->num_rows>0) {
            while($guw = $GetUserWallets->fetch_assoc()) { ?>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4" style="margin-top:4px;">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold"><?php echo filter_var($guw['currency'], FILTER_SANITIZE_STRING)?>  Balance</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?php echo filter_var(get_wallet_balance($_SESSION['pw_uid'],$guw['currency']), FILTER_SANITIZE_STRING)?>
                                    <span class="text-success text-sm font-weight-bolder"></span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <img src="<?= $settings['url'] ?>assets/flag/<?=$guw['currency']?>.png" style="width:50%;">
                            
                            <div class="dropdown pe-2">
                                <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                                  <i class="fa fa-ellipsis-h text-secondary"></i>
                                </a>
                                <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                                    <?php if ($m["deposit"] == 1) { ?>
                                        <li><a class="dropdown-item border-radius-md text-success" href="<?= $settings['url']; ?>index.php?a=account&b=money&c=deposit"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;Deposit Fund</a></li>
                                    <?php } ?>
                                    <?php if ($m["withdrawal"] == 1) { ?>
                                        <li><a class="dropdown-item border-radius-md text-info" href="<?= $settings['url']; ?>index.php?a=account&b=money&c=withdrawal"><i class="fa fa-minus-circle"></i>&nbsp;&nbsp;Withdraw Fund</a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        
            }
        }
        ?>
    </div>
      <?php
        $GetUserRequests = $db->query("SELECT * FROM requests WHERE uid='$_SESSION[pw_uid]' and status='1'");
        if($GetUserRequests->num_rows>0) {
      ?>
    <div class="row my-4">
        <div class="col">
          <div class="card">
            <div class="card-header pb-0">
              <div class="row">
                <div class="col">
                  <h6>Request Money</h6>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                        <?php while($gur = $GetUserRequests->fetch_assoc()) { ?>
                        <tr>
                                <td><?php if(idinfo($gur['fromu'],"account_type") == "1") { echo idinfo($gur['fromu'],"first_name")." ".idinfo($gur['fromu'],"last_name"); } else { echo idinfo($gur['fromu'],"business_name"); } ?> request <?php echo filter_var($gur['amount']." ".$gur['currency'], FILTER_SANITIZE_STRING); ?> from you.<br/>Description: <?php echo filter_var($gur['description'], FILTER_SANITIZE_STRING); ?></td>
                                <td>
                                    <a href="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>index.php?a=account&b=money&c=request&d=pay$e=<?php echo filter_var($gur['id'], FILTER_SANITIZE_STRING); ?>" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Pay</a> 
                                    <a href="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>index.php?a=account&b=money&c=request&d=cancel$e=<?php echo filter_var($gur['id'], FILTER_SANITIZE_STRING); ?>" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Cancel</a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
          </div>
        </div>
    </div>
                                            
                                        
                        <?php
                    }
                    ?>
      
      <div class="row my-4">
        <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
          <div class="card">
            <div class="card-header pb-0">
              <div class="row">
                <div class="col-lg-6 col-7">
                  <h6>Recent Activity</h6>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    <?php
                        $GetUserActivity = $db->query("SELECT * FROM activity WHERE uid='$_SESSION[pw_uid]' ORDER BY id DESC LIMIT 7");
                        if($GetUserActivity->num_rows>0) {
                            while($gua = $GetUserActivity->fetch_assoc()) {
                                $amount = $gua['amount'];
                                if($gua['type'] == "2" or $gua['type'] == "4" or $gua['type'] == "6" or $gua['type'] == "7" or $gua['type'] == "8" or $gua['type'] == "29" or $gua['type'] == "41"  or $gua['type'] == "45"  or $gua['type'] == "51" or $gua['type'] == "62") {
                                    $amount = '-'.$amount;
                                } else {
                                    $amount = '+'.$amount;
                                } ?>
                    
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div>
                            <small><?= PW_ActivityDate($gua['created']) ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</small>
                          </div>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?php echo filter_var(PW_DecodeUserActivity($gua['id']), FILTER_SANITIZE_STRING) ?></h6>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold"> <?php echo filter_var($amount.' '.$gua['currency'], FILTER_SANITIZE_STRING) ?> </span>
                      </td>
                      <td class="align-middle">
                        <center><span class="text-xs font-weight-bold"> <?php echo PW_DecodeTXStatus($gua['status']) ?> </span></center>
                      </td>
                      <td>
                        <div class="avatar-group mt-2 text-xs">
                          <a href="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>index.php?a=account&b=transaction&id=<?php echo filter_var($gua['txid'], FILTER_SANITIZE_STRING) ?>" class="btn btn-primary text-xs">View</a>
                        </div>
                      </td>
                   </tr>
                   
                   <?php    }
                        } else { 
                            echo '<tr>
                                <td>'.$lang['info_8'].'</td>
                            </tr>';
                        }
                        ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="card h-100">
            <div class="card-header pb-0">
              <h6>How to get started?</h6>
            </div>
            <div class="card-body p-3">
              <div class="timeline timeline-one-side">
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-bell-55 text-success text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Deposit Fund</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">any amount you can deposit in your wallet for your use.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-send text-danger text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Send/Request money</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Send fund or Request fund from your friends.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-world text-info text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Exchange eCurrencies</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Exchange your ecurrencies by deposit and making withdraw with multiple ewallets.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-credit-card text-warning text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Activate Prepaid Credit Card</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Activate your prepaid debit and use it anywhere online.</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-cart text-info text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Use Prepaid Card</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Use your Prepaid Credit Card on Paypal, Go for shopping anywhere, you can use our card anywhere online were VISA cards are accepted.
                    you cannot use our card for Gambling, Money Transfer & Harrasement Use.</p>
                  </div>
                </div>
                
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-key-25 text-primary text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Share & Like our Profiles</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Like and Share our website on Facebook and any other social media site.</p>
                  </div>
                </div>
                <div class="timeline-block">
                  <span class="timeline-step">
                    <i class="ni ni-money-coins text-dark text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Want to Earn? Make Referrals</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Join Affliate Program and earn money. </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </div>