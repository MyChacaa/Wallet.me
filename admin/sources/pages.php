<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
?>
<?php

if (isset($_GET['b'])){
	$b = protect($_GET['b']);
} else {
	$b = "";
}

if($b == "add") {
?>
	    
<!-- include libraries(jQuery, bootstrap) -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<div class="col-md-12">
	<div class="card">
        <div class="card-body">
			<?php
			if(isset($_POST['btn_add'])) {
				$title = protect($_POST['title']);
				$prefix = protect($_POST['prefix']);
				$content = addslashes($_POST['content']);
				$check = $db->query("SELECT * FROM pages WHERE prefix='$prefix'");
				if(empty($title) or empty($prefix) or empty($content)) { echo error("All fields are required."); }
				elseif(!isValidUsername($prefix)) { echo error("Please enter valid prefix."); }
				elseif($check->num_rows>0) { echo error("This prefix is already used. Please choose another. "); }
				else {
					$page = $settings['url']."page/".$prefix;
					$link = '<a href="'.$page.'" target="_blank">'.$page.'</a>';
					$time = time();
					$insert = $db->query("INSERT pages (title,prefix,content,created) VALUES ('$title','$prefix','$content','$time')");
					echo success("Page was created successfully. Preview link: $link");
				}	
			}
			?>
			
			<form action="" method="POST">
				<div class="form-group">
					<label>Title</label>
					<input type="text" class="form-control" name="title">
				</div>
				<div class="form-group">
					<label>Prefix</label>
					<div class="input-group">
					  <span class="input-group-addon"><?php echo $settings['url']; ?>page/</span>
					  <input type="text" class="form-control" name="prefix">
					</div>
					<small>Use latin characters and symbols - and _. Do not make spaces between words.</small>
				</div>
				<div class="form-group">
					<label>Content</label>
					<textarea id="summernote" rows="15" name="content"></textarea>
				</div>
				<button type="submit" class="btn btn-primary" name="btn_add"><i class="fa fa-plus"></i> Add</button>
			</form>		
		</div>
	</div>
</div>
<script>
    $(document).ready(function() {
  $('#summernote').summernote();
});
</script>

	<?php
} elseif($b == "edit") {
	$id = protect($_GET['id']);
	$query = $db->query("SELECT * FROM pages WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=pages"); }
	$row = $query->fetch_assoc();
	?>
	
<!-- include libraries(jQuery, bootstrap) -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<div class="col-md-12">
	<div class="card">
        <div class="card-body">
			<?php
			if(isset($_POST['btn_save'])) {
				$title = protect($_POST['title']);
				$prefix = protect($_POST['prefix']);
				$content = addslashes($_POST['content']);
				$check = $db->query("SELECT * FROM pages WHERE prefix='$prefix'");
				if(empty($title) or empty($prefix) or empty($content)) { echo error("All fields are required."); }
				elseif(!isValidUsername($prefix)) { echo error("Please enter valid prefix."); }
				elseif($row['prefix'] !== $prefix && $check->num_rows>0) { echo error("This prefix is already used. Please choose another. "); }
				else {
					$page = $settings['url']."page/".$prefix;
					$link = '<a href="'.$page.'" target="_blank">'.$page.'</a>';
					$time = time();
					$update = $db->query("UPDATE pages SET title='$title',prefix='$prefix',content='$content',updated='$time' WHERE id='$row[id]'");
					$query = $db->query("SELECT * FROM pages WHERE id='$id'");
					$row = $query->fetch_assoc();
					echo success("Page was updated successfully. Preview link: $link");
				}	
			}
			?>
			
			<form action="" method="POST">
				<div class="form-group">
					<label>Title</label>
					<input type="text" class="form-control" name="title" value="<?php echo $row['title']; ?>">
				</div>
				<div class="form-group">
					<label>Prefix</label>
					<div class="input-group">
					  <span class="input-group-addon"><?php echo $settings['url']; ?>page/</span>
					  <input type="text" class="form-control" name="prefix" value="<?php echo $row['prefix']; ?>">
					</div>
					<small>Use latin characters and symbols - and _. Do not make spaces between words.</small>
				</div>
				<div class="form-group">
					<label>Content</label>
					<textarea id="summernote" rows="15" name="content"><?php echo $row['content']; ?></textarea>
				</div>
				<button type="submit" class="btn btn-primary" name="btn_save"><i class="fa fa-check"></i> Save changes</button>
			</form>
		</div>
	</div>
</div>
<script>
    $(document).ready(function() {
  $('#summernote').summernote();
});
</script>
	<?php
} elseif($b == "delete") {
	$id = protect($_GET['id']);
	$query = $db->query("SELECT * FROM pages WHERE id='$id'");
	if($query->num_rows==0) { header("Location: ./?a=pages"); }
	$row = $query->fetch_assoc();
	?>
	

<div class="col-md-12">
	<div class="card">
        <div class="card-body">
			<?php
			if(isset($_GET['confirm'])) {
				$delete = $db->query("DELETE FROM pages WHERE id='$row[id]'");
				echo success("Page <b>$row[title]</b> was deleted.");
			} else {
				echo info("Are you sure you want to delete page <b>$row[title]</b>?");
				echo '<a href="./?a=pages&b=delete&id='.$row['id'].'&confirm=1" class="btn btn-success"><i class="fa fa-check"></i> Yes</a>&nbsp;&nbsp;
					<a href="./?a=pages" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
			}
			?>
		</div>
	</div>
</div>
	<?php
} else {
?>
<div class="row">
    <div class="col-md-12">
	    <center><a class="btn btn-primary btn-block" style="width:97%;" href="./?a=pages&b=add"><i class="fa fa-plus"></i> Create New Page</a></center>
	</div>
</div>
<div class="content mt-3">

<div class="col-md-12">
	<div class="card">
        <div class="card-body">
		    <table class="table table-striped">
    			<thead>
    				<tr>
    					<th width="30%">Title</th>
    					<th width="20%">Prefix</th>
    					<th width="20%">Created on</th>
    					<th width="20%">Updated on</th>
    					<th width="10%">Action</th>
    				</tr>
    			</thead>
    			<tbody>
    				<?php
    				$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    				$limit = 20;
    				$startpoint = ($page * $limit) - $limit;
    				if($page == 1) {
    					$i = 1;
    				} else {
    					$i = $page * $limit;
    				}
    				$statement = "pages";
    				$query = $db->query("SELECT * FROM {$statement} ORDER BY id LIMIT {$startpoint} , {$limit}");
    				if($query->num_rows>0) {
    					while($row = $query->fetch_assoc()) {
    						?>
    						<tr>
    							<td><?php echo $row['title']; ?></td>
    							<td><?php echo $row['prefix']; ?></td>
    							<td><?php if($row['created']) { echo '<span class="label label-default">'.date("d/m/Y H:i:s".$row[created]).'</span>'; } else { echo '-'; } ?></td>
    							<td><?php if($row['updated']) { echo '<span class="label label-default">'.date("d/m/Y H:i:s".$row[updated]).'</span>'; } else { echo '-'; } ?></td>
    							<td>
    								<a href="./?a=pages&b=edit&id=<?php echo $row['id']; ?>" title="Edit"><span class="badge badge-primary"><i class="fa fa-pencil"></i> Edit</span></a> 
    								<a href="./?a=pages&b=delete&id=<?php echo $row['id']; ?>" title="Delete"><span class="badge badge-danger"><i class="fa fa-trash"></i> Delete</span></a>
    							</td>
    						</tr>
    						<?php
    					}
    				} else {
    					echo '<tr><td colspan="5">No have pages yet. <a href="./?a=pages&b=add">Click here</a> to add.</td></tr>';
    				}
    				?>
    			</tbody>
		    </table>
    		<?php
    		$ver = "./?a=pages";
    		if(admin_pagination($statement,$ver,$limit,$page)) {
    			echo admin_pagination($statement,$ver,$limit,$page);
    		}
    		?>
	    </div>
	</div>
</div>
<?php
}
?>