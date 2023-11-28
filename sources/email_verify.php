<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

if(checkSession()) {
    $redirect = $settings['url']."index.php?a=account&b=summary";
    header("Location: $redirect");
}

$hash = protect($_GET['hash']);
$query = $db->query("SELECT * FROM users WHERE email_hash='$hash'");
if($query->num_rows==0) { header("Location: $settings[url]"); }
$row = $query->fetch_assoc();
$update = $db->query("UPDATE users SET email_hash='',email_verified='1',status='1' WHERE id='$row[id]'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
   <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
  <title><?php echo filter_var($lang['title_email_verification']); ?> - <?php echo filter_var($settings['name']); ?></title>
  <meta name="description" content="<?php echo filter_var($settings['description']); ?>">
  <meta name="keywords" content="<?php echo filter_var($settings['keywords']); ?>">
    <?php if($settings['favicon']) { ?>
		<link rel="icon" type="image/png" href="<?= $settings['url'].$settings['favicon'] ?>">
	<?php } else { ?>
		<link rel="icon" type="image/png" href="<?= $settings['url'] ?>assets/logo/favicon.png">
	<?php } ?>
</head>
<style type="text/css">

    body
    {
        background:#f2f2f2;
    }

    .payment
	{
		border:1px solid #f2f2f2;
		height:280px;
        border-radius:20px;
        background:#fff;
	}
   .payment_header
   {
	   background:#58C827;
	   padding:20px;
       border-radius:20px 20px 0px 0px;
	   
   }
   
   .check
   {
	   margin:0px auto;
	   width:50px;
	   height:50px;
	   border-radius:100%;
	   background:#fff;
	   text-align:center;
   }
   
   .check i
   {
	   vertical-align:middle;
	   line-height:50px;
	   font-size:30px;
   }

    .content 
    {
        text-align:center;
    }

    .content  h1
    {
        font-size:25px;
        padding-top:25px;
    }

    .content a
    {
        width:200px;
        height:35px;
        color:#fff;
        border-radius:30px;
        padding:5px 10px;
        background:#58C827;
        transition:all ease-in-out 0.3s;
    }

    .content a:hover
    {
        text-decoration:none;
        background:#000;
    }
   
</style>
<body>
        <div class="container">
           <div class="row">
              <div class="col-md-6 mx-auto mt-5">
                 <div class="payment">
                    <div class="payment_header">
                       <div class="check"><i class="fa fa-check" aria-hidden="true"></i></div>
                    </div>
                    <div class="content">
                       <h1><?= $lang['title_email_verification']; ?></h1>
                       <p><?= $lang['success_18']; ?></p>
                       <a href="<?= $settings['url']; ?>login">Login Now</a>
                    </div>
                    
                 </div>
              </div>
           </div>
        </div>
    </body>
</html>
