<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
if(!checkSession()) {
    $redirect = $settings['url']."index.php?a=login";
    header("Location: $redirect");
}
$myuser_infoQuery= $db->query("SELECT * FROM users WHERE id=".$_SESSION['pw_uid']); 
$myuser_info = $myuser_infoQuery->fetch_assoc();
?>
<style>
* {
margin: 0px;
padding: 0px;
transition: all 0.5s ease 0s;
-webkit-transition: all 0.5s ease 0s;
-moz-transition: all 0.5s ease 0s;
-ms-transition: all 0.5s ease 0s;
-o-transition: all 0.5s ease 0s;
}
html, body, address, blockquote, div, dl, form, h1, h2, h3, h4, h5, h6, ol, p, pre, table, ul, dd, dt, li, tbody, td, tfoot, th, thead, tr, button, del, ins, map, object, a, abbr, acronym, b, bdo, big, br, cite, code, dfn, em, i, img, kbd, q, samp, small, span, strong, sub, sup, tt, var, legend, fieldset, p {
margin: 0;
padding: 0;
border: none;
}
a, input, select, textarea {
outline: none;
margin: 0;
padding: 0;
}
a:hover,focus{
text-decoration:none;
outline: none;
border: none;
}
img, fieldset {
border: 0;
}
a {
outline: none;
border: none;
}
img {
max-width: 100%;
height: auto;
width: auto\9;
vertical-align: middle;
border: none;
outline: none;
}
article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {
display: block;
margin: 0;
padding: 0;
}
div, h1, h2, h3, h4, span, p, input, form, img, hr, img, a {
margin: 0;
padding: 0;
border: none;
}
.mt-30, .mb-30{margin: 30px 0;}
.clear {
clear: both;
}
.share-boxes p {margin: 15px 0 0; font-size: 15px; font-weight: bold;}
.share-boxes {background: #f9f9f9; text-align: center; border-radius: 10px;  box-shadow: 0 0 17px #ccc;
padding: 20px 0;  position: relative;}
.share-boxes img.dotted-line {position: absolute; left: -167px; top: 5px; transform: rotate(-3deg);}
.share-boxes img.dotted-line2 {position: absolute; right: -173px; top: 5px; transform: rotate(-4deg);}
.refer-image img {width: 100%;}
.refer-form ul li {float: left; list-style: none; width: 33.333%; text-align: center;}
.refer-form ul li a {background: #9fb0f8; display: block; padding: 14px; color: #fff; text-transform: uppercase;
font-weight: 600;}
.refer-form ul {margin: 0;}
.refer-form ul li.facebook-color a{background: #9fb0f8}
.refer-form ul li.youtube-color a{background: #eb8c8c}
.refer-form ul li.twitter-color a{background: #9cd0fc}
.refer-form ul li.facebook-color a:hover{background: #4667f7; text-decoration: none;}
.refer-form ul li.youtube-color a:hover{background: #dd2020; text-decoration: none;}
.refer-form ul li.twitter-color a:hover{background: #40a7ff; text-decoration: none;}
.refer-form-content {float: left; width: 100%; background: #f9f9f9; padding: 30px; }
.refer-form-content h2 {color: #ffc3c9; font-weight: bold; text-transform: uppercase; font-size: 25px; margin: 0 0 10px; }
.refer-form-content P a {color: #ffc3c9; font-weight: 500; }
.refer-form-content input{height: 50px; width: 100%; padding: 15px; border-radius: 1px; margin-bottom: 20px; box-shadow: 0 0 6px #ccc; }
.container-checkbox {display: block; position: relative; padding-left: 30px; margin-bottom: 12px; cursor: pointer; font-size: 16px; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; }
.container-checkbox input {position: absolute; opacity: 0; cursor: pointer; height: 0; width: 0; } 
.checkmark {position: absolute; top: 3px; left: 0; height: 20px; width: 20px; background-color: transparent; border: 2px solid #ffc3c9; }
.container-checkbox:hover input ~ .checkmark {background-color: #ccc; } 
.container-checkbox input:checked ~ .checkmark {background-color: #ffc3c9; } 
.checkmark:after {content: ""; position: absolute; display: none; } 
.container-checkbox input:checked ~ .checkmark:after {display: block; } 
.container-checkbox .checkmark:after {left: 5px; top: 0px; width: 7px; height: 12px; border: solid white; border-width: 0 3px 3px 0; -webkit-transform: rotate(45deg); -ms-transform: rotate(45deg); transform: rotate(45deg); } 
.refer-form-content form button {background: #ffc3c9; color: #fff; font-weight: 500; font-size: 18px; width: 100%; height: 50px; cursor: pointer; } 
.refer-form-content form button:hover{background: #000;}
.refer-form-content input::placeholder{color:#c5c5c5; font-size: 14px;}
.row.refer-form-sec {height: 450px; overflow: hidden; margin-top: 55px; }
.referal-progress table td:nth-child(2) {text-align: right; } 
.referal-progress table td {border: 1px solid #cccc; padding: 15px 20px; } 
.row.refer-form-sec .col:first-child {padding-right: 0; } 
.row.refer-form-sec .col:last-child {padding-left: 0; }
.referal-progress h2 {color: #ffc3c9; font-size: 22px; margin: 10px 0 15px; }
.share-boxes:after {content: ""; background: url("https://i.ibb.co/WHdS3G1/circle.png") no-repeat 0 0; position: absolute; left: 0; right: 0; bottom: -65px; margin: 0 auto; z-index: 99999; height: 60px; width: 20px; }
@media only screen and (max-width: 1100px){
.share-boxes img.dotted-line, .share-boxes img.dotted-line2 {
display: none;
}

}
@media only screen and (max-width: 767px){
.share-boxes {
margin: 0 0 52px;
}
.row.refer-form-sec {
height: auto;
overflow: hidden;
margin-top: 55px;
display: block;
}
.row.refer-form-sec .col:first-child {
padding-right: 15px;
margin: 0 0 30px;
}
.row.refer-form-sec .col:last-child {
padding-left: 15px;
}
}
@media only screen and (max-width: 380px){
.refer-form ul li a {
padding: 9px;
font-size: 14px;
}
.refer-form-content h2 {
font-size: 22px;
}
}
</style>
    <?php 
    
    $refQuery = $db->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
	$row = $refQuery->fetch_assoc();
			
	?>
    <div class="container">
        <div class="row mt-30 mb-30">
          <div class="col-sm-12 col-md-3">
            <div class="share-boxes">
              <img src="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>assets/images/1.png" alt="img1" border="0">
              <p>Share with your friends</p>
            </div>
          </div>
          <div class="col"></div>
          <div class="col-sm-12 col-md-3">
            <div class="share-boxes">
              <img src="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>assets/images/2.png" alt="img2" border="0">
              <p><?php echo filter_var($row['ref_com'], FILTER_SANITIZE_STRING); ?>% Referral Commission</p>
              <img src="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>assets/images/dotted-arrow1.png" alt="dotted-arrow1" class="dotted-line">
              <img src="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>assets/images/dotted-arrow2.png" alt="dotted-arrow2" class="dotted-line2">
            </div>
          </div>
          <div class="col"></div>
          <div class="col-sm-12 col-md-3">
            <div class="share-boxes">
              <img src="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>assets/images/3.png" alt="img3" border="0">
              <p>Enjoy happy earning</p>
            </div>
          </div>
        </div>
        <div class="row refer-form-sec">
          <div class="col">
            <div class="refer-image">
              <img src="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>assets/images/4.jpg" alt="big-image" border="0" />
            </div>
          </div>
          <div class="col">
            <div class="refer-form">
              <ul>
                <li class="facebook-color"><a target="_blank" href="https://www.facebook.com/sharer.php?u=<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>index.php?ref=<?php echo filter_var($_SESSION['pw_uid'], FILTER_SANITIZE_STRING); ?>">Facebook</a></li>
                <li class="youtube-color"><a href="#">you tube</a></li>
                <li class="twitter-color"><a href="#">twitter</a></li>
              </ul>
            </div>
            <div class="refer-form-content">
              <h2>Friends To Friends</h2>
              <p>Share & Earn.</p>
              <br>
              <form action="#" method="post">
                <input type="text" value="<?php echo filter_var($settings['url'], FILTER_SANITIZE_STRING); ?>index.php?ref=<?php echo filter_var($_SESSION['pw_uid'], FILTER_SANITIZE_STRING); ?>" id="myInput">
                <button type="button" onclick="myFunction()" onmouseout="outFunc()"><span class="tooltiptext" id="myTooltip">COPY, SHARE, REFER & EARN</span></button>
              </form>
            </div>
          </div>
        </div>
        <div class="row mt-30 mb-30">
          <div class="col">
            <div class="referal-progress">
              <h2>YOUR REFERAL PROGRESS</h2>
              <table class="table table-hover">
                <tbody>
                  <tr>
                    <td>Total Referrals</td>
                    <td><strong><?php $query = $db->query("SELECT * FROM users WHERE ref1='$_SESSION[pw_uid]' "); echo filter_var($query->num_rows, FILTER_SANITIZE_STRING); ?></strong></td>
                  </tr>
                  <tr>
                    <td>Commission Earned in <?= $settings['default_currency'] ?></td>
                    <td><strong>
                    <?php
    			        $email = idinfo($_SESSION['pw_uid'],"email");
                        $GetUserWallets = $db->query("SELECT SUM(commission) FROM bonus_logs WHERE user_email='$email' and currency='$settings[default_currency]'");
                        if($GetUserWallets->num_rows>0) {
                            while($guw = $GetUserWallets->fetch_assoc()) { ?>
                            <?= $settings['default_currency']; ?> <?= $guw['SUM(commission)'] ?>
                        <?php } } ?>
                    </strong></td>
                  </tr>
                  <tr>
                    <td></td>
                    <td><strong></strong></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
    </div>
    <script>
    function myFunction() {
      var copyText = document.getElementById("myInput");
      copyText.select();
      copyText.setSelectionRange(0, 99999);
      document.execCommand("copy");
      
      var tooltip = document.getElementById("myTooltip");
      tooltip.innerHTML = "Copied!";
    }
    
    function outFunc() {
      var tooltip = document.getElementById("myTooltip");
      tooltip.innerHTML = "Copy";
    }
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
</body>
</html>
