<?php
    //Connect to database
    $mysqli = require __DIR__ ."/db.php";

    // Start the session if not already started
    session_start();

    //TODO: Change this out once the teacher creates a new session
    $_SESSION["game_session_id"] = 1;
    $game_session_id = $_SESSION["game_session_id"];

    //Get data from the database
    $sql = sprintf("SELECT COUNT(link_id) AS total_players
	                    FROM student_game_session
                        WHERE game_session_id = '%s'",
                        $mysqli->real_escape_string($game_session_id)
                            );
    
    $result = $mysqli->query($sql);
    $mysqli->close();
    $row = $result->fetch_assoc();
    $total_players = $row['total_players'];
    
    echo json_encode(['total_players' => $total_players]);
?>