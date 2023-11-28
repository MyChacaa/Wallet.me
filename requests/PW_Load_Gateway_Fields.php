<?php
// eWallet - PHP Script
// Author: DeluxeScript
define('PWV1_INSTALLED',TRUE);
ob_start();
session_start();
include("../configs/bootstrap.php");
include("../includes/bootstrap.php");
include(getLanguage($settings['url'],null,2));

if(checkSession()) {
    $id = protect($_GET['id']);
    
    $query = $db->query("SELECT * FROM gateways_fields WHERE gateway_id='$id' ORDER BY id");
    if($query->num_rows>0) {
        $process_type = gatewayinfo($id,"process_type");
        $process_time = gatewayinfo($id,"process_time");
        $fee = gatewayinfo($id,"fee");
        $include_fee = gatewayinfo($id,"include_fee");
        $extra_fee = gatewayinfo($id,"extra_fee");
        if($include_fee == "1") {
            $efee = '+ '.$extra_fee.'%';
        } else {
            $efee = '';
        }
        if($process_type == "1") {
            if($process_time == "1") {
                $prcoessed_time = '1 '.$lang[minute];
            } else {
                $processed_time = $process_time.' '.$lang[minutes];
            }
        } elseif($process_type == "2") {
            if($process_time == "1") {
                $processed_time = '1 '.$lang['hour'];
            } else {
                $processed_time = $process_time.' '.$lang[hours];
            }
        } elseif($process_type == "3") {
            if($process_time == "1") {
                $processed_time = '1 '.$lang[day];
            } else {
                $processed_time = $process_time.' '.$lang[days];
            }   
        } else {
            $processed_time = '';
        }
        $c_fee = $fee;
        echo '<div class="form-group" style="color:white;">
                <label style="color:white;">'.$lang['will_be_debited'].'</label>
                <input type="text" class="form-control" disabled id="receive_amount">
                <input type="hidden" id="c_fee" value="'.$c_fee.'">
                <input type="hidden" id="d_fee" value="'.$c_fee.'">
                <input type="hidden" id="c_include_fee" value="'.$include_fee.'">
                <input type="hidden" id="c_extra_fee" value="'.$extra_fee.'">
            </div>';
        echo filter_var($lang['will_be_processed'], FILTER_SANITIZE_STRING).' '.$processed_time.'<br/>
        '.$lang['withdrawal_fee'].': <span id="wfee">'.$fee.' '.$settings['default_currency'].'</span> '.$efee.'
        ';
        while($row = $query->fetch_assoc()) {
            echo '<div class="form-group">
                <label style="color:white;">'.$row['field_name'].'</label>
                <input type="text" class="form-control" name="fieldvalues['.$row['id'].']">
            </div>';
        }
    } 
}
?>