<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
?>

<div class="container-fluid py-4">
    <?php
        if(isset($_POST['purchase'])) {
    		$purchase = protect($_POST['purchase']);
    		if (empty($purchase)) {
    		    echo error("Package not selected.");
    		} elseif ($purchase == "purchase") {
    		    $id = protect($_POST['plan']);
    		    $plan_Query = $db->query("SELECT * FROM fixed_deposit_plans WHERE id='$id'");
                $plan = $plan_Query->fetch_assoc();
                
                $duration = $plan['days'];
                $status = $plan['status'];
                $rate = $plan['return_per'];
                $currency = protect($_POST['currency']);
                
                if ($settings['default_currency'] !== "$currency") {
                    $min = $plan['min_amount'];
                    $min = PW_currencyConvertor($min,$settings['default_currency'],$currency);
                    $max = $plan['max_amount'];
                    $max = PW_currencyConvertor($max,$settings['default_currency'],$currency);
                    $amount = protect($_POST['amount']);
                } else {
                    $min = $plan['min_amount'];
                    $max = $plan['max_amount'];
                    $amount = protect($_POST['amount']);
                }
                
                if (empty($amount) or empty($currency) or empty($id)) {
                    echo error("Some fields are empty");
                } elseif (!is_numeric($amount)) {
                    echo error($lang['error_7']);
                } elseif($amount<0) {
                    echo error($lang['error_7']);
                } elseif($amount == "0") {
                    echo error($lang['error_7']);
                }else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $amount)) {
                     echo error("Invalid Amount");   
                } elseif(get_wallet_balance($_SESSION['pw_uid'],$currency) < $amount) {
                    echo error("$lang[error_8] You should have $amount.");
                } elseif($amount<$min) {
                    echo error("Minimum Fixed Deposit required to be $min.");
                } elseif($amount>$max) {
                    echo error("Maximum fixed deposit required to be $max.");
                } elseif($status !== "1") {
                    echo error("Plan is not active.");
                } else {
                    $txid = strtoupper(randomHash(10));
                    $time = time();
                    $description = "Move fund to Fixed Deposit.";
                    PW_UpdateUserWallet($_SESSION['pw_uid'],$amount,$currency,2);   //User deduction
                    
                    $date = date('Y-m-d');      //Date of activation
                    $date_complete = date('Y-m-d', strtotime($date. ' + '.$duration.' days'));      //Date of completion
                    $total_return = (($amount * $rate)/100)+$amount;
                    $create_transaction = $db->query("INSERT transactions (txid,type,sender,description,amount,currency,status,created) 
                    VALUES ('$txid','51','$_SESSION[pw_uid]','$description','$amount','$currency','1','$time')");
                    
                    $insert_activity = $db->query("INSERT activity (txid,type,uid,amount,currency,status,created) 
                    VALUES ('$txid','51','$_SESSION[pw_uid]','$amount','$currency','1','$time')");
                    
                    //Create fixed deposit
                    $insert_fix_deposit = $db->query("INSERT fixed_deposits (uid,plan_id,duration,rate,amount,currency,date_activation,date_finish,txid,total_return,created_at,status) 
                    VALUES ('$_SESSION[pw_uid]','$plan[id]','$duration','$rate','$amount','$currency','$date','$date_complete','$txid','$total_return','$time','1')");
                    
                    //Status 1 will be for active investment.
                    echo success("Fixed Deposit has been activated.");
                }
    		} else {
    		    error("Package is empty.");
    		}
        }
        ?>
    <div class="collapse" id="collapseExample">
      <div class="card card-body">
        <form action="" method="POST">
            <div class="row">
                <div class="col-md">
                    <label>Enter Amount</label>
                    <input type="text" class="form-control" name="amount" placeholder="0.00" aria-label="Amount (to the nearest dollar)">
                </div>
                <div class="col-md">
                    <label>Select Currency</label>
                    <select class="form-control" name="currency">
                        <?php
        				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='1'");
    		            while($curr = $curr_Query->fetch_assoc()) {
        						
                            echo '<option value="'.$curr['currency'].'">'.$curr['currency'].'</option>';
                        }
                        ?>
                        <?php
        				$curr_Query = $db->query("SELECT * FROM currency WHERE status='1' and default_curr='2'");
    		            while($curr = $curr_Query->fetch_assoc()) {
        						
                            echo '<option value="'.$curr['currency'].'">'.$curr['currency'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md">
                    <label>Select Plan</label>
                    <select class="form-control" name="plan">
                        <?php
        				$plan_Query = $db->query("SELECT * FROM fixed_deposit_plans WHERE status='1' ORDER BY id");
    		            while($plan = $plan_Query->fetch_assoc()) {
        						
                            echo '<option value="'.$plan['id'].'">'.$plan['name'].'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <button type="submit" name="purchase" value="purchase"  class="btn bg-gradient-dark w-100 mt-4 mb-0">
              Deposit Now
            </button>
        </form>
      </div>
    </div>
    <p></p>
    <div class="row">
        <?php
        $fixed_plans = $db->query("SELECT * FROM fixed_deposit_plans WHERE status='1' ORDER BY id"); //Status 1 means Active.
        if($fixed_plans->num_rows>0) {
            while($fixed = $fixed_plans->fetch_assoc()) { ?>
              <div class="col-md-4 mb-4">
                <div class="card card-pricing">
                  <div class="card-header bg-gradient-dark text-center pt-4 pb-5 position-relative">
                    <div class="z-index-1 position-relative">
                      <h5 class="text-white"><?= $fixed['name'] ?></h5>
                      
                      <h6 class="text-white">Valid for <?= $fixed['days'] ?> Days</h6>
                    </div>
                  </div>
                  <div class="position-relative mt-n5" style="height: 50px;">
                    <div class="position-absolute w-100">
                        <svg class="waves waves-sm" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 40" preserveAspectRatio="none" shape-rendering="auto">
                          <defs>
                            <path id="card-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z"></path>
                          </defs>
                          <g class="moving-waves">
                            <use xlink:href="#card-wave" x="48" y="-1" fill="rgba(255,255,255,0.30"></use>
                            <use xlink:href="#card-wave" x="48" y="3" fill="rgba(255,255,255,0.35)"></use>
                            <use xlink:href="#card-wave" x="48" y="5" fill="rgba(255,255,255,0.25)"></use>
                            <use xlink:href="#card-wave" x="48" y="8" fill="rgba(255,255,255,0.20)"></use>
                            <use xlink:href="#card-wave" x="48" y="13" fill="rgba(255,255,255,0.15)"></use>
                            <use xlink:href="#card-wave" x="48" y="16" fill="rgba(255,255,255,0.99)"></use>
                          </g>
                        </svg>
                      </div>
                  </div>
                  <div class="card-body text-center">
                    <ul class="list-unstyled max-width-200 mx-auto">
                      <li>
                        <b><?= $settings['default_currency'] ?> <?= $fixed['min_amount'] ?></b> Minimum
                        <hr class="horizontal dark">
                      </li>
                      <li>
                        <b><?= $settings['default_currency'] ?> <?= $fixed['max_amount'] ?></b> Maximum
                        <hr class="horizontal dark">
                      </li>
                      <li>
                        <b><?= $fixed['return_per'] ?>%</b> Principle Return
                        <hr class="horizontal dark">
                      </li>
                    </ul>
                    <button class="btn bg-gradient-dark w-100 mt-4 mb-0" onclick="topFunction()" id="myBtn" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        Deposit Now
                    </button>
                  </div>
                </div>
              </div>
        <?php  } } ?>
    </div>
    <p>
      <a class="btn btn-info btn-block" data-bs-toggle="collapse" href="#view_active_fix_deposit" role="button" aria-expanded="false" aria-controls="view_active_fix_deposit">
        View Fixed Deposit History
      </a>
    </p>
    <div class="collapse" id="view_active_fix_deposit">
        <div class="card card-body">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Plan Name</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Amount</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Return Percent</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Total Return</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Start Date</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Return Date</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                    </tr>
                  </thead>
                    <tbody>
                        <?php
                        $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
                        $limit = 7;
                        $startpoint = ($page * $limit) - $limit;
                        if($page == 1) {
                            $i = 1;
                        } else {
                            $i = $page * $limit;
                        }
                        $statement = "fixed_deposits WHERE uid='$_SESSION[pw_uid]'";
                        $GetFixDeposit = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                        if($GetFixDeposit->num_rows>0) {
                        while($gfd = $GetFixDeposit->fetch_assoc()) { 
                            
                        $pid = $gfd['plan_id'];
                        $pQuery = $db->query("SELECT * FROM fixed_deposit_plans WHERE id='$pid'");
                        $pq = $pQuery->fetch_assoc();
                        ?>
                        <tr>
                          <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold"><?=$pq['name']?></span>
                          </td>
                          <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold"><?=$gfd['currency']?> <?=$gfd['amount']?></span>
                          </td>
                          <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold"><?=$gfd['rate']?>%</span>
                          </td>
                          <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold"><?=$gfd['currency']?> <?=$gfd['total_return']?></span>
                          </td>
                          <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold"><?=$gfd['date_activation']?></span>
                          </td>
                          <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold"><?=$gfd['date_finish']?></span>
                          </td>
                          <td class="align-middle text-center">
                            <?php if ($gfd['status'] == "1") { ?>
                                <span class="badge badge-sm badge-warning">waiting</span><!--1-->
                            <?php } elseif ($gfd['status'] == "2") { ?>
                                <span class="badge badge-sm badge-success">completed</span><!--2-->
                            <?php } elseif ($gfd['status'] == "3") { ?>
                                <span class="badge badge-sm badge-danger">cancelled</span> <!--3-->
                            <?php } ?>
                          </td>
                        </tr>
                    <?php } } ?>
                    </tbody>
                </table>
                <?php
                $ver = $settings['url']."account/money/fixed_deposit";
                if(web_pagination($statement,$ver,$limit,$page)) {
                    echo web_pagination($statement,$ver,$limit,$page);
                }
                ?>
            </div>
        </div>
    </div>
    <p></p>
</div>
<script>
//Get the button
var mybutton = document.getElementById("myBtn");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
    mybutton.style.display = "block";
  } else {
    mybutton.style.display = "none";
  }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}
</script>