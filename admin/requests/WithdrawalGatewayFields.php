<?php
// eWallet - PHP Script
// Author: DeluxeScript
define('PWV1_INSTALLED',TRUE);
ob_start();
session_start();
include("../../configs/bootstrap.php");
include("../../includes/bootstrap.php");
$gateway = protect($_GET['gateway']);
	?>
	<div class="row">
	<div class="col-md-12"><?php echo info("<b>Name of the field</b> is used to define the name of a field, is used to define the name of a field so the user can understand what information to provide."); ?></div>
	<div class="col-md-12 col-lg-12">
		<div class="form-group">
			<label>Name of the Field 1</label>
			<input type="text" class="form-control" name="field_1" <?php if($gateway !== "Bank Transfer") { echo 'value="Your '.$gateway.' account"'; } ?>>
		</div>
	</div>
	<div class="col-md-12 col-lg-12">
		<div class="form-group">
			<label>Name of the Field 2</label>
			<input type="text" class="form-control" name="field_2">
		</div>
	</div>
	<div class="col-md-12 col-lg-12">
		<div class="form-group">
			<label>Name of the Field 3</label>
			<input type="text" class="form-control" name="field_3">
		</div>
	</div>
	<div class="col-md-12 col-lg-12">
		<div class="form-group">
			<label>Name of the Field 4</label>
			<input type="text" class="form-control" name="field_4">
		</div>
	</div>
	<div class="col-md-12 col-lg-12">
		<div class="form-group">
			<label>Name of the Field 5</label>
			<input type="text" class="form-control" name="field_5">
		</div>
	</div>
	<div class="col-md-12 col-lg-12">
		<div class="form-group">
			<label>Name of the Field 6</label>
			<input type="text" class="form-control" name="field_6">
		</div>
	</div>
	<div class="col-md-12 col-lg-12">
		<div class="form-group">
			<label>Name of the Field 7</label>
			<input type="text" class="form-control" name="field_7">
		</div>
	</div>
	<div class="col-md-12 col-lg-12">
		<div class="form-group">
			<label>Name of the Field 8</label>
			<input type="text" class="form-control" name="field_8">
		</div>
	</div>
	<div class="col-md-12 col-lg-12">
		<div class="form-group">
			<label>Name of the Field 9</label>
			<input type="text" class="form-control" name="field_9">
		</div>
	</div>
	<div class="col-md-12 col-lg-12">
		<div class="form-group">
			<label>Name of the Field 10</label>
			<input type="text" class="form-control" name="field_10">
		</div>
	</div>
</div>