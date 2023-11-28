<?php
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
?>
<div class="col-md-12">
	<div class="card">
        <div class="card-body">
            <?php
            $refQuery = $db->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
			$row = $refQuery->fetch_assoc();
            
			if (isset($_POST['ref_update'])){
			$FormBTN = protect($_POST['ref_update']);
			} else {
			$FormBTN = "";
			}
            if($FormBTN == "ref_update") {
                $ref_com = protect($_POST['ref_com']);
                $update = $db->query("UPDATE settings SET ref_com='$ref_com'");
                echo success("Setting Updated...");
                $refQuery = $db->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
				$row = $refQuery->fetch_assoc();
            }
            ?>
            <form action="" method="POST">
            <div class="row">
                <div class="col-md-5" style="padding:10px;">
                    <input type="text" class="form-control" name="ref_com" placeholder="Referral Commission %" value="<?php echo filter_var($row['ref_com'], FILTER_SANITIZE_STRING); ?>">
                </div>
                <div class="col-md-2" style="padding:10px;">
                    <button type="submit" class="btn btn-primary btn-block" name="ref_update" value="ref_update">Update</button>
                </div>
                <div class="col-md-5" style="padding:10px;">
                    <small>Enter Referral Commission Percentage, Referral system works for verified and non verified user. Referral commission will be credited on send money.</small>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>

<?php $query = $db->query("SELECT * FROM bonus_logs ORDER BY id DESC"); ?>
           <div class="col-md-12">
					<div class="card">
                        <div class="card-body table-responsive">
                            
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="25%">User</th>
                                    <th width="15%">From Who</th>
                                    <th width="15%">Commission</th>
                                    <th width="15%">Date</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                   while($ccc = $query->fetch_assoc()) {
                                        ?>
                                
                                        <tr>
                                            <td><?php echo filter_var($ccc['user_email'], FILTER_SANITIZE_STRING); ?></td>
                                            <td><?php echo filter_var($ccc['from_who'], FILTER_SANITIZE_STRING); ?></td>
                                            <td><?php echo filter_var($ccc['currency'], FILTER_SANITIZE_STRING); ?> <?php echo filter_var($ccc['commission'], FILTER_SANITIZE_STRING); ?></td>
                                            <td><?php echo filter_var($ccc['date'], FILTER_SANITIZE_STRING); ?></td>
                                            
                                        </tr>
                                    <?php } ?>    
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>