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
?>
<h3><?php echo $lang['head_account_logs']; ?></h3>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <td width="25%"><?php echo $lang['date']; ?></td>
                <td width="25%"><?php echo $lang['ip']; ?></td>
                <td><?php echo $lang['activity']; ?></td>
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
        $statement = "users_logs WHERE uid='$_SESSION[pw_uid]'";
        $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
        if($query->num_rows>0) {
            while($row = $query->fetch_assoc()) {
            ?>
            <tr>
                <td><?php echo date("d M Y H:i",$row['time']); ?></td>
                <td><?php echo $row['u_field_1']; ?></td>
                <td>
                    <?php
                    if($row['type'] == "1") {
                        echo 'Login';
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

    <?php
    $ver = $settings['url']."account/logs";
    if(web_pagination($statement,$ver,$limit,$page)) {
        echo web_pagination($statement,$ver,$limit,$page);
    }
    ?>
</div>