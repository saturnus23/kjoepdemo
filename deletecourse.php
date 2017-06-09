<?php 	// deletecourse.php

	require_once 'functions.php'; 

	if 	(!isset($_GET['courseid']))	die ($dietext . __FILE__ . __LINE__);
	$courseid = $_GET['courseid'];

	if (isset($_GET['mode'])) {
		if ($_GET['mode'] == 'go') {
			// voorlopig gekozen voor cascaded delete, tzt is restricted de betere optie
			$query 	= "SELECT id FROM coursefollower WHERE courseid=$courseid ";
			$result = queryMySQL($query);
			for ($i = 0 ; $i < $result->num_rows ; $i++) {
				$row	= $result->fetch_array(MYSQLI_ASSOC);
				$id 	= $row['id'];
				$query 	= "DELETE FROM coursefollower WHERE id=$id ";
				$result = queryMySQL($query);
				if (!$result) die ($dietext . __FILE__ . __LINE__);
			}
			$query 	= "SELECT id FROM courseowner WHERE courseid=$courseid ";
			$result = queryMySQL($query);
			for ($i = 0 ; $i < $result->num_rows ; $i++) {
				$row	= $result->fetch_array(MYSQLI_ASSOC);
				$id 	= $row['id'];
				$query 	= "DELETE FROM courseowner WHERE id=$id ";
				$result = queryMySQL($query);
				if (!$result) die ($dietext . __FILE__ . __LINE__);
			}
			$query 	= "DELETE FROM course WHERE id=$courseid ";
			$result = queryMySQL($query);
			if (!$result) die ($dietext . __FILE__ . __LINE__);
			header("Location: dashboard.php");
		}
		die ($dietext . __FILE__ . __LINE__);
	} else {
		$query 	= "SELECT name, start, weight, description "
						.	"FROM 	course "
						.	"WHERE 	id=$courseid ";
		$result = queryMySQL($query);
		if ($result->num_rows != 1) die ($dietext . __FILE__ . __LINE__);
		$courses = "";
		$row 	= $result->fetch_array(MYSQLI_ASSOC);
		$nm 	= $row['name'];
		$st		= $row['start'];
		$wt		= $row['weight'];
		$dc		= $row['description'];
		$course = <<<_END
				<canvas id='divider' width='10' height='15'>Extra space</canvas>
				<div width='auto'>
					<div>
						<strong>$nm</strong>
					</div>
					<div><strong>Beschikbaar vanaf:</strong> $st</div>
					<div><strong>Studiepunten:</strong> $wt</div>
					<div>$dc</div>
				</div>
_END;
	}
	require_once 'header.php';
?>
		<div class="container">
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-10">
					<div class="well">
						<div><?php echo $deletecoursetext; ?></div>
					</div>
					<div class="well">
						<div id="mycourseowner"><?php echo "<strong>LESPAKKET VERWIJDEREN.</strong><br>"
													. $course; ?></div>
						<?php include 'snippet_vertical_space_15px.html'; ?>
						<div>
							<a href="deletecourse.php?mode=go&courseid=<?php echo $courseid; ?>" class="btn btn-danger"  role="button">VERWIJDER</a>
							<a href="dashboard.php" class="btn btn-default" role="button">Annuleer</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
				
