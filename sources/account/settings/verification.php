<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

if(!checkSession()) {
    $redirect = $settings['url']."login";
    header("Location: $redirect");
}   

if($settings['require_document_verify'] !== "1") {
    $redirect = $settings['url']."account/summary";
    header("Location: $redirect");
}
?>
<h3><?php echo $lang['head_verification']; ?></h3>
<hr/>
<?php 
if(idinfo($_SESSION['pw_uid'],"document_verified") == "1") {
    echo success($lang['success_11']);
} else {
    echo error($lang['error_22']);
}
?>
<?php if(idinfo($_SESSION['pw_uid'],"document_verified") !== "1") { ?>
<p><?php echo $lang['head_verification_info']; ?></p>
<br>
<div class="card">
    <div class="card-body">
        <h4><?php echo $lang['upload_document']; ?></h4>
        <?php
        $FormBTN = protect($_POST['pw_upload']);
        if($FormBTN == "upload") {
            $document_type = protect($_POST['document_type']);
            $document_number = protect($_POST['document_number']);
            $extensions = array('jpg','jpeg','png'); 
            $fileextension = end(explode('.',$_FILES['uploadFile']['name'])); 
            $fileextension = strtolower($fileextension); 
            $maxfilesize = '5242880'; // 5MB
            
            $vrfdocs = $db->query("SELECT * FROM users_documents WHERE uid='$_SESSION[pw_uid]' and document_type='$document_type'");
            $vrfdocs = $vrfdocs->fetch_assoc();
            
            if(empty($document_type)) {
                echo error($lang['error_23']);
            }else if($vrfdocs['status'] == 1){
                echo error("Your document is pending. wait to approved by compliance.");   
            }else if($vrfdocs['status'] == 3){
                echo error("Your document has been approved by compliance.");
            } elseif(empty($document_number)) {
                echo error($lang['error_24']);
            } elseif(empty($_FILES['uploadFile']['name'])) {
                echo error($lang['error_25']);
            } elseif(!in_array($fileextension,$extensions)) { 
                echo error($lang['error_26']); 
            } elseif($_FILES['uploadFile']['size'] > $maxfilesize)  {
                echo error($lang['error_27']);
            } else {
                $secure_directory = PW_secure_directory();
                if(!is_dir("./".$secure_directory)) {
                    mkdir("./".$secure_directory,0777);
                    $file_htaccess = '';
                    file_put_contents("./".$secure_directory."/.htaccess",$file_htaccess);
                }
                $upload_file = $secure_directory.'/'.$_SESSION[pw_uid];
                if(!is_dir($upload_file)) {
                    mkdir("./".$upload_file,0777);
                }
                $upload_file = $upload_file.'/'.randomHash(20).'.'.$fileextension;
                @move_uploaded_file($_FILES['uploadFile']['tmp_name'],$upload_file);
                $time = time();
                $insert = $db->query("INSERT users_documents (uid,document_type,document_path,uploaded,status,u_field_1) VALUES ('$_SESSION[pw_uid]','$document_type','$upload_file','$time','1','$document_number')");
                echo success($lang['success_12']);
            }
        }
        ?>
        
        <form class="user-connected-from user-signup-form" action="" method="POST" enctype="multipart/form-data">
            <div class="row form-group">
                <div class="col">
                    <label><?php echo $lang['field_18']; ?></label>
                    <select class="form-control form-control-lg" name="document_type">
                        <option></option>
                        <option value="1"><?php echo $lang['doc_type_1']; ?></option>
                        <option value="2"><?php echo $lang['doc_type_2']; ?></option>
                        <option value="3"><?php echo $lang['doc_type_3']; ?></option>
                        <option value="4"><?php echo $lang['doc_type_4']; ?></option>
                    </select>
                </div>
                <div class="col">
                    <label><?php echo $lang['field_19']; ?></label>
                    <input type="text" class="form-control" name="document_number">
                </div>
            </div>
            <div class="form-group">
                <label><?php echo $lang['field_20']; ?></label>
                <input type="file" class="form-control" name="uploadFile">
                <small><?php echo $lang['field_20_info']; ?></small>
            </div>
            <button type="submit" name="pw_upload" value="upload" class="btn btn-primary" style="padding:12px;"><?php echo $lang['btn_19']; ?></button>
        </form>
        
    </div>
</div>
<?php } ?>
<br>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <td width="25%"><?php echo $lang['date']; ?></td>
                <td width="25%"><?php echo $lang['document_number']; ?></td>
                <td width="25%"><?php echo $lang['status']; ?></td>
                <td width="25%"><?php echo $lang['comment']; ?></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $GetDocuments = $db->query("SELECT * FROM users_documents WHERE uid='$_SESSION[pw_uid]' ORDER BY id");
            if($GetDocuments->num_rows>0) {
                while($get = $GetDocuments->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo date("d M Y H:i",$get['uploaded']); ?></td>
                        <td><?php echo $get['u_field_1']; ?><br/>(<?php if($get['document_type'] == "1") { echo $lang['doc_type_1']; } elseif($get['document_type'] == "2") { echo $lang['doc_type_2']; } elseif($get['document_type'] == "3") { echo $lang['doc_type_3']; } elseif($get['document_type'] == "4") { echo $lang['doc_type_4']; } else { echo 'Unknown'; } ?>)</td>
                        <td>
                            <?php
                            if($get['status'] == "1") { echo '<span class="badge badge-warning">'.$lang[status_doc_1].'</span>'; }
                            elseif($get['status'] == "2") { echo '<span class="badge badge-danger">'.$lang[status_doc_2].'</span>'; } 
                            elseif($get['status'] == "3") { echo '<span class="badge badge-success">'.$lang[status_doc_3].'</span>'; }
                            else {
                                echo '<span class="badge badge-default">'.$lang[status_unknown].'</span>';
                            }
                            ?>
                        </td>
                        <td><?php if($get['u_field_5']) { echo $get['u_field_5']; } ?></td>
                    </tr>
                    <?php
                }
            } else {
                echo '<tr><td colspan="4">'.$lang[info_5].'</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>