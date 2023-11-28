<?php

$date = date('Y-m-d');
$fdac = $db->query("SELECT * FROM fixed_deposits WHERE status='1'");
if($fdac->num_rows>0) {
    while($fd = $fdac->fetch_assoc()) {
        if ($date > $fd['date_finish']) {
            $txid = strtoupper(randomHash(10));
            $time = time();
            $description = "Fixed Deposit Returned";
            PW_UpdateUserWallet($fd['uid'],$fd['total_return'],$fd['currency'],1);   //Add Fund to User Account
            $update = $db->query("UPDATE fixed_deposits SET status='2',proceed_at='$time' WHERE id='$fd[id]'"); //Updating Fix Deposit Details
            //Create Transaction
            $create_transaction = $db->query("INSERT transactions (txid,type,sender,description,amount,currency,status,created) 
            VALUES ('$txid','52','$fd[uid]','$description','$fd[total_return]','$fd[currency]','1','$time')");
            //Create Activity
            $insert_activity = $db->query("INSERT activity (txid,type,uid,amount,currency,status,created) 
            VALUES ('$txid','52','$fd[uid]','$fd[total_return]','$fd[currency]','1','$time')");
        } 
    }
}



?>