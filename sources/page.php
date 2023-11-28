<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$prefix = protect($_GET['prefix']);
$query = $db->query("SELECT * FROM pages WHERE prefix='$prefix'");
if($query->num_rows==0) {
    $redirect = $settings['url'];
    header("Location: $redirect");
}
$row = $query->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php if ($m["google_analytics"] == "1") { ?>
    <?= $settings['google_analytics_code'] ?>
    <?php } ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://use.fontawesome.com/32efc5ddb7.js"></script>
    <link rel="stylesheet" type="text/css" href="<?= $settings['url']; ?>assets/front/css/slick.css"/>
    <link rel="stylesheet" type="text/css" href="<?= $settings['url']; ?>assets/front/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?= $settings['url']; ?>assets/front/css/custom_styles.css">
    <script src="<?= $settings['url']; ?>assets/front/js/jquery.min.js"></script>
    <?php if($settings['favicon']) { ?>
		<link rel="icon" type="image/png" href="<?= $settings['url'].$settings['favicon'] ?>">
	<?php } else { ?>
		<link rel="icon" type="image/png" href="<?= $settings['url'] ?>assets/logo/favicon.png">
	<?php } ?>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <title><?php echo $row['title']; ?> - <?php echo $settings['name']; ?></title>
    <meta name="description" content="<?= $settings['description']; ?>">
    <meta name="keywords" content="<?= $settings['keywords']; ?>">
</head>
<body>

<header>
	<div class="container rel">

		<div class="bars abs pointer">
			<span></span>
			<span></span>
			<span></span>
		</div>

		<div class="row">
			<div class="col-md-3 tac">
				<a href="<?= $settings['url']; ?>index.php">
					<?php if($settings['white_logo']) { ?>
						<img src="<?= $settings['url'].$settings['white_logo'] ?>" class="logo-main">
					<?php } else { ?>
						<img src="<?= $settings['url']; ?>assets/logo/logo-white.png" class="logo-main">
					<?php } ?>
				</a>
			</div>
			<div class="col-md-9" align="right">
				<ul class="mainUl">
					<li><a href="<?= $settings['url']; ?>index.php#home" class="main-link"><?= $lang['home'] ?></a></li>
					<li class="rel">
						<a href="JavaScript:" class="main-link dropdownLi">Features &nbsp;&nbsp;<i class="fa fa-angle-down" aria-hidden="true"></i></a>
						<div class="abs w100 drodpwnToggle">
							<ul class="dropdownUl">
								<li><a href="javascript:"><p>Send money</p>Send money with ease</a></li>
								<li><a href="javascript:"><p>Payments Links</p>Send Payment Links to get paid</a></li>
								<li><a href="javascript:"><p>Referrals</p>Invite and earn life time commission</a></li>
							</ul>
							<ul class="dropdownUl">
								<li><a href="javascript:"><p>Receive funds</p>Receive money internationally</a></li>
								<li><a href="javascript:"><p>Protect pay</p>Pay with protection</a></li>
								<li><a href="javascript:"><p>Refer a friend</p>Refer a friend and earn</a></li>
							</ul>
						</div>
					</li>
					<li><a href="<?= $settings['url']; ?>index.php#how" class="main-link"><?= $lang['how_we_work'] ?></a></li>
					<li><a href="<?= $settings['url']; ?>index.php#contact" class="main-link"><?= $lang['title_contacts'] ?></a></li>
						<?php if(!checkSession()) { ?>
                        <li><a href="<?= $settings['url']; ?>index.php?a=login" class="main-link logBtn"><?= $lang['menu_login'] ?></a></li>
					    <li><a href="<?= $settings['url']; ?>index.php?a=login" class="main-link logBtn"><?= $lang['menu_register'] ?></a></li>
                    <?php } ?>
					<?php if(checkSession()) { ?>
                        <li><a href="<?= $settings['url']; ?>index.php?a=account&b=summary" class="main-link logBtn"><?= $lang['title_summary'] ?></a></li>
    					<li><a href="<?= $settings['url']; ?>index.php?a=logout" class="main-link logBtn"><?= $lang['log_out'] ?></a></li>
                    <?php } ?>
				</ul>
			</div>
		</div>
	</div>
</header>
<style type="text/css">
header{
    background: #<?=$bck_color?>;
}	
</style>
<section class="bottomSlid">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <br><br>
                <div class="card">
                    <div class="card-body">
                        <h3><?php echo $row['title']; ?></h3>
                        <hr/>
                        <?php echo $row['content']; ?>
                    </div>
                </div>
                <br><br>
            </div>
        </div>
    </div>
</section>
<?php include("footer.php"); ?>
