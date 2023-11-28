<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
?>
<div class="card">
    <div class="card-header">
        <strong class="card-title">Add <b>Country</b></strong>
    </div>
    <div class="card-body">
        <?php
        if(isset($_POST['add_country'])) {
		   $name = protect($_POST['name']);
		   $code = protect($_POST['code']);
		   
		    $vrfcountry_1 = $db->query("SELECT * FROM country WHERE name='$name'");
            $vrfcoun_1 = $vrfcountry_1->fetch_assoc();
            
            $vrfcountry_2 = $db->query("SELECT * FROM country WHERE code='$code'");
            $vrfcoun_2 = $vrfcountry_2->fetch_assoc();
            
            if($vrfcoun_1['name'] == $name or $vrfcoun_1['code'] == $code or $vrfcoun_2['name'] == $name or $vrfcoun_2['code'] == $code){
                echo error("Country already exist.");
            } elseif (empty($name) or empty($code)) {
                echo error("Some fields are empty");
            } else {
    		   $update = $db->query("UPDATE country SET status='2' WHERE name='$country'");
    		   $insert = $db->query("INSERT country (name,code,status) VALUES ('$name','$code','1')");
    		   echo success("Country has been Added.");
            }
		}
        ?>
        <form method="POST" action="">
            <div class="row">
				<div class="col-md-6">
					<input type="text" name="name" placeholder="Country Name" class="form-control">
				</div>
				<div class="col-md-6">
					<input type="text" name="code" placeholder="Country Code (2 Letters)" class="form-control" maxlength="2">
				</div>
			</div>
			<br>
			<button name="add_country" value="add_country" class="btn btn-primary btn-sm">Add Country</button>
        </form>
    
    
    </div>
</div>
<div class="card">
    <div class="card-header">
        <strong class="card-title">All <b>Countries</b></strong>
    </div>
    <div class="card-body table-responsive">
                <?php
                if(isset($_POST['inactive'])) {
        		   $country = protect($_POST['inactive']);
        		   $update = $db->query("UPDATE country SET status='2' WHERE name='$country'");
        		   echo success("Country has been disactivated.");
        		}
        		if(isset($_POST['active'])) {
        		   $country = protect($_POST['active']);
        		   $update = $db->query("UPDATE country SET status='1' WHERE name='$country'");
        		   echo success("Country has been activated.");
        		}
                ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Country</th>
                            <th>Code</th>
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
        	        $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
        	        $limit = 20;
        	        $startpoint = ($page * $limit) - $limit;
        	        if($page == 1) {
        	            $i = 1;
        	        } else {
        	            $i = $page * $limit;
        	        }
        	        $statement = "country";
        	        $query = $db->query("SELECT * FROM {$statement} ORDER BY id LIMIT {$startpoint} , {$limit}");
        	        if($query->num_rows>0) {
        	            while($row = $query->fetch_assoc()) {
        	                ?>
        	                <tr>
        	                    <td><?php echo filter_var($row['name'], FILTER_SANITIZE_STRING); ?></td>
        	                    <td><?php echo filter_var($row['code'], FILTER_SANITIZE_STRING); ?></td>
        	                    <td>
        	                        <?php
        	                        if($row['status'] == "1") {
        	                            echo '<span class="badge badge-success"><i class="fa fa-check"></i> Active</span>';
        	                        } else {
        	                            echo '<span class="badge badge-danger"><i class="fa fa-times"></i> Inactive</span>';
        	                        }
        	                        ?>
        	                    </td>
        	                    <td>
        	                        <?php if ($row['status'] == "1") { ?>
        	                            <form action="" method="POST">
            	                            <button type="submit" name="inactive" value="<?= $row['name']; ?>" class="btn btn-danger btn-sm">Make It Inactive</button>
            	                        </form>
        	                        <?php } else { ?>
        	                            <form action="" method="POST">
            	                            <button type="submit" name="active" value="<?= $row['name']; ?>" class="btn btn-success btn-sm">Make It Active</button>
            	                        </form>
        	                        <?php } ?>
        	                        
        	                    </td>
        	                </tr>
                                <?php
                            }
                        } else {
                            if($searching == "1") {
                                echo '<tr><td colspan="5">No found results.</td></tr>';
                            } else {
                                echo '<tr><td colspan="5">No have active countries yet.</td></tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <br>
                <?php
                if($searching == "0") {
                    $ver = "./?a=country";
                    if(admin_pagination($statement,$ver,$limit,$page)) {
                        echo admin_pagination($statement,$ver,$limit,$page);
                    }
                }
                ?>
                </div>
        	</div>
        </div>
    </div>
</div>