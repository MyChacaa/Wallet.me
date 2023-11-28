<?php
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

?>



<div class="col-md-12">
	<div class="card">
        <div class="card-body table-responsive">
            <?php
            if(isset($_POST['send_mail'])) {
            $subject = protect($_POST['subject_mail']);
            $message = protect($_POST['message_mail']);
            $type = protect($_POST['type_mail']);
            $email = protect($_POST['email']);
            $test = mass_mail($subject, $message, $type, $email);
            echo $test;
            }
            ?>
                            
            <form action="" method="POST">
            <div class="form-group">
		    <label>Send To :</label>
		    <select name="type_mail" id="type_mail" class="form-control" onchange="change_type(this.value);">
		    <option value="1">Single member</option>
		    <option value="2">All members</option>
		    </select>
		    </div>
		    
		    <div class="form-group" id="email_user">
		    <label>Email :</label>
		    <input type="text" placeholder="Email" class="form-control" name="email">
	         </div>
	         <div class="form-group">
		    <label>Subject :</label>
		    <input type="text" placeholder="Subject" class="form-control" name="subject_mail">
	         </div>
	         <div class="form-group">
		    <label>Message :</label>
		    <textarea class="form-control cleditor" name="message_mail" rows="7" placeholder="Message..."></textarea>
		     </div>
	         <button type="submit" class="btn btn-primary" name="send_mail"><i class="fa fa-check"></i> Send Mail</button>
	         </form>
			     
		     <script>
		     function change_type(a){
		     if(a == 2) {
		     document.getElementById("email_user").style.display = 'none';
		     document.getElementById("email_user").setAttribute("disabled", "true");
		     }else {
		     document.getElementById("email_user").style.display = 'block';
		     document.getElementById("email_user").removeAttribute("disabled");
		        
		     }
		     }
		     </script>
        </div>
    </div>
</div>
