<?php 	// index.php
	require_once 'functions.php'; 
	
	$error	= $user	= "";
	
	if (isset($_GET['user'])) {
		$user	= sanitizeString($_GET['user']);
		
		if ($user == "") {
			$error	= "<span class='error'>$foutNoInput</span>";
		} else {
			$result	= queryMySQL(	"SELECT name FROM user "
													.	"WHERE name='$user'");
			if ($result->num_rows == 0) {
				$target = "register.php";
			} else {
				$target = "dashboard.php";
			}
			session_start();
			$_SESSION = array();	// discard previous session
			$_SESSION['user']			= $user; 
			header("Location: $target");
		}
	} else {
		session_start();
		$_SESSION = array();		// discard previous session
		$_SESSION['user']			= NULL;
	}

	require_once 'header.php';
?>
		<div class="container">
			<div class="row">
				<div class="col-sm-2"></div>
				<div class="col-sm-10">
						<div class="well">
							<div><?php echo "Welkom bij ". APPNAME; ?></div>
							<div><?php echo "<div title='$helptext01'>$introtext</div>"; ?>
							</div>
						</div>
		<?php include 'snippet_vertical_space_30px.html'; ?>
						<canvas id='divider' width='10' height='30'>Extra space</canvas>
						<h4 display='inline'>Log hier in</h4> 
						<?php if ($error != "") include 'snippet_error_message.php'; ?>
						<form action="index.php" method = "get">
							<div class="form-group" title='<?php echo "$helptext02"; ?>' >
								<div class="input-group">
									<span class="input-group-addon">Gebruiker</span>
									<input id="msg" type="text" class="form-control" placeholder="Tijdens DEMO graag een voornaam" name="user" value="<?php echo $user?>" >
								</div>
							</div>
							<button type="submit" class="btn btn-primary">Registreer/Login</button>
						</form>
				</div>
			</div>
		</div>

		<?php include 'snippet_vertical_space_30px.html'; ?>
		<?php include 'snippet_footer.html'; ?>
			
	</body>
</html>