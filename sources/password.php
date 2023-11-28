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
if ($m["forget_password"] !== "1") {
    $redirect = $settings['url']."index.php?a=account&b=summary";
    header("Location: $redirect");
}
$b = protect($_GET['b']);
if($b == "reset") {
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title><?php echo filter_var($lang['title_reset_account_password']); ?> - <?php echo filter_var($settings['name']); ?></title>
    <meta name="description" content="<?php echo filter_var($settings['description']); ?>">
    <meta name="keywords" content="<?php echo filter_var($settings['keywords']); ?>">
    <?php if($settings['favicon']) { ?>
		<link rel="icon" type="image/png" href="<?= $settings['url'].$settings['favicon'] ?>">
	<?php } else { ?>
		<link rel="icon" type="image/png" href="<?= $settings['url'] ?>assets/logo/favicon.png">
	<?php } ?>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../../assets/new/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../assets/new/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/new/dist/css/adminlte.min.css">

</head>

<body class="hold-transition login-page">
    <div class="login-box">
      <div class="card card-outline card-primary">
        <div class="card-header text-center">
          <a href="<?php echo filter_var($settings['url']); ?>index.php" class="h1"><b><?php echo filter_var($settings['name']); ?></b></a>
        </div>
        <div class="card-body">
          <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>
          <?php
            $FormBTN = protect($_POST['pw_reset']);
            if($FormBTN == "reset") {
                $email = protect($_POST['email']);
                $recaptcha_response = protect($_POST['g-recaptcha-response']);
               if(empty($email)) {
                    echo error($lang['error_40']);
                } elseif(PW_CheckUser($email)==false) {
                    echo error($lang['error_41']);
                }  elseif($settings['enable_recaptcha'] == "1" && !VerifyGoogleRecaptcha($recaptcha_response)) {
                    echo error($lang['error_52']);  
                } else {
                    $hash = randomHash(25);
                    $update = $db->query("UPDATE users SET password_recovery='$hash' WHERE email='$email'");
                    PW_EmailSys_Send_Password_Reset($email);
                    echo success($lang['success_20']);
                }
            }
          ?>
          <form action="" method="post">
            <div class="input-group mb-3">
              <input class="form-control" type="email" name="email" placeholder="<?php echo filter_var($lang['placeholder_3']); ?>" required>
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
            </div>
            <?php if($settings['enable_recaptcha'] == "1") { ?>
                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                <div class="g-recaptcha" data-sitekey="<?php echo filter_var($settings['recaptcha_publickey']); ?>"></div>
                <br>
            <?php } ?>
            <div class="row">
              <div class="col-12">
                <button type="submit" name="pw_reset" value="reset" class="btn btn-primary btn-block">Recover Password</button>
              </div>
            </div>
          </form>
          <p class="mt-3 mb-1">
            <a href="<?php echo filter_var($settings['url']); ?>index.php?a=login">Login</a>
          </p>
        </div>
      </div>
    </div>

    <script src="../../assets/new/plugins/jquery/jquery.min.js"></script>
    <script src="../../assets/new/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/new/dist/js/adminlte.min.js"></script>
</body>
</html>



<?php
} elseif($b == "change") {
$hash = protect($_GET['hash']);
if(empty($hash)) { header("Location: $settings[url]"); }
$CheckUser = $db->query("SELECT * FROM users WHERE password_recovery='$hash'");
if($CheckUser->num_rows==0) { header("Location: $settings[url]"); }
$u = $CheckUser->fetch_assoc();
?>
<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title><?php echo filter_var($lang['title_change_account_password']); ?> - <?php echo filter_var($settings['name']); ?></title>
    <meta name="description" content="<?php echo filter_var($settings['description']); ?>">
    <meta name="keywords" content="<?php echo filter_var($settings['keywords']); ?>">
    
    <?php if($settings['favicon']) { ?>
		<link rel="icon" type="image/png" href="<?= $settings['url'].$settings['favicon'] ?>">
	<?php } else { ?>
		<link rel="icon" type="image/png" href="<?= $settings['url'] ?>assets/logo/favicon.png">
	<?php } ?>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../../assets/new/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../assets/new/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/new/dist/css/adminlte.min.css">
</head>
    <body class="hold-transition login-page">
        <div class="login-box">
          <div class="card card-outline card-primary">
            <div class="card-header text-center">
              <a href="<?= $settings['url']; ?>index.php" class="h1"><b><?= $settings['name']; ?></b></a>
            </div>
            <div class="card-body">
              <p class="login-box-msg">You are only one step a way from your new password, recover your password now.</p>
              <?php
                $HideChangeForm = 0;
                $FormBTN = protect($_POST['pw_change']);
                if($FormBTN == "change") {
                    $password = protect($_POST['password']);
                    $cpassword = protect($_POST['cpassword']);
                    if(empty($password)) { 
                        echo error($lang['error_42']);
                    } elseif(strlen($password)<8) { 
                        echo error($lang['error_43']);
                    } elseif($password !== $cpassword) {
                        echo error($lang['error_44']);
                    } else {
                        $password = password_hash($password, PASSWORD_DEFAULT);
                        $update = $db->query("UPDATE users SET password='$password',password_recovery='' WHERE id='$u[id]'");
                        echo success($lang['success_21']);
                        $HideChangeForm=1;
                    }
                }
                if($HideChangeForm==0) {
              ?>
              <form action="" method="post">
                <div class="input-group mb-3">
                  <input type="email" name="email" disabled value="<?php echo filter_var($u['email']); ?>" placeholder="Email address" class="form-control">
                  <div class="input-group-append">
                    <div class="input-group-text">
                      <span class="fas fa-envelope"></span>
                    </div>
                  </div>
                </div>
                <div class="input-group mb-3">
                  <input type="password" name="password" placeholder="<?php echo filter_var($lang['placeholder_13']); ?>" required class="form-control">
                  <div class="input-group-append">
                    <div class="input-group-text">
                      <span class="fas fa-lock"></span>
                    </div>
                  </div>
                </div>
                <div class="input-group mb-3">
                  <input type="password" name="cpassword" placeholder="<?php echo filter_var($lang['placeholder_14']); ?>" required class="form-control">
                  <div class="input-group-append">
                    <div class="input-group-text">
                      <span class="fas fa-lock"></span>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <button type="submit" name="pw_change" value="change" class="btn btn-primary btn-block"><?php echo filter_var($lang['btn_17']); ?></button>
                  </div>
                </div>
              </form>
              <?php } ?>
              <p class="mt-3 mb-1">
                <a href="<?php echo filter_var($settings['url']); ?>index.php?a=login">Login</a>
              </p>
            </div>
          </div>
        </div>
        <script src="../../assets/new/plugins/jquery/jquery.min.js"></script>
        <script src="../../assets/new/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="../../assets/new/dist/js/adminlte.min.js"></script>
    </body>
</html>
<?php
} else {
    header("Location: $settings[url]");
}
?>