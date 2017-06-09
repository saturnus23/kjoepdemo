<?php 	// course.php
	require_once 'functions.php'; 
	
	$user	= $roleid = $crsename = $crsestart = $crseweight = $crsedescription = "";
	$crsenameerror = $crsestarterror = $crseweighterror = $crsedescriptionerror = "";

	session_start();

	// mode is new or existing
	if 			(isset($_GET['mode']))			$mode	= $_SESSION['mode'] = $_GET['mode'];
	elseif	(isset($_SESSION['mode']))	$mode	= $_SESSION['mode'];
	else																die ($dietext . __FILE__ . __LINE__);
	
	switch ($mode) {
		case 'new':	
			$courseid = $_SESSION['courseid'] = "";
			break;
		case 'existing':
			if 			(isset($_GET['courseid']))			$courseid = $_SESSION['courseid'] = $_GET['courseid'];
			elseif	(isset($_SESSION['courseid']))	$courseid = $_SESSION['courseid'];
			else														die ($dietext . __FILE__ . __LINE__);
			break;
		default:
			die ($dietext . __FILE__ . __LINE__);
	}
	
	if (!isset($_SESSION['user']))			die ($dietext . __FILE__ . __LINE__);
	if (!isset($_SESSION['roleid'])) 		die ($dietext . __FILE__ . __LINE__);

	$user 	= $_SESSION['user'];
	$roleid = $_SESSION['roleid'];

	$go = 0;
	if (isset($_GET['crsename']))	{
		$crsename 				= $_GET['crsename']; 
		// validate crsename
		if ($crsename 	== $askcrsename
		||	$crsename 	== "")
			$crsenameerror = "<span class='error'>$foutNoInput</span>";
		$crsename = sanitizeString($crsename);
		if (strlen($crsename) > 200)
			$crsenameerror = "<span class='error'>$foutMax200</span>";

		if ($mode == 'new') {
			$query 	= "SELECT id FROM course WHERE name='$crsename'";
		} else { // mode == 'existing'
			$query 	= "SELECT id FROM course WHERE name='$crsename' AND id!='$courseid'";
		}
		$result = queryMySQL($query);
		if ($result->num_rows > 0) 		$crsenameerror = "<span class='error'>$alreadyexists</span>";
		
		if ($crsenameerror == "") $go++;
	} 
	elseif ($mode == 'new')				$crsename = $askcrsename;
	else 	/*$mode == 'existing'*/	$crsename = fetchOneFieldFromDbTable("name", "course", "id", "$courseid");

	if (isset($_GET['crsestart']))	{
		$crsestart 				= $_GET['crsestart']; 
		// validate crsestart
		if ($crsestart 	== $askcrsestart
		||	$crsestart 	== "")				$crsestarterror = "<span class='error'>$foutNoInput</span>";
		$crsestart = sanitizeString($crsestart);
		if (strlen($crsestart) > 20)
			$crsestarterror = "<span class='error'>$foutMax20</span>";
		if ($crsestarterror == "") $go++;
	}
	elseif ($mode == 'new')				$crsestart = $askcrsestart;
	else 	/*$mode == 'existing'*/	$crsestart = fetchOneFieldFromDbTable("start", "course", "id", "$courseid");

	if (isset($_GET['crseweight']))	{
		$crseweight 			= $_GET['crseweight']; 
		// validate crseweight
		if ($crseweight 	== $askcrseweight
		||	$crseweight 	== "")			$crseweighterror = "<span class='error'>$foutNoInput</span>";
		$crseweight = sanitizeString($crseweight);
		if (!is_numeric($crseweight))	$crseweighterror = "<span class='error'>$foutNotNumeric</span>";
		// moet ook nog een integer zijn
		// technisch tussen -16384 en +16383
		// functioneel tussen 1 en 100 oid
		// kom later wel
		if ($crseweighterror == "") $go++;
	} 
	elseif ($mode == 'new')				$crseweight = $askcrseweight;
	else 	/*$mode == 'existing'*/	$crseweight = fetchOneFieldFromDbTable("weight", "course", "id", "$courseid");

	if (isset($_GET['crsedescription']))	{
		$crsedescription 	= $_GET['crsedescription']; 
		// validate crsedescription
		if ($crsedescription 	== $askcrsedescription
		||	$crsedescription 	== "")	$crsedescriptionerror = "<span class='error'>$foutNoInput</span>";
		$crsedescription = sanitizeString($crsedescription);
		if (strlen($crsedescription) > 3000)
			$crsestarterror = "<span class='error'>$foutMax3000</span>";
		if ($crsedescriptionerror == "") $go++;
	} 
	elseif ($mode == 'new')				$crsedescription = $askcrsedescription;
	else 	/*$mode == 'existing'*/	$crsedescription = fetchOneFieldFromDbTable("description", "course", "id", "$courseid");
	
	// DATABASE
	if ($go == 4) {		// based on FOUR inputfields
		// course
		if 		($mode == 'new') 				$query 	= "INSERT INTO course (id, name, start, weight, description) VALUES (null, '$crsename', '$crsestart', '$crseweight', '$crsedescription')";
		else /*$mode == 'existing'*/	$query 	= "UPDATE course SET name='$crsename', start='$crsestart', weight='$crseweight', description='$crsedescription' WHERE id='$courseid'";
		$result = queryMySQL($query);
		if (!$result) die ($dietext . __FILE__ . __LINE__);
		if 		($mode == 'new') {
			// get courseid
			$query 	= "SELECT id FROM course WHERE name='$crsename'";
			$result = queryMySQL($query);
			if ($result->num_rows == 1) {
				$row			= $result->fetch_array(MYSQLI_ASSOC);
				$courseid = $row['id'];
			} 
			else 				die ($dietext . __FILE__ . __LINE__);
			// courseowner
			$query 	= "INSERT INTO courseowner (id, courseid, roleid) VALUES (null, $courseid, '$roleid')";
			$result = queryMySQL($query);
			if (!$result) die ($dietext . __FILE__ . __LINE__);
		}
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
						<div><?php echo "<strong>Lespakket</strong> maken voor " . $user . "."; ?></div>
						<div><?php echo "<div title='$helptext01'>$coursetext</div>"; ?>
						</div>
					</div>
					<?php
						include 'snippet_vertical_space_30px.html'; 
						// eerste keer hoeft geen fout te worden gemeld
					?>
					<form action="course.php" method = "get">
						<div class="form-group">
							<?php if ($crsenameerror != "") errormessage($crsenameerror)?>
							<div class="input-group">
								<span class="input-group-addon">Naam:</span>
								<input id="msg" type="text" class="form-control" name="crsename" value="<?php echo $crsename?>" >
							</div>
							<?php include 'snippet_vertical_space_30px.html'; ?>
							<?php if ($crsestarterror != "") errormessage($crsestarterror)?>
							<div class="input-group">
								<span class="input-group-addon">Start:</span>
								<input id="msg" type="text" class="form-control" name="crsestart" value="<?php echo $crsestart?>" >
							</div>
							<?php include 'snippet_vertical_space_30px.html'; ?>
							<?php if ($crseweighterror != "") errormessage($crseweighterror)?>
							<div class="input-group">
								<span class="input-group-addon">Waarde:</span>
								<input id="msg" type="text" class="form-control" name="crseweight" value="<?php echo $crseweight?>" >
							</div>
							<?php include 'snippet_vertical_space_30px.html'; ?>
							<?php if ($crsedescriptionerror != "") errormessage($crsedescriptionerror)?>
							<label for="crsedesc">Beschrijving:</label>
							<textarea class="form-control" rows="5" id="crsedesc" name="crsedescription"><?php echo $crsedescription?></textarea>
						<?php include 'snippet_vertical_space_30px.html'; ?>
						</div>
						<!-- javascript will remove publishbtn or (editbtn and deletebtn) -->
						<button type="submit" class="btn btn-primary" id="publishbtn">Publiceer lespakket</button>
						<a href="dashboard.php" class="btn btn-default" role="button">Annuleer</a>
						<button type="submit" class="btn btn-primary" id="editbtn">Wijzig lespakket</button>
						<a href="<?php echo "deletecourse.php?courseid=$courseid" ?>" class="btn btn-danger" role="button" id="deletebtn">Verwijder lespakket</a>
					</form>
				</div>
			</div>
		</div>
		<!-- show relevant button(s)  -->
		<div id="existingcourse" hidden><?php echo ($mode == 'existing') ? "true" : "false" ?></div>
		<script>
			var mystr = "existingcourse";
			var myswitch = O(mystr);
			if (myswitch.innerHTML == "true") {
				var tmp = O("publishbtn");
				tmp.parentNode.removeChild(tmp);
			} else {
				var tmp = O("editbtn");
				tmp.parentNode.removeChild(tmp);
				tmp = O("deletebtn");
				tmp.parentNode.removeChild(tmp);
			}
		</script>

		<?php include 'snippet_vertical_space_30px.html'; ?>
		<?php include 'snippet_footer.html'; ?>
			
	</body>
</html>