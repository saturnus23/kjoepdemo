<?php // functions.php

	require_once 'dbcredentials.php';

	{	// database openen
		$connection = new mysqli(HOST, DBUSER, DBPASS, DB);
		if ($connection->connect_error)
			die($connection->connect_error);
}
{ // initialiseren van variabelen
	$error	= "";
	$user		= "";
}
{ // vaste teksten (content)
	$introtext					= APPNAME
											. " is een platform voor Gepersonaliseerd Leren. "
											. "Het faciliteert leerlingen bij het samenstellen van een persoonlijk leerplan, geeft leerkrachten de mogelijkheid om daarvoor lespakketten aan te bieden. Etcetera.";
	$registertext				= "Om uw registratie af te maken hebben we nog een paar gegevens van u nodig.";
	$coursetext					= "Hier kunt u een lespakket opgeven waarop leerlingen kunnen intekenen. Beschrijf het lespakket zo duidelijk mogelijk.";
	$helptext01					= "Deze tekst kan natuurlijk worden aangepast :-)";
	$helptext02					= "In de Demofase is het handig om alleen voornamen te gebruiken.";
	$pupilrole					= "Leerling";
	$teacherrole				= "Docent";
	$mentorrole					= "Mentor";
	$parentrole					= "Ouder";
	$staffrole					= "Staf";
	$foutNoInput				= "Dit veld moet worden ingevoerd";
	$foutNoInputs				= "Deze velden moeten worden ingevoerd";
	$foutMax20					= "Veld mag maximaal 20 tekens bevatten";
	$foutMax200					= "Veld mag maximaal 200 tekens bevatten";
	$foutMax3000				= "Veld mag maximaal 3000 tekens bevatten";
	$foutNotNumeric			= "Veld moet een getal bevatten";
	$askSelect					= "Maak een keuze";
	$dietext						= "Onverwachte logische fout in ";
	$askcrsename				= "Naam voor het lespakket";
	$askcrsestart				= "Beschikbaar per...";
	$askcrseweight			= "Waarde van het lespakket (in studiepunten)";
	$askcrsedescription	= "Beschrijf hier het lespakket zo duidelijk mogelijk. Bijvoorbeeld: beschrijf de vereiste of gewenste voorkennis, de lesmethode, het aantal lesuren per week enzovoorts enzovoorts.";
	$alreadyexists			= "Er is al een gegeven met deze naam";
	$deletecoursetext		= "Pas op: deze handeling kan niet ongedaan gemaakt worden.";
}
{	// functions
	function queryMysql($query) {					// HANDHAVEN voor KJOEPDEMO
		global $connection;
		$result = $connection->query($query);
		if (!$result) {
			die($connection->error);
		}
		return $result;
	}
	function destroySession() {						// HANDHAVEN voor KJOEPDEMO
		$_SESSION=array();
		if (	session_id() != ""
		||  	isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-2592000, '/');
		}
	}
	function sanitizeString($var) {				// HANDHAVEN voor KJOEPDEMO
		global $connection;
		$var	= strip_tags($var);
		$var	= htmlentities($var);
		$var	= stripslashes($var);
		$var	= trim($var);
		return $connection->real_escape_string($var);
	}
	function showProfile($user) {					// GEBRUIK voor KJOEPDEMO staat nog ter discussie
		if (file_exists("$user.jpg")) {
			echo "<img src='$user.jpg' style='float:left'>";
		}
		$result = queryMysql("SELECT * FROM profiles WHERE user='$user'");
		if ($result->num_rows) {
			$row = $result->fetch_array(MYSQLI_ASSOC);
			echo stripslashes($row['text']) . "<br style='clear:left;'><br>";
		}
	}
	// fetch missing data from db and store in session
	function assureDbFieldIntoSession ($nameinsession, $nameindb, $table, $searchitem, $searchvalue) {
		if (!isset($_SESSION[$nameinsession])) {
			$_SESSION[$nameinsession] = fetchOneFieldFromDbTable ($nameindb, $table, $searchitem, $searchvalue);
		}
		return $_SESSION[$nameinsession];
	}
	// fetch One Field From Db Table
	function fetchOneFieldFromDbTable ($nameindb, $table, $searchitem, $searchvalue) {
		$query 		= "SELECT $nameindb FROM $table WHERE $searchitem = '$searchvalue'";
		$result	= queryMySQL($query);
		if ($result->num_rows == 0) die ("Onverwachte logische fout in " . __FILE__ . ", regel " . __LINE__ . "($query)");
		$result 	= queryMySQL($query);
		$row			= $result->fetch_array(MYSQLI_ASSOC);
		return $row[$nameindb];
	}
	function storeProfileItem ($personid, $field, $val) {
		$value	= sanitizeString($val);
		//read, then either insert or update
		$query 	= "SELECT id FROM profile WHERE personid=$personid";
		$result = queryMySQL($query);
		if ($result->num_rows == 0) {	// no profile yet
			$query 	= "INSERT INTO profile (id, personid, $field) "
							. "VALUES (null, $personid, '$value')";
			$result = queryMySQL($query);
			if (!$result) die ($dietext . __FILE__ . __LINE__);
		} else {	//profile already exists
			$row		= $result->fetch_array(MYSQLI_ASSOC);
			$id 		= $row['id'];
			$query 	= "UPDATE profile "
							.	"SET $field='$value' "
							.	"WHERE id=$id";
			$result = queryMySQL($query);
			if (!$result) die ($dietext . __FILE__ . __LINE__);
		}
	}
	function divProfileItem ($item, $caption, $value) {
	// $item = teachersubject
	// $caption = Vak
		$show = "$item" . "showfrm";
		$edit = "$item" . "editfrm";
		
		$htmlstr  = "<div id='$show'>";
		$htmlstr .= 	"<table class='table-responsive'>";
		$htmlstr .= 		"<tr>";
		$htmlstr .= 			"<td width='120px' class='profile-edit'";
		if (substr($item,0,3) != 'all') {	// 'all' profile items are never optional
			$htmlstr .= 			"hidden";			// other profile items are hidden by default
		}																	// will selectively be shown by Javascript later
		$htmlstr .= 				">";
		$htmlstr .= 				"<button type='button' class='btn btn-secondary'";
		$htmlstr .= 					"onclick=\"EditProfileItem('$item')\">Wijzig</button>";
		$htmlstr .= 			"</td>";
		$htmlstr .= 			"<td width='100px'><strong>$caption</strong></td>";
		$htmlstr .= 			"<td>$value</td>";
		$htmlstr .= 		"</tr>";
		$htmlstr .= 	"</table>";
		$htmlstr .= "</div>";
		
		$htmlstr .= "<div id='$edit' hidden>";	// will be shown through onclick event
		$htmlstr .= 	"<form action='profile.php' method='get'>";
		$htmlstr .= 		"<div class='form-group'>";
		$htmlstr .= 			"<div class='input-group'>";
		$htmlstr .= 				"<span class='input-group-addon'>$caption: </span>";
		$htmlstr .= 				"<input id='msg' type='text' class='form-control'";
		$htmlstr .= 				"name='$item' value='$value'>";
		$htmlstr .= 			"</div>";
		$htmlstr .= 		"</div>";
		$htmlstr .= 		"<button type='submit' class='btn btn-primary'>OK</button>";
		$htmlstr .= 	"</form>";
		$htmlstr .= "</div>";
		echo $htmlstr;
	}
	function errormessage ($error) {
		echo <<<_END
	<div class="alert alert-danger">
		<strong>Oeps! </strong>$error;
	</div>
_END;
;
	}
	
}
?>