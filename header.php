<?php // header.php
	require_once 'functions.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<title id='mytitle'><?php echo APPNAME; ?></title>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>

		<!--<link rel='stylesheet' href='styles.css'>-->
		<script src='javascript.js'></script>
		
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-sm-2"><img src='qube.png'></div>
				<div class="col-sm-10">
					<div class="page-header">
						<h1><?php echo APPNAME; ?></h1>      
					</div>
				</div>
			</div>
		</div>
