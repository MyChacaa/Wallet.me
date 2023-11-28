<section class="darkblueGrad contactHoe flex jcb" id="contact">
    <div class="w100" align="right">
		<div class="leftContact " align="center">
			<h2 class="fs35 fw3 mb15 cwhite headResp"><?= $lang["Have_a_question_in_mind?"]; ?></h2>
			<p class="fs20 cwhite contactPara"><?= $lang["contact_text"]; ?></p>
                <?php
				if (isset($_POST['send'])){
                $FormBTN = protect($_POST['send']);
				} else {
				$FormBTN = "";
				}
                if($FormBTN == "message") {
                    $name = protect($_POST['name']);
                    $email = protect($_POST['email']);
                    $subject = protect($_POST['subject']);
                    $message = protect($_POST['message']);
                    if(empty($name) or empty($email) or empty($subject) or empty($message)) {
                        echo error($lang['error_20']);
                    } elseif(!isValidEmail($email)) {
                        echo error($lang['error_34']);
                    } else {
                        $mail = new PHPMailer;
                        $mail->isSMTP();
                        $mail->SMTPDebug = 0;
                        $mail->Host = $smtpconf["host"];
                        $mail->Port = $smtpconf["port"];
                        $mail->SMTPAuth = $smtpconf['SMTPAuth'];
                        $mail->Username = $smtpconf["user"];
                        $mail->Password = $smtpconf["pass"];
                        $mail->setFrom($email, $name);
                        $mail->addAddress($settings['supportemail'], $settings['supportemail']);
                        //Set the subject line
                        $lang = array();
                        $mail->Subject = $subject;
                        $mail->msgHTML($message);
                        //Replace the plain text body with one created manually
                        $mail->AltBody = $message;
                        //Attach an image file
                        //send the message, check for errors
                        $send = $mail->send();
                        if($send) {
                            echo success("You Query has been received.");
                        } else {
                            echo error("Some thing is wrong.");
                        }
                    }
                }
                ?>
			<form action="" method="POST">
    			<div class="row">
    				<div class="col-md-6">
    					<input type="text" name="name" class="inpHone" placeholder="Full name">
    				</div>
    				<div class="col-md-6">
    					<input type="email" name="email" class="inpHone" placeholder="Email address">
    				</div>
    				<div class="col-md-12">
    					<input type="text" name="subject" class="inpHone" placeholder="<?php echo filter_var($lang['placeholder_9']); ?>">
    				</div>
    				<div class="col-md-12">
    					<textarea name="message" class="inpHone txtareaHome" placeholder="<?php echo filter_var($lang['placeholder_10']); ?>"></textarea>
    				</div>
    				<div class="col-md-12">
    					<button type="submit" name="send" value="message" class="submitBtnHome"><?php echo filter_var($lang['btn_26']); ?> &nbsp;<i class="fa fa-caret-right"></i></button>
    				</div>
    			</div>
    		</form>
		</div>
	</div>
    <div class="rightContact"></div>
</section>

<footer>
	<div class="container tac">
		<div class="row">
			<div class="col-md-8 m20">
				<ul class="footLink1">
					<li><a href="<?= $settings['url']; ?>"><?= $lang['home'] ?></a></li>
					<li><a href="JavaScript:"><?= $lang['about_us'] ?></a></li>
					<li><a href="#how"><?= $lang['how_we_work'] ?></a></li>
					<li><a href="<?= $settings['url']; ?>merchant"><?= $lang['merchant_ipn']; ?></a></li>
					<li><a href="<?= $settings['url']; ?>login"><?= $lang['join_us']; ?></a></li>
					<li><a href="#contact"><?= $lang['contact_us']; ?></a></li>
				</ul>
			</div>
			<div class="col-md-4 m20" align="right">
				<ul class="footLink1 tac">
					<li><a href="JavaScript:"><?= $lang['footer_email']; ?>:</a></li>
					<li><a href="mailto:<?= $settings['supportemail']; ?>" class="m0"><?= $settings['supportemail']; ?></a></li>
				</ul>
			</div>
		</div> <!-- row -->
	</div> <!-- container -->
	<hr class="hr1">
	<div class="container">
		<div class="row">
			<div class="col-md-5 m20">
				<a href="<?= $social['facebook_profile']; ?>"><i class="fa fa-facebook socialIconFoot" aria-hidden="true"></i></a>
				<a href="<?= $social['twitter_profile']; ?>"><i class="fa fa-twitter socialIconFoot" aria-hidden="true"></i></a>
				<a href="<?= $social['linkedin_profile']; ?>"><i class="fa fa-linkedin socialIconFoot" aria-hidden="true"></i></a>
			</div>
			<div class="col-md-7 m20" align="right">
				<ul class="footLink1">
					<li class="copyrightTxt" >© 2021 <?= $settings['name']; ?>. <?= $lang['all_right_reserved']; ?></li>
					<li><a href="JavaScript:" class="fw2"><?= $lang['terms_of_use']; ?></a></li>
					<li><a href="JavaScript:" class="fw2"><?= $lang['privacy_policy']; ?></a></li>
					<li><a href="JavaScript:" class="fw2"><?= $lang['faqs']; ?></a></li>
				</ul>
			</div>
		</div>
	</div>
</footer>
<script type="text/javascript">

    $(window).scroll(function(){var sticky = $('header'),scroll = $(window).scrollTop();if (scroll >= 100) sticky.addClass('fixedHead');else sticky.removeClass('fixedHead');});
    $(document).mouseup(function(e) {var container = $(".drodpwnToggle, .dropdownLi");if (!container.is(e.target) && container.has(e.target).length === 0) {container.hide();$(".dropdownLi").show();}});
    $(document).ready(function(){$(".bars").click(function(){ $(".mainUl").slideToggle(); });$(".dropdownLi").click(function(){$(this).parent().children(".drodpwnToggle").fadeToggle('fast');});$(window).resize(function(){if($(window).width() <= 992) {$(".mainUl").hide();}else{$(".mainUl").show();}});
    $('.servSlider').slick({infinite: true,slidesToShow: 2,autoplay: true,arrows: true,autoplaySpeed: 2000,
    prevArrow: '<div class="slick-prev"><i class="fa fa-angle-left" aria-hidden="true"></i></div>',
    nextArrow: '<div class="slick-next"><i class="fa fa-angle-right" aria-hidden="true"></i></div>',
    responsive: [{breakpoint: 768,settings: {arrows: false,centerMode: true,centerPadding: '40px',slidesToShow: 3}},{breakpoint: 480,settings: {arrows: false,centerMode: true,centerPadding: '40px',slidesToShow: 1}}]});});
    if ( window.history.replaceState ) {window.history.replaceState( null, null, window.location.href );}
    
</script>

<script src="../assets/front/js/slick.min.js"></script>
<?php if ($m["live_chat"] == "1") { ?>
<?= $settings['live_chat_code'] ?>
<?php } ?>
</body>
</html>