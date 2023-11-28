<?php
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
if(isset($_GET['b'])) {
$b = protect($_GET['b']);

if($b == "edit") {
	$id = protect($_GET['id']);
	$query = $db->query("SELECT * FROM users WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=users"); }
	$row = $query->fetch_assoc();
	?>
	<div class="row">
            <div class="col-md-12">
            <?php
			if(isset($_POST['btn_save'])) {
            $FormBTN = protect($_POST['btn_save']);
            if($FormBTN == "profile") {
                $first_name = protect($_POST['first_name']);
                $last_name = protect($_POST['last_name']);
                $account_type = protect($_POST['account_type']);
                $business_name = protect($_POST['business_name']);
                $email = protect($_POST['email']);
                $country = protect($_POST['country']);
                $city = protect($_POST['city']);
                $zip_code = protect($_POST['zip_code']);
                $address = protect($_POST['address']);
                $status = protect($_POST['status']);
                if(isset($_POST['email_verified'])) { $email_verified = 1; } else { $email_verified = 0; }
                if(isset($_POST['document_verified'])) { $document_verified =1; } else { $document_verified=0; }
                $CheckEmail = $db->query("SELECT * FROM users WHERE email='$email'");
                if(empty($first_name) or empty($last_name) or empty($account_type) or empty($email) or empty($country) or empty($city) or empty($zip_code) or empty($address) or empty($status)) {
                    echo error("All fields are required.");
                } elseif(!isValidEmail($email)) {
                    echo error("Please enter valid email address.");
                } elseif($row['email'] !== $email && $CheckEmail->num_rows>0) { 
                    echo error("This email address is already used.");
                } elseif($country !== "United Kingdom" && !is_numeric($zip_code)) { 
                    echo error("Invalid Zip Code.");
                } elseif($country == "United Kingdom" && postcode_check($zip_code) == false) {
                    echo error("Invalid Zip Code.");	
                } else {
                    $update = $db->query("UPDATE users SET first_name='$first_name',last_name='$last_name',account_type='$account_type',business_name='$business_name',email='$email',country='$country',city='$city',zip_code='$zip_code',address='$address',status='$status',email_verified='$email_verified',document_verified='$document_verified' WHERE id='$row[id]'");
                    if(!empty($_POST['newpass'])) {
                        $password = protect($_POST['newpass']);
                        $password = password_hash($password, PASSWORD_DEFAULT);
                        $update = $db->query("UPDATE users SET password='$password' WHERE id='$row[id]'");
                    }
                    $query = $db->query("SELECT * FROM users WHERE id='$row[id]'");
                    $row = $query->fetch_assoc();
                    echo success("Profile changes was saved successfully.");
                }
            }
			}
			if(isset($_POST['balance'])) {
            if($FormBTN == "balance") {
                foreach($_POST['balance'] as $k => $v) {
                    $update = $db->query("UPDATE users_wallets SET amount='$v' WHERE id='$k'");
                }
                echo success("Balance was updated successfully.");
            }
			}
            ?>
            </div>
           <div class="col-md-8">
				<div class="card">
				    <div class="card-header">
                        <strong class="card-title">Profile <b>Information</b></strong>
                    </div>
                    <div class="card-body">
                         
			
			<form action="" method="POST">
            <div class="form-group">
					<label>First name</label>
					<input type="text" class="form-control" name="first_name" value="<?php echo filter_var($row['first_name'], FILTER_SANITIZE_STRING); ?>">
                </div>
                <div class="form-group">
					<label>Last name</label>
					<input type="text" class="form-control" name="last_name" value="<?php echo filter_var($row['last_name'], FILTER_SANITIZE_STRING); ?>">
                </div>
                <div class="form-group">
                    <label>Account type</label>
                    <select class="form-control" name="account_type">
                        <option value="1" <?php if($row['account_type'] == "1") { echo 'selected'; } ?>>Personal</option>
                        <option value="2" <?php if($row['account_type'] == "2") { echo 'selected'; } ?>>Business</option>
                    </select>
                </div>
                <div class="form-group">
					<label>Business name</label>
					<input type="text" class="form-control" name="business_name" value="<?php echo filter_var($row['business_name'], FILTER_SANITIZE_STRING); ?>">
                </div>
                <div class="form-group">
					<label>Email address</label>
					<input type="text" class="form-control" name="email" value="<?php echo filter_var($row['email'], FILTER_SANITIZE_STRING); ?>">
                </div>
                <div class="form-group">
					<label>Country</label>
					<select class="form-control" name="country">
                        <?php
        				$country_Query = $db->query("SELECT * FROM country WHERE status='1'");
    		            while($country = $country_Query->fetch_assoc()) {
                            if($row['country'] == $country['code']) { $sel = 'selected'; } else { $sel = ''; } 
                            echo '<option value="'.$country['code'].'" '.$sel.'>'.$country['name'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
					<label>City</label>
					<input type="text" class="form-control" name="city" value="<?php echo filter_var($row['city'], FILTER_SANITIZE_STRING); ?>">
                </div>
                <div class="form-group">
					<label>Zip Code</label>
					<input type="text" class="form-control" name="zip_code" value="<?php echo filter_var($row['zip_code'], FILTER_SANITIZE_STRING); ?>">
                </div>
                <div class="form-group">
					<label>Address</label>
					<input type="text" class="form-control" name="address" value="<?php echo filter_var($row['address'], FILTER_SANITIZE_STRING); ?>">
				</div>
				<div class="form-group">
					<label>New password</label>
					<input type="text" class="form-control" name="newpass" placeholder="Leave empty if do not want to change it.">
				</div>
				<div class="form-group">
					<label>Status</label>
					<select class="form-control" name="status">
						<option value="1" <?php if($row['status'] == "1") { echo 'selected'; } ?>>Active</option>
						<option value="11" <?php if($row['status'] == "11") { echo 'selected'; } ?>>Blocked</option>
					</select>
				</div>
				<div class="checkbox">
					<label>
					  <input type="checkbox" name="email_verified" value="yes" <?php if($row['email_verified'] == "1") { echo 'checked'; }?>> Email verified
					</label>
				</div>
				 <div class="checkbox">
					<label>
					  <input type="checkbox" name="document_verified" value="yes" <?php if($row['document_verified'] == "1") { echo 'checked'; }?>> Document verified
					</label>
				  </div>
				<button type="submit" class="btn btn-primary" name="btn_save" value="profile"><i class="fa fa-check"></i> Save changes</button>
			</form>
		</div>
        </div>
        
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Balances</strong>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <?php
                    $GetBalances = $db->query("SELECT * FROM users_wallets WHERE uid='$row[id]'");
                    if($GetBalances->num_rows>0) {
                        while($balance = $GetBalances->fetch_assoc()) {
                            ?>
                            <div class="input-group mb-3">
                            <input type="text" class="form-control" name="balance[<?php echo filter_var($balance['id'], FILTER_SANITIZE_STRING); ?>]" value="<?php echo filter_var($balance['amount'], FILTER_SANITIZE_STRING); ?>">
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2"><?php echo filter_var($balance['currency'], FILTER_SANITIZE_STRING); ?></span>
                            </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                    <button type="submit" class="btn btn-primary" name="btn_save" value="balance">Update</button>
                </form>
            </div>
        </div>
    
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Users <b>Role</b></strong>
            </div>
            <div class="card-body">
                <?php
				if(isset($_POST['btn_save'])) {
                $FormBTN = protect($_POST['btn_save']);
                if($FormBTN == "role") {
                $account_level = protect($_POST['account_level']);
                $account_user = protect($_POST['account_user']);
                $update = $db->query("UPDATE users SET account_user='$account_user',account_level='$account_level' WHERE id='$row[id]'");
                $query = $db->query("SELECT * FROM users WHERE id='$row[id]'");
                $row = $query->fetch_assoc();
                echo success("Users Roles changes was saved successfully.");
                }
				
                if($FormBTN == "login") {
                $email = $row['email'];
                $CheckLogin = $db->query("SELECT * FROM users WHERE email='$email'");
                if($CheckLogin->num_rows==0) {
                    echo error("Error.");
                } else {
                    $login = $CheckLogin->fetch_assoc();
                    
                        
                    $_SESSION['pw_uid'] = $login['id'];
                    $last_login = $login['last_login']+5000;
                    if(time() > $last_login) {
                        $time = time();
                    }
                    $time = time();
                    $login_ip = $_SERVER['REMOTE_ADDR'];
                    if($_SESSION['pw_payorder_url']) {
                        $redirect = $_SESSION['pw_payorder_url'];
                        header("Location: $redirect");   
                    } else {
                        $redirect = $settings['url']."account/summary";
                        header("Location: $redirect");
                    }
                            
                }
                }
				}
                ?>
                <form action="" method="POST">
        			<div class="form-group">
    					<label>Account Level</label>
    					<select class="form-control" name="account_level">
    						<option value="666" <?php if($row['account_level'] == "666") { echo 'selected'; } ?>>Admin</option>
    						<option value="0" <?php if($row['account_level'] == "0" or $row['account_level'] == "") { echo 'selected'; } ?>>User</option>
    					</select>
                    </div>
                    <div class="form-group">
    					<label>Admin Username</label>
    					<input class="form-control" name="account_user" value="<?php echo filter_var($row['account_user'], FILTER_SANITIZE_STRING); ?>" >
    					<small>This is for admin access only. Don't enter any username for users.</small>
                    </div>
                    <button type="submit" class="btn btn-primary" name="btn_save" value="role" style="width:100%;"><i class="fa fa-check"></i> Save changes</button>
                </form>
                <form action="" target="_blank" method="POST">
                    <hr>
        		    <button type="submit" class="btn btn-primary" name="btn_save" value="login" style="width:100%;"><i class="fa fa-eye"></i> Login to user account</button>
                </form>
                <a href="./?a=manual_transactions&id=<?php echo $id;?>"><button class="btn btn-primary btn-block" >Manual Transaction</button></a><p></p>
                <a href="./?a=all_user_activity&id=<?php echo $id;?>"><button class="btn btn-primary btn-block" >All Activity</button></a><p></p>
        	</div>
        </div>
    </div>
    </div>
    <div class="col-md-14">
    <?php 
        $GetDocuments = $db->query("SELECT * FROM users_documents WHERE uid='$row[id]' ORDER BY id");
        if($GetDocuments->num_rows>0) {
            ?>
            <div class="card" id="documents">
            <div class="card-body">
                <h3>Documents</h3>
                <br/>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Document Type</th>
                            <th>Document Number</th>
                            <th>Status</th>
                            <th>Comment</th>
                            <th>Attached file</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while($doc = $GetDocuments->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php if($doc['document_type'] == "1") { echo 'Personal ID'; } elseif($doc['document_type'] == "2") { echo 'National Passport'; } elseif($doc['document_type'] == "3") { echo 'Driving License'; } elseif($doc['document_type'] == "4") { echo 'Invoice'; } else { echo 'Unknown'; } ?></td>
                                <td><?php echo filter_var($doc['u_field_1'], FILTER_SANITIZE_STRING); ?></td>
                                <td>
                                    <?php
                                    if($doc['status'] == "1") { echo '<span class="badge badge-warning">Pending</span>'; }
                                    elseif($doc['status'] == "2") { echo '<span class="badge badge-danger">Rejected</span>'; } 
                                    elseif($doc['status'] == "3") { echo '<span class="badge badge-success">Accepted</span>'; }
                                    else {
                                        echo '<span class="badge badge-default">Unknown</span>';
                                    }
                                    ?>
                                </td>
                                <td><?php echo filter_var($doc['u_field_5'], FILTER_SANITIZE_STRING); ?></td>
                                <td><a href="<?php echo filter_var($settings['url'].$doc['document_path'], FILTER_SANITIZE_STRING); ?>" target="_blank"><span class="badge badge-primary"><i class="fa fa-search"></i> Preview</span></a><br/><small>Uploaded on <?php echo date("d/m/Y H:i:s",$doc['uploaded']); ?></td>
                                <td>
                                    <?php if($doc['status'] == "1") { ?>
                                    <a href="./?a=users&b=documents&c=accept&uid=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>&did=<?php echo filter_var($doc['id'], FILTER_SANITIZE_STRING); ?>"><span class="badge badge-success"><i class="fa fa-check"></i> Accept</span></a> 
                                    <a href="./?a=users&b=documents&c=reject&uid=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>&did=<?php echo filter_var($doc['id'], FILTER_SANITIZE_STRING); ?>"><span class="badge badge-danger"><i class="fa fa-times"></i> Reject</span></a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            </div>
            <?php
        }
        ?>
                </div>
	<?php
} elseif($b == "documents") {
    $id = protect($_GET['uid']);
    $query = $db->query("SELECT * FROM users WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=users"); }
	$row = $query->fetch_assoc();
    $c = protect($_GET['c']);
    $did = protect($_GET['did']);
    if($c == "accept") {
        $check = $db->query("SELECT * FROM users_documents WHERE uid='$id' and id='$did'");
        if($check->num_rows>0) {
            $update = $db->query("UPDATE users_documents SET status='3' WHERE id='$did'");
            $redirect = './?a=users&b=edit&id='.$id.'#documents'; 
            header("Location: $redirect");
        } else {
            $redirect = './?a=users&b=edit&id='.$id.'#documents'; 
            header("Location: $redirect");
        }
    } elseif($c == "reject") {
        $check = $db->query("SELECT * FROM users_documents WHERE uid='$id' and id='$did'");
        if($check->num_rows==0) {
            $redirect = './?a=users&b=edit&id='.$id.'#documents'; 
            header("Location: $redirect");
        }
        $doc = $check->fetch_assoc();
        ?>
        

           <div class="col-md-12">
					<div class="card">
                        <div class="card-body">

                        <?php
                        $FormBTN = protect($_POST['btn_reject']);
                        if($FormBTN == "document") {
                            $comment = protect($_POST['comment']);
                            if(empty($comment)) {
                                echo error("Please provide a reason for rejection.");
                            } else {
                                $update = $db->query("UPDATE users_documents SET status='2',u_field_5='$comment' WHERE id='$doc[id]'");
                                $redirect = './?a=users&b=edit&id='.$id.'#documents'; 
                                header("Location: $redirect");
                            }
                        }
                        ?>

                        <form action="" method="POST">
                            <div class="form-group">
                                <label>Reason for rejection</label>
                                <textarea class="form-control" name="comment" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" name="btn_reject" value="document">Reject</button>
                        </form>
                        </div>
                    </div>
            </div>
        <?php
    } else {
        header("Location: ./?a=users&b=edit&id=$id");
    }
} elseif($b == "delete") {
	$id = protect($_GET['id']);
	$query = $db->query("SELECT * FROM users WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=users"); }
	$row = $query->fetch_assoc();
	?>
	

           <div class="col-md-12">
					<div class="card">
                        <div class="card-body">
			<?php
			if(isset($_GET['confirm'])) {
				$delete = $db->query("DELETE FROM users WHERE id='$row[id]'");
				$delete = $db->query("DELETE FROM users_wallets WHERE uid='$row[id]'");
				$delete = $db->query("DELETE FROM transactions WHERE sender='$row[id]' or recipient='$row[id]'");
                $delete = $db->query("DELETE FROM activity WHERE uid='$row[id]'");
                $delete = $db->query("DELETE FROM users_documents WHERE uid='$row[id]'");
                $delete = $db->query("DELETE FROM users_logs WHERE uid='$row[id]'");
                $delete = $db->query("DELETE FROM requests WHERE uid='$row[id]' or fromu='$row[id]'");
                $delete = $db->query("DELETE FROM withdrawals_values WHERE uid='$row[id]'");
                $delete = $db->query("DELETE FROM deposits WHERE uid='$row[id]'");
                $delete = $db->query("DELETE FROM disputes WHERE sender='$row[id]' or recipient='$row[id]'");
                $delete = $db->query("DELETE FROM disputes_messages WHERE uid='$row[id]'");
				echo success("User <b>$row[email]</b> was deleted.");
			} else {
				echo info("Are you sure you want to delete user <b>$row[email]</b>?<br/><small>Once this action is completed, user information will be deleted from the database and will not be restored.</small>");
				echo '<a href="./?a=users&b=delete&id='.$row['id'].'&confirm=1" class="btn btn-success"><i class="fa fa-check"></i> Yes</a>&nbsp;&nbsp;
					<a href="./?a=users" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
			}
			?>
		</div>
	</div>
	</div>
	<?php
} } else {
?>


            <div class="col-md-12">
				<div class="card">
                    <div class="card-body">
                        <form action="" method="POST">
                        <div class="row">
                        <div class="col-md-3" style="padding:10px;">
                                <input type="text" class="form-control" name="first_name" placeholder="First name" value="<?php if(isset($_POST['first_name'])) { echo filter_var($_POST['first_name'], FILTER_SANITIZE_STRING); } ?>">
                            </div>
                            <div class="col-md-3" style="padding:10px;">
                                <input type="text" class="form-control" name="last_name" placeholder="Last name" value="<?php if(isset($_POST['last_name'])) { echo filter_var($_POST['last_name'], FILTER_SANITIZE_STRING); } ?>">
                            </div>
                            <div class="col-md-3" style="padding:10px;">
                                <input type="text" class="form-control" name="business_name" placeholder="Business name" value="<?php if(isset($_POST['business_name'])) { echo filter_var($_POST['business_name'], FILTER_SANITIZE_STRING); } ?>">
                            </div>
                            <div class="col-md-3" style="padding:10px;">
                                <input type="text" class="form-control" name="email" placeholder="Email address" value="<?php if(isset($_POST['email'])) { echo filter_var($_POST['email'], FILTER_SANITIZE_STRING); } ?>">
                            </div>
                            <div class="col-md-2" style="padding:10px;">
                                <select class="form-control" name="country">
                                    <option value="">Country</option>
                                <option></option>
                                <?php
                                $countries = getCountries();
                                foreach($countries as $code=>$country) {
                                    $sel='';
                                    if(isset($_POST['country'])) { if($_POST['country'] == $country) { $sel = 'selected'; } }
                                    echo '<option value="'.$country.'" '.$sel.'>'.$country.'</option>';
                                }
                                ?>
                                </select>
                            </div>
                            <div class="col-md-2" style="padding:10px;">
                                <input type="text" class="form-control" name="city" placeholder="City" value="<?php if(isset($_POST['city'])) { echo filter_var($_POST['city'], FILTER_SANITIZE_STRING); } ?>">
                            </div>
                            <div class="col-md-2" style="padding:10px;">
                                <input type="text" class="form-control" name="zip_code" placeholder="Zip Code" value="<?php if(isset($_POST['zip_code'])) { echo filter_var($_POST['zip_code'], FILTER_SANITIZE_STRING); } ?>">
                            </div>
                            <div class="col-md-3" style="padding:10px;">
                                <input type="text" class="form-control" name="ip" placeholder="IP Address" value="<?php if(isset($_POST['ip'])) { echo filter_var($_POST['ip'], FILTER_SANITIZE_STRING); } ?>">
                            </div>
                            <div class="col-md-3" style="padding:10px;">
                                <button type="submit" class="btn btn-primary btn-block" name="btn_search" value="users">Search</button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

           <div class="col-md-12">
					<div class="card">
                        <div class="card-body table-responsive">
                            
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="25%">Email</th>
                                    <th width="15%">IP</th>
                                    <th width="15%">Email verified</th>
                                    <th width="18%">Document verified</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $searching=0;
								if(isset($_POST['btn_search'])) {
                                $FormBTN = protect($_POST['btn_search']);
                                if($FormBTN == "users") {
                                    $searching=1;
                                    $search_query = array();
                                    $s_first_name = protect($_POST['first_name']);
                                    if(!empty($s_first_name)) { $search_query[] = "first_name='$s_first_name'"; }
                                    $s_last_name = protect($_POST['last_name']);
                                    if(!empty($s_last_name)) { $search_query[] = "last_name='$s_last_name'"; }
                                    $s_business_name = protect($_POST['business_name']);
                                    if(!empty($s_business_name))  { $search_query[] = "business_name='$s_business_name'"; }
                                    $s_email = protect($_POST['email']);
                                    if(!empty($s_email)) { $search_query[] = "email='$s_email'"; }
                                    $s_country = protect($_POST['country']);
                                    if(!empty($s_country)) { $search_query[] = "country='$s_country'"; }
                                    $s_city = protect($_POST['city']);
                                    if(!empty($s_city)) { $search_query[] = "city='$s_city'"; }
                                    $s_zip_code = protect($_POST['zip_code']);
                                    if(!empty($s_zip_code)) { $search_query[] = "zip_code='$s_zip_code'"; }
                                    $s_ip = protect($_POST['ip']);
                                    if(!empty($s_ip)) { $search_query[] = "ip='$s_ip'"; }
                                    $p_query = implode(" and ",$search_query);
                                }
								}
                                $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
                                $limit = 20;
                                $startpoint = ($page * $limit) - $limit;
                                if($page == 1) {
                                    $i = 1;
                                } else {
                                    $i = $page * $limit;
                                }
                                $statement = "users";
                                if($searching==1) {
                                    if(empty($p_query)) {
                                        $qry = 'empty query';
                                    }
                                    $query = $db->query("SELECT * FROM {$statement} WHERE $p_query ORDER BY id");
                                } else {
                                    $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                                }
                                if($query->num_rows>0) {
                                    while($row = $query->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td><?php echo filter_var($row['email'], FILTER_SANITIZE_STRING); ?></td>
                                            <td><?php echo filter_var($row['ip'], FILTER_SANITIZE_STRING); ?></td>
                                            <td>
                                                <?php
                                                if($row['email_verified'] == "1") {
                                                    echo '<span class="badge badge-success"><i class="fa fa-check"></i> Yes</span>';
                                                } else {
                                                    echo '<span class="badge badge-danger"><i class="fa fa-times"></i> No</span>';
                                                }
                                                ?>
                                            </td><td>
                                                <?php
                                                if($row['document_verified'] == "1") {
                                                    echo '<span class="badge badge-success"><i class="fa fa-check"></i> Yes</span>';
                                                } else {
                                                        echo '<span class="badge badge-danger"><i class="fa fa-times"></i> No</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <a href="./?a=users&b=edit&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="Edit"><span class="badge badge-primary"><i class="fa fa-pencil"></i> Edit</span></a> 
                                                <a href="./?a=users&b=delete&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="Delete"><span class="badge badge-danger"><i class="fa fa-trash"></i> Delete</span></a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    if($searching == "1") {
                                        echo '<tr><td colspan="5">No found results.</td></tr>';
                                    } else {
                                        echo '<tr><td colspan="5">No have users yet.</td></tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        if($searching == "0") {
                            $ver = "./?a=users";
                            if(admin_pagination($statement,$ver,$limit,$page)) {
                                echo admin_pagination($statement,$ver,$limit,$page);
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
<?php
}
?>