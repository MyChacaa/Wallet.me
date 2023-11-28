<?php
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
$id = protect($_GET['id']);
$query = $db->query("SELECT * FROM users WHERE id='$id'");
if($query->num_rows==0) { header("Location: ./?a=users"); }
$row = $query->fetch_assoc();
?>

        
        
<div class="content mt-3">
    <div class="col-md-6">
        <?php
			if(isset($_POST['btn_save'])) {
            $FormBTN = protect($_POST['btn_save']);
            if($FormBTN == "profile") {
                $amount = protect($_POST['amount']);
                $amount = number_format($amount, 2, '.', '');
                $currency = protect($_POST['currency']);
                $txid = strtoupper(randomHash(10));
                $time = time();
                $description = protect($_POST['note']);
                $type = protect($_POST['type']);
                if ($type == '1') {  // ADD
                    PW_UpdateUserWallet($id,$amount,$currency,1); //Wallet Add
                    $create_transaction = $db->query("INSERT transactions (txid,type,sender,description,amount,currency,fee,status,created) VALUES ('$txid','28','$_SESSION[pw_uid]','$description','$amount','$currency','0.00','1','$time')");
                    $insert_activity = $db->query("INSERT activity (txid,type,uid,amount,currency,status,created) VALUES ('$txid','28','$_SESSION[pw_uid]','$amount','$currency','1','$time')");
                    echo success("Fund has been Added.");  
                } elseif ($type == '2') { // Subtract
                    PW_UpdateUserWallet($id,$amount,$currency,2); //Wallet Deduction
                    $create_transaction = $db->query("INSERT transactions (txid,type,sender,description,amount,currency,fee,status,created) VALUES ('$txid','29','$id','$description','$amount','$currency','0.00','1','$time')");
                    $insert_activity = $db->query("INSERT activity (txid,type,uid,amount,currency,status,created) VALUES ('$txid','29','$id','$amount','$currency','1','$time')");
                    echo success("Fund has been Deducted.");
                }
                
                
            }
			}
    
        ?>
		<div class="card">
		    <div class="card-body">
                <h3>Manual Transaction</h3>
                <hr/>
                <form action="" method="POST">
                    <div class="form-group">
    					<label>Enter Amount</label>
    					<input type="text" class="form-control" name="amount" required>
                    </div>
                    <div class="form-group">
    					<label>Select Currency</label>
    					<select class="form-control" name="currency" required>
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
                    <div class="form-group">
                        <label>Add/Deduct</label>
                        <select class="form-control" name="type" required>
                            <option value="1">Add Fund</option>
                            <option value="2">Deduct Fund</option>
                        </select>
                    </div>
                    <div class="form-group">
    					<label>Description</label>
    					<input type="text" class="form-control" name="note" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="btn_save" value="profile"><i class="fa fa-check"></i>Add Transaction</button>
	            </form>
            </div>
        </div>
    </div>
</div>