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
<div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Disputes</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center justify-content-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"><?php echo filter_var($lang['sender'], FILTER_SANITIZE_STRING); ?></th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount</th>
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
                        $statement = "disputes WHERE sender='$_SESSION[pw_uid]' or recipient='$_SESSION[pw_uid]'";
                        $query = $db->query("SELECT * FROM {$statement} ORDER BY status, id DESC LIMIT {$startpoint} , {$limit}");
                        if($query->num_rows>0) {
                            while($row = $query->fetch_assoc()) {
                            ?>
                            <tr>
                              <td>
                                <div class="d-flex px-2 py-1">
                                  <div>
                                    <small><?php echo filter_var(date("d M Y H:i",$row['created']), FILTER_SANITIZE_STRING); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</small>
                                  </div>
                                  <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm"><?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?></h6>
                                  </div>
                                </div>
                              </td>
                              <td><?php if(idinfo($row['sender'],"account_type") == "1") { echo idinfo($row['sender'],"first_name")." ".idinfo($row['sender'],"last_name"); } else { echo idinfo($row['sender'],"business_name"); } if($_SESSION['pw_uid'] == $row['sender']) { echo ' (You)'; } ?></td>
                              <td class="align-middle text-center text-sm">
                                <span class="text-xs font-weight-bold"> <?php echo filter_var($row['amount']." ".$row['currency'], FILTER_SANITIZE_STRING); ?> </span>
                              </td>
                              <td class="align-middle">
                                <center><span class="text-xs font-weight-bold"> 
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
                                </span></center>
                              </td>
                              <td>
                                <div class="avatar-group mt-2 text-xs">
                                  <a href="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>index.php?a=account&b=dispute&c=<?php echo filter_var($row['hash'], FILTER_SANITIZE_STRING); ?>" class="btn btn-primary text-xs">View</a>
                                </div>
                              </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo '<tr><td colspan="7">'.$lang['info_3'].'</td></tr>';
                    }
                    ?>
                  </tbody>
                </table>
                <?php
                $ver = $settings['url']."index.php?a=account&b=disputes";
                if(web_pagination($statement,$ver,$limit,$page)) {
                    echo web_pagination($statement,$ver,$limit,$page);
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
</div>