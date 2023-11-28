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
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Support Tickets</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                
                
                <table class="table align-items-center justify-content-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ticket #</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"><?php echo $lang['sender']; ?></th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
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
                            $statement = "support WHERE sender='$_SESSION[pw_uid]' or recipient='$_SESSION[pw_uid]'";
                            $query = $db->query("SELECT * FROM {$statement} ORDER BY status, id DESC LIMIT {$startpoint} , {$limit}");
                            if($query->num_rows>0) {
                                while($row = $query->fetch_assoc()) {
                        ?>
                            <tr>
                                <td align="center"><?php echo date("d M Y",$row['created']); ?></td>
                                <td><?php echo $row['hash']; ?></td>
                                <td><?php if(idinfo($row['sender'],"account_type") == "1") { echo idinfo($row['sender'],"first_name")." ".idinfo($row['sender'],"last_name"); } else { echo idinfo($row['sender'],"business_name"); } if($_SESSION['pw_uid'] == $row['sender']) { echo ' (You)'; } ?></td>
                                <td align="center">
                                    <?php
                                    $status = $row['status'];
                                    if($row['status'] == "1") {
                                        echo '<span class="badge badge-info">'.$lang['status_dispute_1'].'</span>';
                                    } elseif($row['status'] == "2") {
                                        echo '<span class="badge badge-primary">'.$lang['status_dispute_2'].'</span>';
                                    } elseif($row['status'] == "3") {
                                        echo '<span class="badge badge-success">'.$lang['status_dispute_3'].'</span>';
                                    } elseif($row['status'] == "4") {
                                        echo '<span class="badge badge-warning">'.$lang['status_dispute_4'].'</span>';
                                    } else {
                                        echo '<span class="badge badge-warning">'.$lang['status_unknown'].'</span>';
                                    }
                                    ?> 
                                </td>
                                <td>
                                    <?php if($row['status']<2) { ?>
                                    <a href="<?php echo $settings['url']; ?>index.php?a=account&b=support&hash=<?php echo $row['hash']; ?>" class="btn btn-primary"><?php echo $lang['btn_6']; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $settings['url']; ?>index.php?a=account&b=support&hash=<?php echo $row['hash']; ?>" class="btn btn-primary"><?php echo $lang['btn_7']; ?></a>
                                    <?php } ?>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo '<tr><td colspan="7">You have no support tickets yet.</td></tr>';
                    }
                    ?>
                  </tbody>
                </table>
                <?php
                $ver = $settings['url']."index.php?a=account&b=supports";
                if(web_pagination($statement,$ver,$limit,$page)) {
                    echo web_pagination($statement,$ver,$limit,$page);
                }
                ?>
              </div>
              
              <br>
              <a href="<?php echo $settings['url']; ?>index.php?a=account&b=open&c=support/"><button type="button" class="btn btn-info" style="margin-left: 40%;width: 150px;">Contact us</button></a>
            </div>
          </div>
        </div>
    </div>
</div>