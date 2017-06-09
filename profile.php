<?php
	require_once 'header.php';
	session_start();
	if (!isset($_SESSION['user']))	die ($dietext . __FILE__ . __LINE__);
	if ($_SESSION['user'] == "") 		die ($dietext . __FILE__ . __LINE__);
	if ($_SESSION['user'] == "") 		die ($dietext . __FILE__ . __LINE__);

	$user				= $_SESSION['user'];
	$role				= $_SESSION['role'];
	
	// fetch missing data from db and store in session
	//function assureDbFieldIntoSession 		($nameinsession,	$nameindb,	$table,					$searchitem,	$searchvalue)
	$personid 		= assureDbFieldIntoSession("personid", 			"id", 			"person", 			"username", 	"$user");
	$roleid 			= assureDbFieldIntoSession("roleid", 				"id", 			"role", 				"personid", 	$personid);
	$role					= assureDbFieldIntoSession("role", 					"role", 		"role", 				"personid", 	$personid);
	$associatid		= assureDbFieldIntoSession("associatid", 		"id", 			"association",	"roleid", 		$roleid);
	$schoolid			= assureDbFieldIntoSession("schoolid", 			"schoolid", "association",	"roleid", 		$roleid);
	$school 			= assureDbFieldIntoSession("school", 				"name", 		"school", 			"id", 				$schoolid);

	if (isset($_SESSION['targetuser'])) {
		$targetuser	= $_SESSION['targetuser'];
		die ($dietext . __FILE__ . __LINE__); // todo: parameters ophalen uit db
	} else {
		$targetuser			= $user;
		$targetrole			= $role;
		$targetschool		= $school;
		$targetpersonid	= $personid;
	}	

	// save edited profileitems in database
	if (isset($_GET['teachersubject'])) 	storeProfileItem ($personid, 'teachersubject',	$_GET['teachersubject']);
	if (isset($_GET['pupillevel'])) 			storeProfileItem ($personid, 'pupillevel', 			$_GET['pupillevel']);
	if (isset($_GET['pupilfavsubject'])) 	storeProfileItem ($personid, 'pupilfavsubject',	$_GET['pupilfavsubject']);
	if (isset($_GET['mentorhairstyle']))	storeProfileItem ($personid, 'mentorhairstyle', $_GET['mentorhairstyle']);
	if (isset($_GET['parentnumkids'])) 		storeProfileItem ($personid, 'parentnumkids', 	$_GET['parentnumkids']);
	if (isset($_GET['stafffavcolor'])) 		storeProfileItem ($personid, 'stafffavcolor', 	$_GET['stafffavcolor']);
	if (isset($_GET['allmotto'])) 				storeProfileItem ($personid, 'allmotto', 				$_GET['allmotto']);
	if (isset($_GET['allfavsport'])) 			storeProfileItem ($personid, 'allfavsport', 		$_GET['allfavsport']);

	// get full profile from database (only relevant fields depending on role) 
	switch ($role) {
		case "Docent"		: $fields = "teachersubject";							break;
		case "Leerling"	: $fields = "pupillevel,pupilfavsubject";	break;  // no space after comma!!!!!
		case "Mentor"		: $fields = "mentorhairstyle";						break;
		case "Ouder"		: $fields = "parentnumkids";							break;
		case "Staflid"	: $fields = "stafffavcolor";							break;
		default					:	die ($dietext . __FILE__ . __LINE__);
	}
	$fields  .= ",allmotto,allfavsport"; // add plus generic profile fields	// no space after comma!!!!!

	$query		= "SELECT $fields FROM profile where personid='$targetpersonid'";
	$result		= queryMySQL($query);
	$profile	= array();
	switch ($result->num_rows) {
		case 0	: break;
		case 1	:	$row	= $result->fetch_array(MYSQLI_ASSOC);
							$cnvfields = explode(',', $fields);
							foreach ($cnvfields as $field) {
								$profile[$field] = $row[$field];
							}
							break;
		default	: die ($dietext . __FILE__ . __LINE__);
	}
	// check crucial fields present
	if (!isset($user)) 				die ($dietext . __FILE__ . __LINE__);
	if (!isset($role)) 				die ($dietext . __FILE__ . __LINE__);
	if (!isset($targetuser))	die ($dietext . __FILE__ . __LINE__);
	if (!isset($profile))			die ($dietext . __FILE__ . __LINE__);
	// set non-present fields to default values
	switch ($targetrole) {
		case	'Docent':
				if (!isset($profile['teachersubject'])) 	{	$profile['teachersubject']	= "Nog geen vak opgegeven";							break;		}
		case	'Leerling':
				if (!isset($profile['pupillevel'])) 			{	$profile['pupillevel'] 			= "Nog geen studieniveau opgegeven";							}
				if (!isset($profile['pupilfavsubject'])) 	{	$profile['pupilfavsubject'] = "Nog geen favoriet vak opgegeven";		break;		}
		case	'Mentor':
				if (!isset($profile['mentorhairstyle'])) 	{	$profile['mentorhairstyle'] = "Nog geen haarstijl opgegeven";				break;		}
		case	'Ouder':
				if (!isset($profile['parentnumkids'])) 		{	$profile['parentnumkids'] 	= "Aantal kinderen onbekend";						break;		}
		case	'Staflid':
				if (!isset($profile['stafffavcolor'])) 		{	$profile['stafffavcolor'] 	= "Nog geen favoriete kleur opgegeven";	break;		}
	}
	if (!isset($profile['allmotto'])) 		{	$profile['allmotto'] = "Nog geen motto opgegeven";	}
	if (!isset($profile['allfavsport'])) 	{	$profile['allfavsport'] = "Nog geen favoriete sport opgegeven";	}
?>
			<div class="container">
			<div class="row">
				<?php include 'snippet_menu.html'; ?> <!-- takes 2 cols -->
				<div class="col-sm-10">
						<div class="well">
							<div><?php echo "Profiel van $targetuser, $targetrole, $targetschool."; ?></div>
						</div>
						<div class="well">
							<!-- all role dependant content initially hidden -->
							<div id="Docent" hidden>
								<?php //include 'snippet_vertical_space_30px.html'; ?>
								<?php divProfileItem ('teachersubject', 'Vak', $profile['teachersubject']); ?>
							</div>
							<div id="Leerling" hidden>
								<?php include 'snippet_vertical_space_30px.html'; ?>
								<?php divProfileItem ('pupillevel', 'Niveau', $profile['pupillevel']); ?>
								<?php include 'snippet_vertical_space_30px.html'; ?>
								<?php divProfileItem ('pupilfavsubject', 'Favoriete vak', $profile['pupilfavsubject']); ?>
							</div>
							<div id="Mentor" hidden>
								<?php include 'snippet_vertical_space_30px.html'; ?>
								<?php divProfileItem ('mentorhairstyle', 'Haarstijl', $profile['mentorhairstyle']); ?>
							</div>
							<div id="Ouder" hidden>
								<?php include 'snippet_vertical_space_30px.html'; ?>
								<?php divProfileItem ('parentnumkids', 'Aantal kinderen', $profile['parentnumkids']); ?>
							</div>
							<div id="Staflid" hidden>
								<?php include 'snippet_vertical_space_30px.html'; ?>
								<?php divProfileItem ('stafffavcolor', 'Favoriete kleur', $profile['stafffavcolor']); ?>
							</div>
							<!-- role independant content not hidden -->
							<div id="All">
								<?php include 'snippet_vertical_space_30px.html'; ?>
								<?php divProfileItem ('allmotto', 'Motto', $profile['allmotto']); ?>
								<?php include 'snippet_vertical_space_30px.html'; ?>
								<?php divProfileItem ('allfavsport', 'Favoriete sport', $profile['allfavsport']); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- show edit button if profile is own -->
		<div id="ownprofile" hidden><?php echo ($targetuser	== $user) ? "true" : "false" ?></div>
		<script>
			var mystr = "ownprofile";
			var myswitch = O(mystr);
			if (myswitch.innerHTML == "true") {
				var edit_tds = C("profile-edit", "td");
				for (var i = 0 ; i < edit_tds.length ; i++) {
					edit_tds[i].removeAttribute("hidden");
				}
			}
		</script>
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