<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
if(!checkSession()) {
    $redirect = $settings['url']."login";
    header("Location: $redirect");
}

if(idinfo($_SESSION['pw_uid'],"account_type") !== "2") {
    $redirect = $settings['url']."index.php?a=account&b=summary";
    header("Location: $redirect");
}

if ($m["payment_link"] !== "1") {
    $redirect = $settings['url']."index.php?a=account&b=summary";
    header("Location: $redirect");
}
?>
<div class="container-fluid py-4">                
    <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Payment Links
              <button type="button" class="btn btn-primary" style="float:right;" data-toggle="modal" data-target="#myModal">Create Link</button></h6>
              
            <?php
			if(isset($_POST['delete'])) {
            $FormBTN = protect($_POST['delete']);
            if($FormBTN == "delete") {
                
                $hash = protect($_POST['hash']);
                Delete_Payment_Link($hash);
                echo "<br><br>";
                echo  info("Payment Link Deleted.");
            }
			}
            ?>
            </div>
            <!-- The Modal -->
            <div class="modal" id="myModal">
              <div class="modal-dialog">
                <div class="modal-content">
            
                  <!-- Modal Header -->
                  <div class="modal-header">
                    <h4 class="modal-title">Payment Link</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                    
                  <!-- Modal body -->
                  <div class="modal-body">
                      <div class="overflow-hidden position-relative border-radius-lg bg-cover h-100" style="background-image: url('<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>assets/miltipay/img/ivancik.jpg');">
                          <span class="mask bg-gradient-dark"></span>
                          <div class="card-body position-relative z-index-1 d-flex flex-column h-100 p-3">
                            <?php
							if(isset($_POST['pw_link'])) {
                            $FormBTN = protect($_POST['pw_link']);
                            if($FormBTN == "create") {
                                
                                $hash = strtoupper(randomHash(30));
                                $time = time();
                                $merchant_account = protect($_POST['merchant_account']);
                                $item_number = protect($_POST['item_number']);
                                $item_name = protect($_POST['item_name']);
                                $item_price = protect($_POST['item_price']);
                                $item_currency = protect($_POST['item_currency']);
                                $return_success = protect($_POST['return_success']);
                                $return_fail = protect($_POST['return_fail']);
                                $return_cancel = protect($_POST['return_cancel']);
                                
                                $merchant_id = PW_GetUserID($merchant_account);
                                if($merchant_id==false) {
                                    echo  error("Merchant does not exists.");
                                } elseif(idinfo($merchant_id,"account_type") !== "2") {
                                    echo  error("$merchant_account cannot accept payments. Only Business accounts can accept payments.");
                                } elseif(empty($item_number) or empty($item_name) or empty($item_price) or empty($item_currency) or empty($return_success) or empty($return_fail) or empty($return_cancel)) {
                                    echo  error("Some data was missing.");
                                } else {
                                    Create_Payment_Link($merchant_account,$hash,$item_number,$item_name,$item_price,$item_currency,$return_success,$return_fail,$return_cancel,$time);
                                    $result = success("Payment Link Generated.");
                                    echo  $result;
                                }
                            }
                            }
                            ?>
                        <form class="user-connected-from user-login-form" action="" method="POST">
                            <div class="row">
                                <div class="col">
                                    <div class="input-group" style="height:45px;">
                                        <input type="text" class="form-control" id="" name="item_name" placeholder="Link Name">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <input type="number"  style="height:45px;" class="form-control" name="item_price" placeholder="0.00" aria-label="Amount (to the nearest dollar)" required minimum="1.00" step="0.01">
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="height:45px;float:right;">
                                                <select class="form-control" name="item_currency" required style="height:36px;">
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
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            
                            
                        <input type="hidden" name="item_number" value="1">
                        <input type="hidden" name="merchant_account" value="<?= idinfo($_SESSION['pw_uid'],"email"); ?>">
                        <input type="hidden" name="return_success" value="<?= $settings['url']; ?>link/success">
                        <input type="hidden" name="return_fail" value="<?= $settings['url']; ?>link/fail">
                        <input type="hidden" name="return_cancel" value="<?= $settings['url']; ?>link/fail">
                        <br>
                        <button type="submit" name="pw_link" value="create" class="btn btn-info" style="float:right;">Create Link</button>
                    </form>
                  </div>
            </div>
          </div>
        
                  <!-- Modal footer -->
                  
            
                </div>
              </div>
            </div>
            
            <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center justify-content-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width:10%;">Page Name</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width:6%;">Amount</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width:10%;">Created On</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"  style="width:74%;">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                      <?php
                        $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
                        $limit = 15;
                        $startpoint = ($page * $limit) - $limit;
                        if($page == 1) {
                            $i = 1;
                        } else {
                            $i = $page * $limit;
                        }
                        
						$statement = "payment_link WHERE user_id='$_SESSION[pw_uid]'";
						$query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                        
                        if($query->num_rows>0) {
                            while($row = $query->fetch_assoc()) {
                                ?>
                            <tr>
                              <td class="align-middle text-center text-sm"><?php echo filter_var($row['item_name'], FILTER_SANITIZE_STRING); ?></td>
                              <td class="align-middle text-center text-sm">
                                <span class="text-xs font-weight-bold"><?php echo filter_var($row['item_price'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($row['item_currency'], FILTER_SANITIZE_STRING); ?></span>
                              </td>
                              <td class="align-middle text-center text-sm"><?php echo filter_var(date("d M Y H:i",$row['time']), FILTER_SANITIZE_STRING); ?></td>
                              <td>
                                <div class="align-middle text-center text-sm">
                                    <form action="" method="POST">
                                    <input type="hidden" name="hash" value="<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>">
                                    <input type="text" value="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>link/<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>" id="myInput<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>" style="width:35%;background:none;border:none;">
                                    	&nbsp;	&nbsp;	&nbsp;	&nbsp;	&nbsp;
                                    <button type="button" class="btn btn-info text-xs" onclick="my<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>Function()" onmouseout="out<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>Func()"><span class="tooltiptext" id="myTooltip<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>">Copy</span></button>
                                    <a target="_blank" href="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>link/<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>" class="btn btn-secondary text-xs">View</a>
                                    <button type="submit" name="delete" value="delete" class="btn btn-danger">Delete</button>
                                    </form>
                                    <script>
                                    function my<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>Function() {
                                      var copyText = document.getElementById("myInput<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>");
                                      copyText.select();
                                      copyText.setSelectionRange(0, 99999);
                                      document.execCommand("copy");
                                      
                                      var tooltip = document.getElementById("myTooltip<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>");
                                      tooltip.innerHTML = "Copied!";
                                    }
                                    
                                    function outFunc() {
                                      var tooltip = document.getElementById("myTooltip<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>");
                                      tooltip.innerHTML = "Copy";
                                    }
                                    </script>
                                </div>
                              </td>
                            </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="6"><center>You have no Payment Link yet.</center></td></tr>';
                        }
                        ?>
                  </tbody>
                </table>
                <center>
                <?php
                
				$ver = $settings['url']."index.php?a=account&b=money&c=link";
				if(web_pagination($statement,$ver,$limit,$page)) {
					echo web_pagination($statement,$ver,$limit,$page);
				}
                
                ?>
                </center>
              </div>
            </div>
          </div>
        </div>
      </div>
</div>
