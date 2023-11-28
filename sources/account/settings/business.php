<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
if ($m["merchants"] !== "1") {
    $redirect = $settings['url']."account/summary";
    header("Location: $redirect");
}
// business status = 1 its able to accept/approved
// = 2 its pending    /done-done
// = 3 require documents
// = 4 its rejected   /done-done
// = "" not applied   /done-done
// = 5 cancel by user /done-done
// = 6 Block          /done

if(!checkSession()) {
    $redirect = $settings['url']."login";
    header("Location: $redirect");
}
if(idinfo($_SESSION['pw_uid'],"account_type") == "1") {
    $redirect = $settings['url']."account/profile";
    header("Location: $redirect");
}
if (idinfo($_SESSION['pw_uid'],"business_status") == "6") {
    echo error("Your Business was blocked. We are not able to support your business at this time. You can use our all service except accept payments online. If you think this is a mistake please contact support team.");
} else {
?>
<h3>Business Application</h3>
<hr/>
<?php
$FormBTN = protect($_POST['save_business']);
if (idinfo($_SESSION['pw_uid'],"business_status") == "" or idinfo($_SESSION['pw_uid'],"business_status") == "5" or idinfo($_SESSION['pw_uid'],"business_status") == "4") {
if($FormBTN == "business_approval") {
    $business_name = protect($_POST['business_name']);
    $business_website = protect($_POST['business_website']);
    $business_website = filter_var($business_website, FILTER_SANITIZE_URL);
    $commission = protect($_POST['commission']); //1= Merchant, 2= Client/Customer
    $business_category = protect($_POST['business_category']);
    $business_country = protect($_POST['business_country']);
    $business_description = protect($_POST['business_description']);
    $business_status = 2;
    
    if(empty($business_name) or empty($business_website) or empty($commission) or empty($business_category) or empty($business_country) or empty($business_description)) {
        echo error($lang['error_20']);
    } else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $business_description)) {
        echo error("Invalid Characters are not allowed.");
    } else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $business_category)) {
        echo error("Invalid Characters are not allowed.");
    }else if (!filter_var($business_website, FILTER_VALIDATE_URL)) {
        echo error("Url is not valid");    
    } else {
        $update = $db->query("UPDATE users SET business_name='$business_name',business_website='$business_website',business_who_pay_fee='$commission',business_category='$business_category',business_country='$business_country',business_description='$business_description',business_status='$business_status' WHERE id='$_SESSION[pw_uid]'");
    }
}
}


if($FormBTN == "business_cancel") {
    $business_status = 5;
    $update = $db->query("UPDATE users SET business_status='$business_status' WHERE id='$_SESSION[pw_uid]'");
}

if($FormBTN == "business_update") {
    $business_who_pay_fee = protect($_POST['commission']); //1= Merchant, 2= Client/Customer
    $update = $db->query("UPDATE users SET business_who_pay_fee='$business_who_pay_fee' WHERE id='$_SESSION[pw_uid]'");
    echo success("Comission Setting updated.");
}
if($FormBTN == "business_doc") {
    $business_status = 2;
    $update = $db->query("UPDATE users SET business_status='$business_status' WHERE id='$_SESSION[pw_uid]'");
}
if (idinfo($_SESSION['pw_uid'],"business_status") == "4") {
    $reason = idinfo($_SESSION[pw_uid],"business_reject");
    echo error("Your Business was rejected due to  <b>$reason</b>. Solve this issues and re-submit again.");
}
if (idinfo($_SESSION['pw_uid'],"business_status") == "1") {
    echo success("Your Business was Approved.");
}
if (idinfo($_SESSION['pw_uid'],"business_status") == "3") {
    echo info("Required additional details, Please upload KYC Documentation to get approval.");
}
?>

<form class="user-connected-from user-signup-form" action="" method="POST">
    <div class="row form-group">
        <div class="col">
            <label>Business Name</label>
            <input type="text" class="form-control" name="business_name" value="<?php echo idinfo($_SESSION['pw_uid'],"business_name"); ?>" <?php if (idinfo($_SESSION['pw_uid'],"business_status") == "2" or idinfo($_SESSION['pw_uid'],"business_status") == "1" or idinfo($_SESSION['pw_uid'],"business_status") == "3") { echo "disabled"; }?>>
        </div>
        <div class="col">
            <label>Business Website</label>
            <input type="text" class="form-control" name="business_website" placeholder="http://www.domain.com/" value="<?php echo idinfo($_SESSION['pw_uid'],"business_website"); ?>" <?php if (idinfo($_SESSION['pw_uid'],"business_status") == "2" or idinfo($_SESSION['pw_uid'],"business_status") == "1" or idinfo($_SESSION['pw_uid'],"business_status") == "3") { echo "disabled"; }?>>
        </div>
    </div>
    <div class="row form-group">
        <div class="col">
            <label>Who Pays Commission</label>
            <select class="form-control" name="commission" <?php if (idinfo($_SESSION['pw_uid'],"business_status") == "2"  or idinfo($_SESSION['pw_uid'],"business_status") == "3") { echo "disabled"; }?>>
                <?php
                if (idinfo($_SESSION['pw_uid'],"business_who_pay_fee") == "1") {
                    $select_com1 = "selected";    
                } elseif (idinfo($_SESSION['pw_uid'],"business_who_pay_fee") == "2") {
                    $select_com2 = "selected";    
                }
                ?>
                <option value="1"<?php echo $select_com1 ?> >Merchant</option>
                <option value="2" <?php echo $select_com2 ?> >Client</option>
            </select>
        </div>
        <div class="col">
            <div class="form-group">
                <label>Category/Industry</label>
                <input type="text" class="form-control" name="business_category" value="<?php echo idinfo($_SESSION['pw_uid'],"business_category"); ?>" <?php if (idinfo($_SESSION['pw_uid'],"business_status") == "2"  or idinfo($_SESSION['pw_uid'],"business_status") == "1"  or idinfo($_SESSION['pw_uid'],"business_status") == "3") { echo "disabled"; }?>>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>Country of Business</label>
        <select class="form-control" name="business_country" <?php if (idinfo($_SESSION['pw_uid'],"business_status") == "2" or idinfo($_SESSION['pw_uid'],"business_status") == "1"  or idinfo($_SESSION['pw_uid'],"business_status") == "3") { echo "disabled"; }?>>
            <?php
			$country_Query = $db->query("SELECT * FROM country WHERE status='1'");
            while($country = $country_Query->fetch_assoc()) {
                echo '<option value="'.$country['code'].'">'.$country['name'].'</option>';
            }
            ?>
        </select>
    </div>
    <div class="form-group">
        <label>Business Description</label>
        <textarea type="text" class="form-control" name="business_description" <?php if (idinfo($_SESSION['pw_uid'],"business_status") == "2"  or idinfo($_SESSION['pw_uid'],"business_status") == "1"  or idinfo($_SESSION['pw_uid'],"business_status") == "3") { echo "disabled"; }?>><?php echo idinfo($_SESSION['pw_uid'],"business_description"); ?></textarea>
    </div>
    <?php if (idinfo($_SESSION['pw_uid'],"business_status") == "" or idinfo($_SESSION['pw_uid'],"business_status") == "0" or idinfo($_SESSION['pw_uid'],"business_status") == "5" or idinfo($_SESSION['pw_uid'],"business_status") == "4") { ?>
    <button type="submit" name="save_business" value="business_approval"  class="btn btn-primary" style="padding:12px;">Apply for Approval</button>
    <?php  } ?>
    <?php if (idinfo($_SESSION['pw_uid'],"business_status") == "2") { ?>
    <?php echo info("Your Business was pending approval."); ?>
    <button type="submit" name="save_business" value="business_cancel"  class="btn btn-danger" style="padding:12px;">Cancel Application</button>
    <?php } ?>
    <?php if (idinfo($_SESSION['pw_uid'],"business_status") == "1") { ?>
    <button type="submit" name="save_business" value="business_update"  class="btn btn-success" style="padding:12px;">Update Setting</button>
    <?php } ?>
    <?php if (idinfo($_SESSION['pw_uid'],"business_status") == "3" && idinfo($_SESSION['pw_uid'],"document_verified") == "1") { ?>
    <button type="submit" name="save_business" value="business_doc"  class="btn btn-info" style="padding:12px;">Submit Again</button>
    <?php } ?>
</form>
<?php } ?>
