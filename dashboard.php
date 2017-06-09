<?php
	require_once 'header.php';
	session_start();
	if (!isset($_SESSION['user'])) 					die ($dietext . __FILE__ . __LINE__);
	if ($_SESSION['user'] == "") 						die ($dietext . __FILE__ . __LINE__);

	$user				= $_SESSION['user'];
	if (!isset($user)) 			die ($dietext . __FILE__ . __LINE__);

	// fetch missing data from db and store in session
	//function assureDbFieldIntoSession 		($nameinsession,	$nameindb,	$table,					$searchitem,	$searchvalue)
	$personid 		= assureDbFieldIntoSession("personid", 			"id", 			"person", 			"username", 	"$user");
	$roleid 			= assureDbFieldIntoSession("roleid", 				"id", 			"role", 				"personid", 	$personid);
	$role					= assureDbFieldIntoSession("role", 					"role", 		"role", 				"personid", 	$personid);
	$associatid		= assureDbFieldIntoSession("associatid", 		"id", 			"association",	"roleid", 		$roleid);
	$schoolid			= assureDbFieldIntoSession("schoolid", 			"schoolid", "association",	"roleid", 		$roleid);
	$school 			= assureDbFieldIntoSession("school", 				"name", 		"school", 			"id", 				$schoolid);

	// school attendees
	$query 	= "SELECT username FROM person, role, association "
					.	"WHERE 	person.id=role.personid "
					.	"AND		role.id=association.id "
					.	"AND		role.role='Leerling' "
					.	"AND		association.schoolid=$schoolid "
					.	"ORDER BY username";
	$result = queryMySQL($query);
	if ($result->num_rows == 0) {
		$schoolattendees = "Uw school heeft geen leerlingen.";
	}	else {
		$schoolattendees = "";
		for ($i = 0 ; $i < $result->num_rows ; $i++) {
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$schoolattendees .= $row['username'] . "<br>";
		}
	}
	// owners courses
	$query 	= "SELECT course.id, name, start, weight, description "
					.	"FROM 	course, courseowner, role "
					.	"WHERE 	course.id=courseowner.courseid "
					.	"AND		role.id=courseowner.roleid "
					.	"AND		role.personid=$personid "
					.	"ORDER BY name";
	$result = queryMySQL($query);
	if ($result->num_rows == 0) {
		$courses = "U heeft (nog) geen lespakketten.";
	}	else {
		$courses = "";
		for ($i = 0 ; $i < $result->num_rows ; $i++) {
			$row 	= $result->fetch_array(MYSQLI_ASSOC);
			$id 	= $row['id'];
			$nm 	= $row['name'];
			$st		= $row['start'];
			$wt		= $row['weight'];
			$dc		= $row['description'];
			$courses .= <<<_END
				<canvas id='divider' width='10' height='15'>Extra space</canvas>
				<div width='auto'>
					<div>
						<a href="#">
							<span class="glyphicon glyphicon-chevron-down" data-toggle="collapse" data-target="#course$i"></span>
						</a>
						<strong>$nm</strong>
						<a href="course.php?mode=existing&courseid=$id">$nm</a>
					</div>
					<div id="course$i" class="collapse">
						<div><strong>Beschikbaar vanaf:</strong> $st</div>
						<div><strong>Studiepunten:</strong> $wt</div>
						<div>$dc</div>
					</div>
				</div>
_END;
		}
	}

//<button type="button" class="btn btn-default" aria-label="Left Align">
  
//<a href="course.php" class="btn btn-default" role="button">Annuleer</a>

	
?>

		<div class="container">
			<div class="row">
				<?php include 'snippet_menu.html'; ?> <!-- takes 2 cols -->
				<div class="col-sm-10">
						<div class="well">
							<div><?php echo "Dashboard van $user, $role, $school."; ?></div>
						</div>
						<!-- all content initially hidden -->
						<div class="well" id="Docent" hidden>
							<div id="mycourseowner"><?php echo "<strong>Mijn lespakketten.</strong><br>"
														. $courses; ?></div>
							<?php include 'snippet_vertical_space_15px.html'; ?>
							<div><a href="course.php?mode=new" class="btn btn-primary" role="button">Nieuw lespakket...</a></div>
							<?php include 'snippet_vertical_space_30px.html'; ?>
							<div><?php echo "<strong>Leerlingen van mijn school.</strong><br>"
														. $schoolattendees; ?></div>
						</div>
						<div class="well" id="Staflid" hidden>
							<div><?php echo "<strong>Hier komt wat.</strong><br>"
														. "En nog wat meer. Dit moet uiteindelijk alleen voor stafleden zichtbaar zijn."; ?></div>
						</div>
						<div class="well" id="Mentor" hidden>
							<div><?php echo "<strong>Hier komt wat.</strong><br>"
														. "En nog wat meer. Dit moet uiteindelijk alleen voor mentoren zichtbaar zijn."; ?></div>
						</div>
						<div class="well" id="Leerling" hidden>
							<div><?php echo "<strong>Hier komt wat.</strong><br>"
														. "En nog wat meer. Dit moet uiteindelijk alleen voor leerlingen zichtbaar zijn."; ?></div>
						</div>
						<div class="well" id="Ouder" hidden>
							<div><?php echo "<strong>Hier komt wat.</strong><br>"
														. "En nog wat meer. Dit moet uiteindelijk alleen voor ouders zichtbaar zijn."; ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- show relevant content based on role -->
		<div id="currole" hidden><?php echo $role; ?></div> <!-- hidden indication of role -->
		<script>
			var mystr = "currole";
			var targetname	= O(mystr);
			mystr = targetname.innerHTML; <!-- get the role -->
			O(mystr).removeAttribute("hidden") <!-- make visible -->
		</script>
		
		<?php include 'snippet_vertical_space_30px.html'; ?>
		<?php include 'snippet_footer.html'; ?>

		</body>
</html>

		<?php //print_r ($_SESSION); ?>
		<?php //include 'snippet_vertical_space_30px.html'; ?>