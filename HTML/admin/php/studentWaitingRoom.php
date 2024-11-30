<?php
    //Connect to database
		$mysqli = require __DIR__ ."/db.php";

    // Start the session if not already started
        session_start();

	//Gets values from the session
		$game_session_id = $_SESSION["game_session_id"];
		$lobby_code = $_SESSION["lobby_code"];
	
    //Get data from the database
        $sql = sprintf("SELECT CONCAT(p.first_nm, ' ', p.last_nm) AS full_nm
									FROM student_game_session s
										JOIN student_profile p
											ON p.student_id = s.user_id
									WHERE s.game_session_id = '%s'",
									$mysqli->real_escape_string($game_session_id)
								);
        
        $result = $mysqli->query($sql);
        //$mysqli->close();
	//Create 3 arrays to break up the names
		$row1 = [];
		$row2 = [];
		$row3 = [];
	//For each row in result, divide them into each of the three arrays
		$count = 1;
		$total_name_count = 0; 
		while( $row = $result->fetch_assoc() ) {
			if ( $count == 1 ) {
				$row1[] = $row["full_nm"];
				$count = 2;
			} else if ( $count == 2) {
				$row2[] = $row["full_nm"];
				$count = 3;
			} else if ( $count == 3) {
				$row3[] = $row["full_nm"];
				$count = 1;
			}
			$total_name_count++;
		}
	//Checks if there is not a red_black_session created
	$sql = sprintf("SELECT * FROM red_black_session WHERE game_session_id = '%s';",
		$mysqli->real_escape_string($game_session_id));
	$result = $mysqli->query($sql);
	if ( $result->num_rows == 0) {
		//Create a instance of red_black_session
		$sql = sprintf("INSERT INTO red_black_session (game_session_id) VALUES ('%s');",
			$mysqli->real_escape_string($game_session_id));
	}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<!-- added for refresh -->
	<meta http-equiv="refresh" content="10">

	<title>Centered Usernames</title>
	<link href="/CSSGametheory/css/studentWaitingRoom.css" rel="stylesheet" type="text/css" />

	<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        //Refreshes the page, shows new students in the lobby
		function autoRefresh() {
			window.location = window.location.href;
		}
		setInterval('autoRefresh()', 10000);
    </script>
</head>
<body>
    
<link href="https://cssgametheory.com/CSSGametheory/css/header.css" rel="stylesheet" />
<p><img id="logo" src="https://cssgametheory.com/CSSGametheory/Img/logo.svg"></p> 


<div style="">
<div style="display: flex; justify-content: center; align-items: center;">
	<h1>Pin</h1>
	</br>

	<h2><?php echo $lobby_code; ?></h2>
</div>
	<h1 >Students In Room</h1>
</div>
<div class="usernames-container">
	<div class="column">
	<?php
		foreach ( $row1 AS $name ) {
			echo "<div class='username'>". $name ."</div>";
		}
	?>
	</div>
	<div class="column">
	<?php
		foreach ( $row2 AS $name ) {
			echo "<div class='username'>". $name ."</div>";
		}
	?>
	</div>
	<div class="column">
	<?php
		foreach ( $row3 AS $name ) {
			echo "<div class='username'>". $name ."</div>";
		}
	?>
	</div>
</div>
</div>
</div>
<form action="/CSSGametheory/HTML/teacherRedBlack/teacherStartGame.php" method = "POST">
	<button style="float: right;">Start Game</button>
</form>
</body>
</html>