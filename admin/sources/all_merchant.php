<?php
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

if (isset($_GET['b'])){
	$b = protect($_GET['b']);
} else {
	$b = "";
}

if($b == "edit") {
	$id = protect($_GET['id']);
	$query = $db->query("SELECT * FROM users WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=all_merchant"); }
	$row = $query->fetch_assoc();
	?>
	<div class="row">
            <div class="col-md-12">
            <?php
            $FormBTN = protect($_POST['btn_save']);
            if($FormBTN == "profile") {
                $business_name = protect($_POST['business_name']);
                $business_website = protect($_POST['business_website']);
                $business_website = filter_var($business_website, FILTER_SANITIZE_URL);
                $commission = protect($_POST['business_who_pay_fee']); //1= Merchant, 2= Client/Customer
                $business_category = protect($_POST['business_category']);
                $business_country = protect($_POST['business_country']);
                $business_description = protect($_POST['business_description']);
                if(isset($_POST['document_verified'])) { $document_verified =1; } else { $document_verified=0; }
                
                if(empty($business_name) or empty($business_website) or empty($commission) or empty($business_category) or empty($business_country) or empty($business_description)) {
                    echo error("Some fields are empty.");
                } else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $business_description)) {
                    echo error("Invalid Characters are not allowed.");
                } else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $business_category)) {
                    echo error("Invalid Characters are not allowed.");
                }else if (!filter_var($business_website, FILTER_VALIDATE_URL)) {
                    echo error("Url is not valid");    
                } else {
                
                $update = $db->query("UPDATE users SET document_verified='$document_verified',business_name='$business_name',business_website='$business_website',business_who_pay_fee='$commission',business_category='$business_category',business_country='$business_country',business_description='$business_description',business_status='$business_status' WHERE id='$_SESSION[pw_uid]'");
                $query = $db->query("SELECT * FROM users WHERE id='$row[id]'");
                $row = $query->fetch_assoc();
                echo success("Merchant has been updated successfully.");
                
                }
            }
            ?>
            </div>
           <div class="col-md-8">
				<div class="card">
				    <div class="card-header">
                        <strong class="card-title">Merchant <b>Information</b></strong>
                    </div>
                    <div class="card-body">
                         
			
			<form action="" method="POST">
                <div class="form-group">
					<label>Business Name</label>
					<input type="text" class="form-control" name="business_name" value="<?php echo filter_var($row['business_name'], FILTER_SANITIZE_STRING); ?>">
                </div>
                <div class="form-group">
					<label>Business Website</label>
					<input type="text" class="form-control" name="business_website" value="<?php echo filter_var($row['business_website'], FILTER_SANITIZE_STRING); ?>">
                </div>
                <div class="form-group">
                    <label>Commission type</label>
                    <select class="form-control" name="business_who_pay_fee">
                        <option value="1" <?php if($row['business_who_pay_fee'] == "1") { echo 'selected'; } ?>>Merchant</option>
                        <option value="2" <?php if($row['business_who_pay_fee'] == "2") { echo 'selected'; } ?>>Client/Customer</option>
                    </select>
                </div>
                <div class="form-group">
					<label>Business Category/Industry</label>
					<input type="text" class="form-control" name="business_category" value="<?php echo filter_var($row['business_category'], FILTER_SANITIZE_STRING); ?>">
                </div>
                <div class="form-group">
					<label>Email address</label>
					<input type="text" disabled class="form-control" name="email" value="<?php echo filter_var($row['email'], FILTER_SANITIZE_STRING); ?>">
                </div>
                <div class="form-group">
					<label>Country of Business</label>
					<select class="form-control" name="business_country">
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
                    <label>Business Description</label>
                    <textarea type="text" class="form-control" name="business_description"><?php echo $row['business_description']; ?></textarea>
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
                <strong class="card-title">Status
                
                <?php
                if($row['business_status'] == "1") {
                    $result = '<span class="badge badge-info">Approved</span>';
                } elseif ($row['business_status'] == "2") {
                    $result = '<span class="badge badge-info">Pending</span>';
                } elseif ($row['business_status'] == "3") {
                    $result = '<span class="badge badge-info">Document Pending</span>';
                } elseif ($row['business_status'] == "4") {
                    $result = '<span class="badge badge-info">Rejected</span>';
                } elseif ($row['business_status'] == "5") {
                    $result = '<span class="badge badge-info">Cancelled by user</span>';
                } elseif ($row['business_status'] == "6") {
                    $result = '<span class="badge badge-info">Blocked</span>';
                } elseif ($row['business_status'] == "") {
                    $result = '<span class="badge badge-info">Not Applied yet</span>';
                } elseif ($row['business_status'] == "0") {
                    $result = '<span class="badge badge-info">Not Applied yet</span>';
                }
                echo $result;
                ?>
                
                </span></strong>
            </div>
            <div class="card-body">
                <?php
                    $FormBTN = protect($_POST['btn_save_status']);
                    if($FormBTN == "btn_save_status") {
                        $business_status = protect($_POST['business_status']);
                        $update = $db->query("UPDATE users SET business_status='$business_status' WHERE id='$row[id]'");
                        $query = $db->query("SELECT * FROM users WHERE id='$id'");
                        $row = $query->fetch_assoc();
                        echo success("Merchant Updated...");
                    }
                ?>
                <form action="" method="POST">
                    <div class="form-group">
    					<label>Status</label>
    					<select class="form-control" name="business_status">
    						<option value="1" <?php if($row['business_status'] == "1") { echo 'selected'; } ?>>Approved</option>
    						<option value="2" <?php if($row['business_status'] == "2") { echo 'selected'; } ?>>Pending</option>
    						<option value="3" <?php if($row['business_status'] == "3") { echo 'selected'; } ?>>Require Document</option>
    						<option value="4" <?php if($row['business_status'] == "4") { echo 'selected'; } ?>>Rejected</option>
    						<option value="5" <?php if($row['business_status'] == "5") { echo 'selected'; } ?>>Cancelled by User</option>
    						<option value="6" <?php if($row['business_status'] == "6") { echo 'selected'; } ?>>Blocked</option>
    						<option value="" <?php if($row['business_status'] == "" or $row['business_status'] == "0") { echo 'selected'; } ?>>Not Applied Yet</option>
    					</select>
				    </div>
                    <button type="submit" class="btn btn-primary btn-block" name="btn_save_status" value="btn_save_status">Update</button>
                </form>
                <a target="_blank" href="./?a=users&b=edit&id=<?php echo $id;?>"><button class="btn btn-primary btn-block" >Check User Details</button></a><p></p>
            </div>
        </div>
    
        <div class="card">
            <div class="card-body">
                <?php
                    $FormBTN = protect($_POST['btn_save_reject']);
                    if($FormBTN == "btn_save_reject") {
                        $business_reject = protect($_POST['business_reject']);
                        $update = $db->query("UPDATE users SET business_reject='$business_reject' WHERE id='$row[id]'");
                        $query = $db->query("SELECT * FROM users WHERE id='$id'");
                        $row = $query->fetch_assoc();
                        echo success("Reason has been added.");
                    }
                ?>
                <form action="" method="POST">
                    <div class="form-group">
                        <label>Enter Rejection Reason</label>
        			    <textarea class="form-control" name="business_reject" placeholder="Rejection Reason"><?=$row['business_reject'];?></textarea>
        			    <small>Enter Rejection reason, If you want to reject merchant.</small>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" name="btn_save_reject" value="btn_save_reject">Save</button>
			    </form>
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
                                    <a href="./?a=all_merchant&b=documents&c=accept&uid=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>&did=<?php echo filter_var($doc['id'], FILTER_SANITIZE_STRING); ?>"><span class="badge badge-success"><i class="fa fa-check"></i> Accept</span></a> 
                                    <a href="./?a=all_merchant&b=documents&c=reject&uid=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>&did=<?php echo filter_var($doc['id'], FILTER_SANITIZE_STRING); ?>"><span class="badge badge-danger"><i class="fa fa-times"></i> Reject</span></a>
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
	if($query->num_rows==0) { header("Location: ./?a=all_merchant"); }
	$row = $query->fetch_assoc();
    $c = protect($_GET['c']);
    $did = protect($_GET['did']);
    if($c == "accept") {
        $check = $db->query("SELECT * FROM users_documents WHERE uid='$id' and id='$did'");
        if($check->num_rows>0) {
            $update = $db->query("UPDATE users_documents SET status='3' WHERE id='$did'");
            $redirect = './?a=all_merchant&b=edit&id='.$id.'#documents'; 
            header("Location: $redirect");
        } else {
            $redirect = './?a=all_merchant&b=edit&id='.$id.'#documents'; 
            header("Location: $redirect");
        }
    } elseif($c == "reject") {
        $check = $db->query("SELECT * FROM users_documents WHERE uid='$id' and id='$did'");
        if($check->num_rows==0) {
            $redirect = './?a=all_merchant&b=edit&id='.$id.'#documents'; 
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
                                $redirect = './?a=all_merchant&b=edit&id='.$id.'#documents'; 
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
        header("Location: ./?a=all_merchant&b=edit&id=$id");
    }
} else {
?>


            <div class="col-md-12">
				<div class="card">
                    <div class="card-body">
                        <form action="" method="POST">
                        <div class="row">
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
                                    <th>Email</th>
                                    <th>Business Name</th>
                                    <th>Business URL</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $searching=0;
                                if (isset($_POST['btn_search'])){
								$FormBTN = protect($_POST['btn_search']);
								} else {
								$FormBTN = "";
								}
                                if($FormBTN == "users") {
                                    $searching=1;
                                    $search_query = array();
                                    $s_business_name = protect($_POST['business_name']);
                                    if(!empty($s_business_name))  { $search_query[] = "business_name='$s_business_name'"; }
                                    $s_email = protect($_POST['email']);
                                    if(!empty($s_email)) { $search_query[] = "email='$s_email'"; }
                                    $s_country = protect($_POST['country']);
                                    if(!empty($s_country)) { $search_query[] = "business_country='$s_country'"; }
                                    $p_query = implode(" and ",$search_query);
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
                                    $query = $db->query("SELECT * FROM {$statement} WHERE account_type='2' ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                                }
                                if($query->num_rows>0) {
                                    while($row = $query->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td><?php echo filter_var($row['email'], FILTER_SANITIZE_STRING); ?></td>
                                            <td><?php echo filter_var($row['business_name'], FILTER_SANITIZE_STRING); ?></td>
                                            <td><?php echo filter_var($row['business_website'], FILTER_SANITIZE_STRING); ?></td>
                                            <td>
                                                <?php
                                                if($row['business_status'] == "1") {
                                                    $result = '<span class="badge badge-success">Approved</span>';
                                                } elseif ($row['business_status'] == "2") {
                                                    $result = '<span class="badge badge-warning">Pending</span>';
                                                } elseif ($row['business_status'] == "3") {
                                                    $result = '<span class="badge badge-warning">Document Pending</span>';
                                                } elseif ($row['business_status'] == "4") {
                                                    $result = '<span class="badge badge-danger">Rejected</span>';
                                                } elseif ($row['business_status'] == "5") {
                                                    $result = '<span class="badge badge-info">Cancelled by user</span>';
                                                } elseif ($row['business_status'] == "6") {
                                                    $result = '<span class="badge badge-danger">Blocked</span>';
                                                } elseif ($row['business_status'] == "") {
                                                    $result = '<span class="badge badge-info">Not Applied yet</span>';
                                                }
                                                echo $result;
                                                ?>
                                            </td>
                                            <td>
                                                <a href="./?a=all_merchant&b=edit&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="Edit"><span class="badge badge-primary"><i class="fa fa-pencil"></i> Edit</span></a> 
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    if($searching == "1") {
                                        echo '<tr><td colspan="5">No found results.</td></tr>';
                                    } else {
                                        echo '<tr><td colspan="5">No have Merchants yet.</td></tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        if($searching == "0") {
                            $ver = "./?a=all_merchant";
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