<?php
	//Connect to database
    $mysqli = require __DIR__ ."/db.php";

	// Start the session if not already started
	session_start();

    //Generate Lobby Code
    $lobbyCode;

    // Generate a random 6-digit code
    $lobbyCode = sprintf('%06d', rand(100000, 999999));
    
    // add code to check for existing lobbyCode
    $sql = sprintf("SELECT *
                        FROM game_session
                        WHERE is_active = 'A'");
    $result = $mysqli->query($sql);
    
    // create codes array
    $allCodes = array();
    
    // loop through query, add to array of game codes
    while($codes = $result->fetch_assoc()) { // thanks Chris
        // Get the game code values
        $code = $codes['join_game_code'];
        array_push($allCodes, $code);
    }
    // loop through array of game codes
    while (in_array($lobbyCode, $allCodes)) {
         // keep randomly generating until it doesn't match
        $lobbyCode = sprintf('%06d', rand(100000, 999999));
    }
    
    echo $lobbyCode;
?>