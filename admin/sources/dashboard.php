<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
?>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php $query = $db->query("SELECT * FROM users"); echo filter_var($query->num_rows, FILTER_SANITIZE_STRING); ?></h3>

                <p>Total Users</p>
              </div>
              <div class="icon">
                <i class="fas fa-users"></i>
              </div>
              <a href="./?a=users" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?php $query = $db->query("SELECT * FROM transactions"); echo filter_var($query->num_rows, FILTER_SANITIZE_STRING); ?></h3>

                <p>Transactions</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="./?a=transactions" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?php $query = $db->query("SELECT * FROM deposits"); echo filter_var($query->num_rows, FILTER_SANITIZE_STRING); ?></h3>

                <p>Total Deposits</p>
              </div>
              <div class="icon">
                <i class="fas fa-clinic-medical"></i>
              </div>
              <a href="./?a=deposits" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?php $get_stats = $db->query("SELECT * FROM withdrawals"); echo filter_var($get_stats->num_rows, FILTER_SANITIZE_STRING); ?></h3>

                <p>Total Withdrawals</p>
              </div>
              <div class="icon">
                <i class="fas fa-coins"></i>
              </div>
              <a href="./?a=withdrawals" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>
        <div class="row">
            <div id="recent-transactions" class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Action Required For <b>Pending Deposits</b></h4>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table id="recent-orders" class="table table-hover table-xl mb-0">
                                <thead>
                                    <tr>
                                        <th class="border-top-0">Status</th>
                                        <th class="border-top-0">TXID</th>
                                        <th class="border-top-0">User</th>
                                        <th class="border-top-0">Method</th>
                                        <th class="border-top-0">Action</th>
                                        <th class="border-top-0">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
    								$i=1;
    								$query = $db->query("SELECT * FROM deposits WHERE status='3' ORDER BY id");
    								if($query->num_rows>0) {
    									while($row = $query->fetch_assoc()) {
    										?>
    							<tr>
                                    <tr>
                                        <td class="text-truncate">
                                            <?php
                                            $status = $row['status'];
                                            if($status == "1") {
                                                echo '<span class="badge badge-success">Completed</span>';
                                            } elseif($status == "2") {
                                                echo '<span class="badge badge-danger">Canceled</span>';
                                            } elseif($status == "3") {
                                                echo '<span class="badge badge-warning">Pending</span>';
                                            } else { }
                                            ?>
                                        </td>
                                        <td class="text-truncate"><?php echo filter_var($row['txid'], FILTER_SANITIZE_STRING); ?></td>
                                        <td class="text-truncate">
                                            <a href="./?a=users&b=edit&id=<?php echo filter_var($row['uid'], FILTER_SANITIZE_STRING); ?>"><?php echo idinfo($row['uid'],"email"); ?></a>
                                        </td>
                                        <td class="text-truncate p-1">
                                            <?php echo gatewayinfo($row['method'],"name"); ?>
                                        </td>
                                        <td>
                                            <a href="./?a=deposits&b=view&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="View"><button type="button" class="btn btn-sm btn-outline-danger round">View</button></a>
                                        </td>
                                        <td class="text-truncate"><?php echo filter_var($row['amount'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($row['currency'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <?php
    									}
    								} else {
    									echo '<tr><td colspan="6">You no have new deposit requests.</td></tr>';
    								}
    								?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div id="recent-transactions" class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Action Required For <b>Pending Cashouts</b></h4>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table id="recent-orders" class="table table-hover table-xl mb-0">
                                <thead>
                                    <tr>
                                        <th class="border-top-0">Status</th>
                                        <th class="border-top-0">TXID</th>
                                        <th class="border-top-0">User</th>
                                        <th class="border-top-0">Method</th>
                                        <th class="border-top-0">Action</th>
                                        <th class="border-top-0">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
        							  $i=1;
        							  $query = $db->query("SELECT * FROM withdrawals WHERE status='1' ORDER BY id");
        							  if($query->num_rows>0) {
        								while($row = $query->fetch_assoc()) {
        								?>
    							<tr>
                                    <tr>
                                        <td class="text-truncate">
                                            <?php
                                            $status = $row['status'];
                                            if($status == "3") {
                                                echo '<span class="badge badge-success">Completed</span>';
                                            } elseif($status == "2") {
                                                echo '<span class="badge badge-danger">Canceled</span>';
                                            } elseif($status == "1") {
                                                echo '<span class="badge badge-warning">Pending</span>';
                                            } else { }
                                            ?>
                                        </td>
                                        <td class="text-truncate"><?php echo filter_var($row['txid'], FILTER_SANITIZE_STRING); ?></td>
                                        <td class="text-truncate">
                                            <a href="./?a=users&b=edit&id=<?php echo filter_var($row['uid'], FILTER_SANITIZE_STRING); ?>"><?php echo idinfo($row['uid'],"email"); ?></a>
                                        </td>
                                        <td class="text-truncate p-1">
                                            <?php echo gatewayinfo($row['method'],"name"); ?>
                                        </td>
                                        <td>
                                            <a href="./?a=withdrawals&b=view&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="View"><button type="button" class="btn btn-sm btn-outline-danger round">View</button></a>
                                        </td>
                                        <td class="text-truncate"><?php echo filter_var($row['amount'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($row['currency'], FILTER_SANITIZE_STRING); ?></td>
                                    </tr>
                                    <?php 
    								$i++;
    								}
    							  } else {
    								echo '<tr><td colspan="6">You no have new withdrawal requests.</td></tr>';
    							  }
    							  ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div id="recent-transactions" class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Action Required For <b>Pending Merchants</b></h4>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table id="recent-orders" class="table table-hover table-xl mb-0">
                                <thead>
                                    <tr>
                                        <th class="border-top-0">Status</th>
                                        <th class="border-top-0">Email</th>
                                        <th class="border-top-0">Business Name</th>
                                        <th class="border-top-0">Business URL</th>
                                        <th class="border-top-0">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
        							  $i=1;
        							  $query = $db->query("SELECT * FROM users WHERE business_status='2' ORDER BY id");
        							  if($query->num_rows>0) {
        								while($row = $query->fetch_assoc()) {
        								?>
    							<tr>
                                    <tr>
                                        <td class="text-truncate">
                                            <?php
                                            $status = $row['business_status'];
                                            if($status == "2") {
                                                echo '<span class="badge badge-warning">Pending</span>';
                                            } else { }
                                            ?>
                                        </td>
                                        <td class="text-truncate"><?php echo filter_var($row['email'], FILTER_SANITIZE_STRING); ?></td>
                                        <td class="text-truncate"><?php echo filter_var($row['business_name'], FILTER_SANITIZE_STRING); ?></td>
                                        <td class="text-truncate"><?php echo filter_var($row['business_website'], FILTER_SANITIZE_STRING); ?></td>
                                        <td>
                                            <a href="./?a=all_merchant&b=edit&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="View"><button type="button" class="btn btn-sm btn-outline-danger round">View</button></a>
                                        </td>
                                    </tr>
                                    <?php 
    								$i++;
    								}
    							  } else {
    								echo '<tr><td colspan="6">You no have new merchant requests.</td></tr>';
    							  }
    							  ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
        <div id="recent-transactions" class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Action Required For <b>Pending KYC</b></h4>
                </div>
                <div class="card-content">
                    <div class="table-responsive">
                        <table id="recent-orders" class="table table-hover table-xl mb-0">
                            <thead>
                                <tr>
                                    <th class="border-top-0">Document</th>
                                    <th class="border-top-0">User</th>
                                    <th class="border-top-0">Doc No.</th>
                                    <th class="border-top-0">Action</th>
                                    <th class="border-top-0">Attached</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                    			  $i=1;
                    			  $query = $db->query("SELECT * FROM users_documents WHERE status='1' ORDER BY id");
                    			  if($query->num_rows>0) {
                    			    while($doc = $query->fetch_assoc()) {
                    				?>
							<tr>
                                <tr>
                                    <td class="text-truncate"><?php if($doc['document_type'] == "1") { echo 'Personal ID'; } elseif($doc['document_type'] == "2") { echo 'National Passport'; } elseif($doc['document_type'] == "3") { echo 'Driving License'; } elseif($doc['document_type'] == "4") { echo 'Invoice'; } else { echo 'Unknown'; } ?></td>
                                    <td class="text-truncate">
                                        <a href="./?a=users&b=edit&id=<?php echo filter_var($doc['uid'], FILTER_SANITIZE_STRING); ?>"><?php echo idinfo($doc['uid'],"email"); ?></a>
                                    </td>
                                    <td class="text-truncate">
                                        <?php echo filter_var($doc['u_field_1'], FILTER_SANITIZE_STRING); ?>
                                    </td>
                                    <td>
                                        <?php if($doc['status'] == "1") { ?>
                                        <a href="./?a=users&b=documents&c=accept&uid=<?php echo filter_var($doc['uid'], FILTER_SANITIZE_STRING); ?>&did=<?php echo filter_var($doc['id'], FILTER_SANITIZE_STRING); ?>"><span class="badge badge-success"><i class="fa fa-check"></i> Accept</span></a> 
                                        <a href="./?a=users&b=documents&c=reject&uid=<?php echo filter_var($doc['uid'], FILTER_SANITIZE_STRING); ?>&did=<?php echo filter_var($doc['id'], FILTER_SANITIZE_STRING); ?>"><span class="badge badge-danger"><i class="fa fa-times"></i> Reject</span></a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-truncate"><a href="<?php echo filter_var($settings['url'].$doc['document_path'], FILTER_SANITIZE_STRING); ?>" target="_blank"><span class="badge badge-primary"><i class="fa fa-search"></i> Preview</span></a><br/><small>Uploaded on <?php echo date("d/m/Y H:i:s",$doc['uploaded']); ?></td>
                                </tr>
                                <?php 
                    				$i++;
                    				}
                    			  } else {
                    				echo '<tr><td colspan="5">You no have new documents for review.</td></tr>';
                    			  }
                    			  ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <strong class="card-title">Admin <b>Login Logs</b></strong>
                    </div>
                    <div class="card-body table-responsive">
                		<table class="table table-striped">
                			<thead style="background:black;color:white;">
                				<tr>
                				    <td width="">Username</td>
                					<td width="">Date</td>
                                    <td width="">Login IP</td>
                                    <td>Activity</td>
                				</tr>
                			</thead>
                			<tbody>
                				<?php
                                $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
                                $limit = 6;
                                $startpoint = ($page * $limit) - $limit;
                                if($page == 1) {
                                    $i = 1;
                                } else {
                                    $i = $page * $limit;
                                }
                                $statement = "users_logs WHERE uid='$_SESSION[admin_uid]'";
                                $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                                if($query->num_rows>0) {
                                    while($row = $query->fetch_assoc()) {
                                    ?>
                					<tr>
                					    <td><?php echo idinfo($row['uid'],"account_user"); ?></td>
                                        <td><?php echo date("d M Y H:i",$row['time']); ?></td>
                                        <td><?php echo filter_var($row['u_field_1'], FILTER_SANITIZE_STRING); ?></td>
                                        <td>
                                            <?php
                                            if($row['type'] == "1") {
                                                echo 'User(Login)';
                                            } elseif($row['type'] == "2") {
                                                echo 'Admin(Login)';
                                            } else {
                                                echo 'Unknown'; 
                                            }
                                            ?>
                                        </td>
                                    </tr>
                				<?php
                                    }
                                } else {
                                    echo '<tr><td colspan="3">'.$lang[info_4].'</td></tr>';
                                }
                                ?>
                			</tbody>
                		</table>
            	    </div>
	            </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <strong class="card-title">Last <b>6 Users</b></strong>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped">
                			<thead style="background:black;color:white;">
                				<tr>
                					<td width="90%">User Email</td>
                					<td width="10%">Action</td>
                                </tr>
                			</thead>
                			<tbody>
                				<?php
                                $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
                                $limit = 6;
                                $startpoint = ($page * $limit) - $limit;
                                if($page == 1) {
                                    $i = 1;
                                } else {
                                    $i = $page * $limit;
                                }
                                $statement = "users";
                                $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                                if($query->num_rows>0) {
                                    while($row = $query->fetch_assoc()) {
                                    ?>
                					<tr>
                					    <td><?php echo filter_var($row['email'], FILTER_SANITIZE_STRING); ?></td>
                					    <td><a href="./?a=users&b=edit&id=<?php echo filter_var($row['id'], FILTER_SANITIZE_STRING); ?>" title="Edit"><span class="badge badge-primary"><i class="fa fa-pencil"></i> Edit</span></a> </td>
                                    </tr>
                				<?php
                                    }
                                } else {
                                    echo '<tr><td colspan="3">No any users yet.</td></tr>';
                                }
                                ?>
                			</tbody>
                		</table>
                    </div>
                </div>
            </div>
        </div>
</div>
</section>
    <!-- /.content -->