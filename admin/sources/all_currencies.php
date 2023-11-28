<div class="card">
    <div class="card-header">
        <strong class="card-title">All <b>Currencies</b></strong>
    </div>

        
        	    <?php
            
        		if(isset($_POST['inactive'])) {
        		   $currency = protect($_POST['inactive']);
        		   $update = $db->query("UPDATE currency SET status='2' WHERE currency='$currency'");
        		   echo success("Currency has been removed.");
        		}
            
            
                ?>
                <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Currency</th>
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
        	        $statement = "currency WHERE status='1'";
        	        $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
        	        if($query->num_rows>0) {
        	            while($row = $query->fetch_assoc()) {
        	                ?>
        	                <tr>
        	                    <td><?php echo filter_var($row['code'], FILTER_SANITIZE_STRING); ?></td>
        	                    <td><?php echo filter_var($row['currency'], FILTER_SANITIZE_STRING); ?></td>
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
        	                        <form action="" method="POST">
        	                        <button type="submit" name="inactive" value="<?php echo filter_var($row['currency'], FILTER_SANITIZE_STRING); ?>" class="btn btn-danger btn-sm">Make It Inactive</button>
        	                        </form>
        	                    </td>
        	                </tr>
                                <?php
                            }
                        } else {
                            if($searching == "1") {
                                echo '<tr><td colspan="5">No found results.</td></tr>';
                            } else {
                                echo '<tr><td colspan="5">No have active currencies yet.</td></tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <?php
                if($searching == "0") {
                    $ver = "./?a=currencies";
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