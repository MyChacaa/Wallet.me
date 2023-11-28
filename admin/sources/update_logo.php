<?php
// WebGuard - Advanced PHP Login and User Management PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
?>
<div class="col-md-12">
	<div class="card">
        <div class="card-body">
			<?php 
			if(isset($_POST['logo'])) {
				$extensions = array('jpg','jpeg','png'); 
				$fileextension = end(explode('.',$_FILES['uploadFile']['name'])); 
				$fileextension = strtolower($fileextension); 
				if(empty($_FILES['uploadFile']['name'])) { echo error("Please select a file."); }
				elseif(!in_array($fileextension,$extensions)) { echo error("Allowed extensions: jpg and png."); }
				else {
					$filename = randomHash(10)."_".$_FILES['uploadFile']['name'];
					$sql_upload_path = 'assets/images/'.$filename;
					$upload_path = '../assets/images/'.$filename;
					@move_uploaded_file($_FILES['uploadFile']['tmp_name'],$upload_path);
					$update = $db->query("UPDATE settings SET logo='$sql_upload_path'");
					$settingsQuery = $db->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
                    $settings = $settingsQuery->fetch_assoc();
					echo success("Your logo was updated successfully.");
				}
			}
			?>
							
			<div class="alert">
				<b>Current Dark Logo:</b><br/><br/>
				<?php if($settings['logo']) { ?>
					<img src="<?= $settings['url'].$settings['logo'] ?>">
				<?php } else { ?>
					<img src="<?= $settings['url'] ?>assets/logo/logo_red.png">
				<?php } ?>
			</div>
			
			<form action="" method="POST" enctype="multipart/form-data">
				<div class="form-group">
					<label>Select logo</label>
					<input type="file" class="form-control" name="uploadFile">
				</div>
				<button type="submit" class="btn btn-primary" name="logo"><i class="fa fa-upload"></i> Upload</button>
			</form>
	    </div>
    </div>
    <div class="card">
        <div class="card-body">
			<?php 
			if(isset($_POST['favicon'])) {
				$extensions = array('jpg','jpeg','png'); 
				$fileextension = end(explode('.',$_FILES['uploadFile']['name'])); 
				$fileextension = strtolower($fileextension); 
				if(empty($_FILES['uploadFile']['name'])) { echo error("Please select a file."); }
				elseif(!in_array($fileextension,$extensions)) { echo error("Allowed extensions: jpg and png."); }
				else {
					$filename = randomHash(5)."_".$_FILES['uploadFile']['name'];
					$sql_upload_path = 'assets/images/'.$filename;
					$upload_path = '../assets/images/'.$filename;
					@move_uploaded_file($_FILES['uploadFile']['tmp_name'],$upload_path);
					$update = $db->query("UPDATE settings SET favicon='$sql_upload_path'");
					$settingsQuery = $db->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
                    $settings = $settingsQuery->fetch_assoc();
					echo success("Your favicon was updated successfully.");
				}
			}
			?>
							
			<div class="alert">
				<b>Current Favicon:</b><br/><br/>
				<?php if($settings['favicon']) { ?>
					<img src="<?= $settings['url'].$settings['favicon'] ?>">
				<?php } else { ?>
					<img src="<?= $settings['url'] ?>assets/logo/favicon.png">
				<?php } ?>
			</div>
			
			<form action="" method="POST" enctype="multipart/form-data">
				<div class="form-group">
					<label>Select favicon</label>
					<input type="file" class="form-control" name="uploadFile">
				</div>
				<button type="submit" class="btn btn-primary" name="favicon"><i class="fa fa-upload"></i> Upload</button>
			</form>
	    </div>
    </div>
    
    <div class="card">
        <div class="card-body">
			<?php 
			if(isset($_POST['white_logo'])) {
				$extensions = array('jpg','jpeg','png'); 
				$fileextension = end(explode('.',$_FILES['uploadFile']['name'])); 
				$fileextension = strtolower($fileextension); 
				if(empty($_FILES['uploadFile']['name'])) { echo error("Please select a file."); }
				elseif(!in_array($fileextension,$extensions)) { echo error("Allowed extensions: jpg and png."); }
				else {
					$filename = randomHash(5)."_".$_FILES['uploadFile']['name'];
					$sql_upload_path = 'assets/images/'.$filename;
					$upload_path = '../assets/images/'.$filename;
					@move_uploaded_file($_FILES['uploadFile']['tmp_name'],$upload_path);
					$update = $db->query("UPDATE settings SET white_logo='$sql_upload_path'");
					$settingsQuery = $db->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
                    $settings = $settingsQuery->fetch_assoc();
					echo success("Your White Logo was updated successfully.");
				}
			}
			?>
							
			<div class="alert alert-info">
				<b>Current White Logo:</b><br/><br/>
				<?php if($settings['white_logo']) { ?>
					<img src="<?= $settings['url'].$settings['white_logo'] ?>">
				<?php } else { ?>
					<img src="<?= $settings['url'] ?>assets/logo/logo-white.png">
				<?php } ?>
			</div>
			
			<form action="" method="POST" enctype="multipart/form-data">
				<div class="form-group">
					<label>Select White Logo</label>
					<input type="file" class="form-control" name="uploadFile">
				</div>
				<button type="submit" class="btn btn-primary" name="white_logo"><i class="fa fa-upload"></i> Upload</button>
			</form>
	    </div>
    </div>
</div>