<?php
    //Connect to database
		$mysqli = require __DIR__ ."/db.php";

    // Start the session if not already started
        session_start();
    
	//TODO: Change this out once the teacher creates a new session
		$_SESSION["game_session_id"] = 1;
		$game_session_id = $_SESSION["game_session_id"];
	
    //Get data from the database
        $sql = sprintf("SELECT CONCAT(p.first_nm, ' ', p.last_nm) AS full_nm
									FROM student_game_session s
										JOIN student_profile p
											ON p.student_id = s.user_id
									WHERE s.game_session_id = '%s'",
									$mysqli->real_escape_string($game_session_id)
								);
        
        $result = $mysqli->query($sql);
        $mysqli->close();
	//Create 3 arrays to break up the names
		$row1 = [];
		$row2 = [];
		$row3 = [];
	//For each row in result, divide them into each of the three arrays
		$count = 1;
		$total_name_count = 0; //TODO: Use to check if there are more student records that have been inserted.
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

        // Output the HTML content for the usernames-container div
        echo "<div class='column'>";
        foreach ($row1 as $name) {
          echo "<div class='username'>" . $name . "</div>";
        }
        echo "</div>";
        
        echo "<div class='column'>";
        foreach ($row2 as $name) {
          echo "<div class='username'>" . $name . "</div>";
        }
        echo "</div>";
        
        echo "<div class='column'>";
        foreach ($row3 as $name) {
          echo "<div class='username'>" . $name . "</div>";
        }
        echo "</div>";
?>