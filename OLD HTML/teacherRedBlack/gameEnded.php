<?php
    //Connect to database
    $mysqli = require __DIR__ ."/db.php";

    // Start the session if not already started
    session_start();

    //Get variables from the session
    $game_session_id = $_SESSION['game_session_id'];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        //Set the current game session to inactive
        $sql = sprintf("UPDATE game_session SET is_active = 'I' WHERE game_session_id = '%s';", 
                            $mysqli->real_escape_string($game_session_id));
        $result = $mysqli->query($sql);

        //Destroy and clear the current session
        session_destroy();

        header("Location: https://cssgametheory.com/");
        exit();
    }
?>