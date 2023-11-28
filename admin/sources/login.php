<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
     $url = "https://";   
else  
     $url = "http://";   
// Append the host(domain name, ip) to the URL.   
$url.= $_SERVER['HTTP_HOST'];   

// Append the requested resource location to the URL   
$url.= $_SERVER['REQUEST_URI'];    
?>
<!DOCTYPE html>
<html lang="en" >
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Admin Panel - <?php echo filter_var($settings['name'], FILTER_SANITIZE_STRING); ?></title>
        <meta name="description" content="Control Panel - <?php echo filter_var($settings['name'], FILTER_SANITIZE_STRING); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <link rel="stylesheet" href="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>assets/new/plugins/fontawesome-free/css/all.min.css">
        <link rel="stylesheet" href="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>assets/new/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>assets/new/dist/css/adminlte.min.css">
    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
          <div class="card card-outline card-primary">
            <div class="card-header text-center">
              <a href="#" class="h1"><b>Admin</b>Panel</a>
            </div>
            <div class="card-body">
              <p class="login-box-msg">Sign in to start your session</p>
              
              <?php
            	if(isset($_POST['bit_login'])) {
            		$username = protect($_POST['username']);
            		$password = protect($_POST['password']);
            		$check = $db->query("SELECT * FROM users WHERE account_user='$username'");
            		if($check->num_rows>0) {
                        $row = $check->fetch_assoc();
                        if(password_verify($password, $row['password'])) {
                            if($row['account_level'] == "666") {
                                $_SESSION['admin_uid'] = $row['id'];
                                $time = time();
                                $login_ip = $_SERVER['REMOTE_ADDR'];
                                $insert = $db->query("INSERT users_logs (uid,type,time,u_field_1) VALUES ('$_SESSION[admin_uid]','2','$time','$login_ip')");
                                header("Location: $url");
                            } else { ?>
                                <div class="callout callout-danger"><p>You do not have privileges to do that!</p></div>
                            <?php }
                        } else { ?>
                            <div class="callout callout-danger"><p>Wrong username or password.</p></div>
                        <?php }
            		} else { ?>
            			<div class="callout callout-danger"><p>Wrong username or password.</p></div>
            		<?php }
            	}
              ?>
              
                  

                  
              <form action="" method="post">
                <div class="input-group mb-3">
                  <input type="text" class="form-control" name="username" placeholder="Username">
                  <div class="input-group-append">
                    <div class="input-group-text">
                      <span class="fas fa-user"></span>
                    </div>
                  </div>
                </div>
                <div class="input-group mb-3">
                  <input type="password" class="form-control" placeholder="Password" name="password">
                  <div class="input-group-append">
                    <div class="input-group-text">
                      <span class="fas fa-lock"></span>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <button type="submit" name="bit_login" value="Authorized" class="btn btn-primary btn-block" style="width:100%;">Sign In</button>
                  </div>
                  <!-- /.col -->
                </div>
              </form>
            </div>
          </div>
        </div>
    
        <script src="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>assets/new/plugins/jquery/jquery.min.js"></script>
        <script src="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>assets/new/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>assets/new/dist/js/adminlte.min.js"></script>
        <script>
            if ( window.history.replaceState ) {
              window.history.replaceState( null, null, window.location.href );
            }
        </script>
    </body>
</html>
