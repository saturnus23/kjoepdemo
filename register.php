<?php 	// register.php
	require_once 'functions.php'; 
	
	$error	= $user	= $school = $role = "";

	session_start();
	if (!isset($_SESSION['user']))	die ($dietext . __FILE__ . __LINE__);
	$user = $_SESSION['user'];
	
	if (isset($_GET['role'])) 	$role 	= $_SESSION['role'] 	= $_GET['role'];
	if (isset($_GET['school'])) $school	= $_SESSION['school']	= $_GET['school'];
	
	if ($role 	== $askSelect
	||	$school == $askSelect) {
		$error = "<span class='error'>$foutNoInputs</span>";
	} elseif ($role 	!= ""
				&&	$school	!= "") {
		// DATABASE
		// user
		$query 	= "INSERT INTO user (name, status) VALUES ('$user', 'active')";
		$result = queryMySQL($query);
		if (!$result) die ($dietext . __FILE__ . __LINE__);
		// person
		$query 	= "INSERT INTO person (id, username) VALUES (null, '$user')";
		$result = queryMySQL($query);
		if (!$result) die ($dietext . __FILE__ . __LINE__);
		// get personid
		$query 	= "SELECT id FROM person WHERE username='$user'";
		$result = queryMySQL($query);
		if ($result->num_rows == 1) {
			$row			= $result->fetch_array(MYSQLI_ASSOC);
			$personid	= $row['id'];
		} else {
			die ($dietext . __FILE__ . __LINE__);
		}
		// role
		$query 	= "INSERT INTO role (id, personid, role) "
						.	"VALUES (null, $personid, '$role')";
		$result = queryMySQL($query);
		if (!$result) die ($dietext . __FILE__ . __LINE__);
		// get roleid
		$query 	= "SELECT id FROM role WHERE personid=$personid";
		$result = queryMySQL($query);
		if ($result->num_rows == 1) {
			$row			= $result->fetch_array(MYSQLI_ASSOC);
			$roleid		= $row['id'];
		} else {
			die ($dietext . __FILE__ . __LINE__);
		}
		// get schoolid
		$query 	= "SELECT id FROM school WHERE name='$school'";
		$result = queryMySQL($query);
		if ($result->num_rows == 1) {
			$row			= $result->fetch_array(MYSQLI_ASSOC);
			$schoolid	= $row['id'];
		} else {
			die ($dietext . __FILE__ . __LINE__);
		}
		// association
		$query 	= "INSERT INTO association (id, roleid, schoolid) "
						. "VALUES (null, $roleid, $schoolid)";
		$result = queryMySQL($query);
		if (!$result) die ($dietext . __FILE__ . __LINE__);
		// verder naar dashboard
		header("Location: dashboard.php");
	}
	require_once 'header.php';
?>
		<div class="container">
			<div class="row">
				<div class="col-sm-2"></div>
				<div class="col-sm-10">
						<div class="well">
							<div><?php echo "Welkom bij ". APPNAME . ", " . $user; ?></div>
							<div><?php echo "<div title='$helptext01'>$registertext</div>"; ?>
							</div>
						</div>
						<?php
							include 'snippet_vertical_space_30px.html'; 
							// eerste keer hoeft geen fout te worden gemeld
							if ($error != "") include 'snippet_error_message.php'; 
						?>
						<form action="register.php" method = "get">
							<div class="form-group">
								<label for="selectschool">Wat is uw school:</label>
								<select class="form-control" id="sel1" name='school'>
									<?php
										if ($school == "" 
										||  $school == $askSelect) {
											echo "<option selected>$askSelect</option>";
										}
										$query	= "SELECT name FROM school ORDER BY name";
										$result = queryMySQL($query);
										for ( $i = 0 ; $i < $result->num_rows ; $i++ ) {
											$row = $result->fetch_array(MYSQLI_ASSOC); // TODO try catch
											$val = $row['name'];
											$sel = ($val == $school) ? " selected" : "";
											echo "<option$sel>$val</option>";
										}
									?>
								</select>
							</div>
							<div class="form-group">
								<label for="selectrole">Wat is uw rol:</label>
								<select class="form-control" id="sel2" name='role'>
									<?php
										if ($role == "" 
										||  $role == $askSelect) {
											echo "<option selected>$askSelect</option>";
										}
										$query	= "SELECT name FROM roletype ORDER BY name";
										$result = queryMySQL($query);
										for ( $i = 0 ; $i < $result->num_rows ; $i++ ) {
											$row = $result->fetch_array(MYSQLI_ASSOC); // TODO try catch
											$val = $row['name'];
											$sel = ($val == $role) ? " selected" : "";
											echo "<option$sel>$val</option>";
										}
									?>
								</select>
							</div>
							<button type="submit" class="btn btn-primary">Registreer</button>
						</form>
				</div>
			</div>
		</div>

		<?php include 'snippet_vertical_space_30px.html'; ?>
		<?php include 'snippet_footer.html'; ?>
			
	</body>
</html>