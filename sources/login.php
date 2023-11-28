<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
// if(checkSession()) {
//     $redirect = $settings['url']."index.php?a=account/summary";
//     header("Location: $redirect");
// }
if(isset($_GET['type'])) {
        $type = protect($_GET['type']);


if($type == "auth") {
$auth_id = $_SESSION['pw_auth_uid'];
$query = $db->query("SELECT * FROM users WHERE id='$auth_id'");
if($query->num_rows==0) { 
    $redirect = $settings['url']."index.php?a=login";
    header("Location: $redirect");
}
$u = $query->fetch_assoc();
$ga 		= new GoogleAuthenticator();
$qrCodeUrl 	= $ga->getQRCodeGoogleUrl(idinfo($_SESSION['pw_auth_uid'],"email"), $_SESSION['pw_secret'], $settings['name']);
?>
<!DOCTYPE html>
<html>
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Two Factor Auth - <?= $settings['name']; ?></title>
    <meta name="description" content="<?= $settings['description']; ?>">
    <meta name="keywords" content="<?php echo filter_var($settings['keywords'], FILTER_SANITIZE_STRING); ?>">
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../../assets/new/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../assets/new/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/new/dist/css/adminlte.min.css">
    <?php if($settings['favicon']) { ?>
		<link rel="icon" type="image/png" href="<?= $settings['url'].$settings['favicon'] ?>">
	<?php } else { ?>
		<link rel="icon" type="image/png" href="<?= $settings['url'] ?>assets/logo/favicon.png">
	<?php } ?>
</head>
<body class="hold-transition login-page">
    <div class="login-box">
      <div class="card card-outline card-primary">
        <div class="card-header text-center">
          <a href="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>" class="h1"><b><?php echo filter_var($settings['name'], FILTER_SANITIZE_STRING); ?></b></a>
        </div>
        <div class="card-body">
          <p class="login-box-msg"><?php echo filter_var($lang['title_2fa'], FILTER_SANITIZE_STRING); ?></p>
          <?php
            $FormBTN = protect($_POST['pw_auth']);
            if($FormBTN == "auth") {
                $code = protect($_POST['code']);
                $checkResult = $ga->verifyCode($_SESSION['pw_secret'], $code, 2);    // 2 = 2*30sec clock tolerance
                if($checkResult) {
                            $_SESSION['pw_auth_code'] = false;
                            $_SESSION['pw_auth_id'] = false;
                            $_SESSION['pw_uid'] = $u['id'];
                            if(protect($_POST['remember_me']) == "yes") {
                                setcookie("prowall_uid", $u['id'], time() + (86400 * 30), '/'); // 86400 = 1 day
                            }
                            $last_login = $login['last_login']+5000;
                            if(time() > $last_login) {
                                $time = time();
                                $update = $db->query("UPDATE users SET last_login='$time' WHERE id='$u[id]'");
                            }
                            $time = time();
                            $login_ip = $_SERVER['REMOTE_ADDR'];
                            $insert = $db->query("INSERT users_logs (uid,type,time,u_field_1) VALUES ('$u[id]','1','$time','$login_ip')");
                            if($_SESSION['pw_payorder_url']) {
                                $redirect = $_SESSION['pw_payorder_url'];
                                header("Location: $redirect");
                            } else {
                                $redirect = $settings['url']."index.php?a=account&b=summary";
                                header("Location: $redirect");
                            }
                } else {
                    echo error($lang['error_51']);
                }
            } 
          ?>
          <form action="" method="post">
            <div class="input-group mb-3">
              <input class="form-control" type="email" disabled value="<?php echo filter_var($u['email'], FILTER_SANITIZE_STRING); ?>" name="email" placeholder="<?php echo filter_var($lang['placeholder_3'], FILTER_SANITIZE_STRING); ?>">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input class="form-control" type="text" name="code" placeholder="<?php echo filter_var($lang['placeholder_12'], FILTER_SANITIZE_STRING); ?>" required>
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-vr-cardboard"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <button type="submit" name="pw_auth" value="auth" class="btn btn-primary btn-block"><?php echo filter_var($lang['btn_29'], FILTER_SANITIZE_STRING); ?></button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
                            
    <script src="../../assets/new/plugins/jquery/jquery.min.js"></script>
    <script src="../../assets/new/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/new/dist/js/adminlte.min.js"></script>
</body>
</html>

  
<?php
} 
} else {
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login or Register - <?php echo filter_var($settings['name'], FILTER_SANITIZE_STRING) ?></title>
    <meta name="description" content="<?php echo filter_var($settings['description'], FILTER_SANITIZE_STRING); ?>">
    <meta name="keywords" content="<?php echo filter_var($settings['keywords'], FILTER_SANITIZE_STRING); ?>">
	<script src="https://use.fontawesome.com/32efc5ddb7.js"></script>
    <link rel="stylesheet" type="text/css" href="<?= $settings['url'] ?>assets/front/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?= $settings['url'] ?>assets/front/css/custom_styles.css">
    <script src="<?= $settings['url'] ?>assets/front/js/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php if($settings['favicon']) { ?>
		<link rel="icon" type="image/png" href="<?= $settings['url'].$settings['favicon'] ?>">
	<?php } else { ?>
		<link rel="icon" type="image/png" href="<?= $settings['url'] ?>assets/logo/favicon.png">
	<?php } ?>
</head>
<body class="bg1">


<script type="text/javascript">
$(document).ready(function(){
$(".loginInp").focus(function(){
	$(this).parent().addClass("logFocusInp");
});
$(".loginInp").focusout(function(){
	$(this).parent().removeClass("logFocusInp");
});
});
</script>

<br>
<br>
<div class="loginPageContainer flex ai jc fdc">
<a href="javascript:">
    <?php if($settings['logo']) { ?>
		<img src="<?= $settings['url'].$settings['logo'] ?>" width="180px" class="loginPageLogo">
	<?php } else { ?>
		<img src="<?= $settings['url'] ?>assets/logo/logo_red.png" width="180px" class="loginPageLogo">
	<?php } ?>
</a>
<div class="flex boxBackLog">
	<div class="leftLogin">
	    <?php
		if(isset($_POST['login'])) {
			$FormBTN = protect($_POST['login']);
		
        
        if($FormBTN == "login") {
            $email = protect($_POST['email']);
            $password = protect($_POST['password']);
			if(isset($_POST['g-recaptcha-response'])) {
			$recaptcha_response = protect($_POST['g-recaptcha-response']);
            }
            $CheckLogin = $db->query("SELECT * FROM users WHERE email='$email'");
            if(empty($email) or empty($password)) { 
                echo error($lang['error_36']);
            } elseif($CheckLogin->num_rows==0) {
                echo error($lang['error_37']);
            } elseif($settings['enable_recaptcha'] == "1" && !VerifyGoogleRecaptcha($recaptcha_response)) {
                echo error($lang['error_52']);  
            } else {
                $login = $CheckLogin->fetch_assoc();
                if(password_verify($password, $login['password'])) {
                
                    if($login['status'] == "11") {
                        echo error($lang['error_38']);
                    } else {
                        if($login['2fa_auth'] == "1" && $login['2fa_auth_login'] == "1") {
                            $_SESSION['pw_auth_uid'] = $login['id'];
                            $_SESSION['pw_secret'] = $login['googlecode'];
                            $_SESSION['pw_auth_code'] = strtoupper(randomHash(7));
                            $redirect = $settings['url']."index.php?a=login&b=auth";
                            header("Location: $redirect");
                        } else {
                            $_SESSION['pw_uid'] = $login['id'];
							if(isset($_POST['remember_me'])) {
								if(protect($_POST['remember_me']) == "yes") {
                                setcookie("prowall_uid", $login['id'], time() + (86400 * 30), '/'); // 86400 = 1 day
								}
							}
                            
                            $last_login = $login['last_login']+5000;
                            if(time() > $last_login) {
                                $time = time();
                                $update = $db->query("UPDATE users SET last_login='$time' WHERE id='$login[id]'");
                            }
                            $time = time();
                            $login_ip = $_SERVER['REMOTE_ADDR'];
                            $insert = $db->query("INSERT users_logs (uid,type,time,u_field_1) VALUES ('$login[id]','1','$time','$login_ip')");
                            EmailSys_loginNotification($email,$login_ip);
                            if($_SESSION['pw_payorder_url']) {
                                $redirect = $_SESSION['pw_payorder_url'];
                                header("Location: $redirect");   
                            } else {
                                $redirect = $settings['url']."index.php?a=account&b=summary";
                                header("Location: $redirect");
                            }
                        }
                    }
                } else {
                    echo error($lang['error_37']);
                }
            }
        }
		}
        ?>
                            
		<form action="" method="POST" autocomplete="off">
			
			<h2 class="fw2"><span class="fw3 cred">Log in</span></h2>

			<p class="cgray fs14 mt10">Login to your Account <br> Send & Receive money online! </p>

			<div class="logInpBack flex ai mt20">
				<i class="fa fa-user"></i>
				<input type="email" name="email" placeholder="<?php echo filter_var($lang['placeholder_3'], FILTER_SANITIZE_STRING); ?>" class="loginInp">
			</div>
			<div class="logInpBack flex ai">
				<i class="fa fa-lock"></i>
				<input type="password" name="password" placeholder="<?php echo filter_var($lang['placeholder_11'], FILTER_SANITIZE_STRING); ?>" class="loginInp">
			</div>
            <div class="row mt20">
				<div class="col-xs-6">
					<label class="inFlex ai fs13 fw2 us pointer">
						<input type="checkbox" name="remember_me" class="checkboxDef  checkboxLogin" value="yes" id="customCheck">
						&nbsp;&nbsp;
						<?php echo filter_var($lang['remember_me'], FILTER_SANITIZE_STRING); ?>
					</label>
				</div>
				<div class="col-xs-6" align="right">
				    <?php if ($m["forget_password"] == "1") { ?>
                    <a href="<?= $settings['url'] ?>password/reset" class="fs13 inline"><?= $lang['forgot_password'] ?></a>
                    <?php } ?>
				</div>
			</div>  <!-- row -->
            
            <?php if($settings['enable_recaptcha'] == "1") { ?>
            <br>
            <center><script src="https://www.google.com/recaptcha/api.js" async defer></script>
            <div class="g-recaptcha" data-sitekey="<?php echo filter_var($settings['recaptcha_publickey'], FILTER_SANITIZE_STRING); ?>"></div></center>
            <br>
            <?php } ?>
           
			<button type="submit" name="login" value="login" class="fw3 mt20 submitBtnLogin w100 cwhite"><i class="fa fa-lock"></i> &nbsp;&nbsp;<?php echo filter_var($lang['btn_27'], FILTER_SANITIZE_STRING); ?></button>

			<hr>
			

			<p class="fs13">Any problem logging in?</p>
			<a href="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>#contact" class="getHelpBtn  td inline w100">Get Help</a>

		</form>
    </div> 
    <?php include("register.php"); ?>
</div>	
</div> <!-- mai8n-container -->
<br>
<br>
</body>
</html>    
    
    

<?php
}
?>