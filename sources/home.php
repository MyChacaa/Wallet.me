<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
?>

<?php include("menu_notlogged.php"); ?>

<section class="slider rel" id="home">

	<style type="text/css">
		header{ background: transparent; }
	</style>
	
	<div class="container">
		<div class="row">
			<div class="col-lg-6 col-lg-offset-3 tac" align="center">
				<h2 class="cwhite fs50 fw3 headResp"><?= $lang['home_intro'] ?>
					</h2>

				<p class="cwhite fs18 m40"><?= $lang['home_intro_2'] ?></p>


				<div class="flexImp">
					<a href="<?= $settings['url']; ?>index.php?a=account&b=money&c=send" class="cwhite td inline btnSlider fs18 fw3 inFlex ai">
						<img src="<?= $settings['url']; ?>assets/front/img/slidIcon1.png">
						<div class="cont">
							<p class="fs22"><?= $lang['head_send_money'] ?></p>
						</div>
					</a>
									
					<a href="<?= $settings['url']; ?>index.php?a=account&b=money&c=request" class="cwhite td inline btnSlider fs18 fw3 inFlex ai">
						<img src="<?= $settings['url']; ?>assets/front/img/slidIcon2.png">
						<div class="cont">
							<p class="fs22"><?= $lang['receive_money'] ?></p>
						</div>
					</a>

				</div>


			</div>
		</div>
	</div>
	<a href="#scrollDown"><img src="<?= $settings['url']; ?>assets/front/img/bottomSliderImg.png" class="bottomSliderImg abs" id="scrollDown"></a>
</section>
    
<section class="bottomSlid rel" id="how">
	<div class="container">
			
		<div class="row" >
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-3">
						<h2 class="fw3 mb20 headResp cred fs40"><?= $lang["how_we_work"] ?>?</h2>
					</div>	
					<div class="col-md-4 col-md-offset-1">
						<p><?= $lang['text_1'] ?></p>
					</div>	
					<div class="col-md-4">
						<p><?= $lang['text_2'] ?></p>
					</div>	
				</div>	
			</div>
		</div>

		<div class="row">

			<div class="col-md-4">
				<div class="bottomBox flex ">
					<div class="cont mr25">
						<span class="btImg flex ai jc"><img src="<?= $settings['url']; ?>assets/front/img/b1.png"></span>
					</div>
					<div class="cont">
						<h4><?= $lang['f_1_text_1'] ?></h4>
						<p><?= $lang['f_1_text_1_2'] ?></p>
					</div>
				</div> <!-- bottomBox -->
			</div>

			<div class="col-md-4">
				<div class="bottomBox flex ">
					<div class="cont mr25">
						<span class="btImg flex ai jc"><img src="<?= $settings['url']; ?>assets/front/img/b2.png"></span>
					</div>
					<div class="cont">
						<h4><?= $lang['f_1_text_2'] ?></h4>
						<p><?= $lang['f_1_text_2_2'] ?></p>
					</div>
				</div> <!-- bottomBox -->
			</div>

			<div class="col-md-4">
				<div class="bottomBox bottomBox2 flex ">
					<div class="cont mr25">
						<span class="btImg flex ai jc"><img src="<?= $settings['url']; ?>assets/front/img/b3.png"></span>
					</div>
					<div class="cont">
						<h4><?= $lang['f_1_text_3'] ?></h4>
						<p><?= $lang['f_1_text_3_2'] ?></p>
					</div>
				</div> <!-- bottomBox -->
			</div>



		</div> <!-- row -->

		<div class="row">

			<div class="col-md-3 col-sm-6">
				<div class="statBox">
					<div class="cont">
						<img src="<?= $settings['url']; ?>assets/front/img/m1.png" class="statImg">
					</div>
					<div class="cont">
						<p><?= $lang['member'] ?></p>
						<h1><?php $query = $db->query("SELECT * FROM users"); echo filter_var($query->num_rows, FILTER_SANITIZE_STRING); ?>+</h1>
					</div>
				</div>
			</div>

			<div class="col-md-3 col-sm-6">
				<div class="statBox">
					<div class="cont">
						<img src="<?= $settings['url']; ?>assets/front/img/m2.png" class="statImg">
					</div>
					<div class="cont">
						<p><?= $lang['transaction'] ?></p>
						<h1><?php $query = $db->query("SELECT * FROM transactions"); echo filter_var($query->num_rows, FILTER_SANITIZE_STRING); ?>+</h1>
					</div>
				</div>
			</div>

			<div class="col-md-3 col-sm-6">
				<div class="statBox">
					<div class="cont">
						<img src="<?= $settings['url']; ?>assets/front/img/m3.png" class="statImg">
					</div>
					<div class="cont">
						<?php 
                            foreach($db->query("SELECT SUM(amount) FROM deposits WHERE status= '1'") as $row) 
                            $total_deposit = number_format($row['SUM(amount)'], 0, '.', '');  
                        ?>
						<p><?= $lang['money_raised'] ?></p>
						<h1>$<?php echo filter_var($total_deposit, FILTER_SANITIZE_STRING);?>+</h1>
					</div>
				</div>
			</div>

			<div class="col-md-3 col-sm-6">
				<div class="statBox">
					<div class="cont">
						<img src="<?= $settings['url']; ?>assets/front/img/m4.png" class="statImg">
					</div>
					<div class="cont">
						<?php 
                            foreach($db->query("SELECT SUM(amount) FROM transactions WHERE status= '1'") as $row) 
                            $total_transaction = number_format($row['SUM(amount)'], 0, '.', '');  
                        ?>
						<p><?= $lang['total_transaction'] ?></p>
						<h1>$<?php echo filter_var($total_transaction, FILTER_SANITIZE_STRING);?>+</h1>
					</div>
				</div>
			</div>


		</div>

	</div>
</section>
<section class="servicesCont">

	<div class="container">
		<div class="row">
			
			<div class="col-md-4" align="center">
				<h2 class="fw3 fs40 head head"><?= $lang['effective_efficent'] ?></h2>
				<p><?= $lang['effective_efficent_text'] ?><br><br>

					<?= $lang['effective_efficent_text_2'] ?>
				</p>
			</div>

			<div class="col-md-8">

				<div class="row">

			<div class="partnerBox">
			<div class="col-md-12" align="center">
				<h2 class="head2 rel fs35 cgray fw3"><span class="txt rel"><?= $lang['thanks_for_sponsor'] ?></span></h2>
				<br><br>
			</div>

			<div class="col-md-12">

					<div class="partnersCont">
						<div class="flex ai jc">
							<div class="cont">
								<img src="<?= $settings['url']; ?>assets/front/img/ssl.png">
							</div>
							<div class="cont">
								<img src="<?= $settings['url']; ?>assets/front/img/mcfee.png">
							</div>
							<div class="cont">
								<img src="<?= $settings['url']; ?>assets/front/img/nt.png">
							</div>
							<div class="cont">
								<img src="<?= $settings['url']; ?>assets/front/img/pm.png">
							</div>
							<div class="cont">
								<img src="<?= $settings['url']; ?>assets/front/img/py.png">
							</div>
							
							<div class="cont">
								<img src="<?= $settings['url']; ?>assets/front/img/sk.png">
							</div>
							<div class="cont">
								<img src="<?= $settings['url']; ?>assets/front/img/pye.png">
							</div>
							<div class="cont">
								<img src="<?= $settings['url']; ?>assets/front/img/pp.png">
							</div>
						</div>

					</div> <!-- col -->
				</div>
			</div>
		</div> <!-- row -->
				
			</div>
		</div>


		


	</div>


</section>

<?php include("footer.php"); ?>