<?php
    //Connect to database
    $mysqli = require __DIR__ ."/db.php";

    // Start the session if not already started
    session_start();

    //Get variables from the session
    $link_id = $_SESSION['link_id'];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        //Set the current game session to inactive
        $sql = sprintf("UPDATE student_game_session SET active_participant = 'I' WHERE link_id = '%s';", 
                            $mysqli->real_escape_string($link_id));
        $result = $mysqli->query($sql);

        //Destroy and clear the current session
        session_destroy();

        header("Location: https://cssgametheory.com/");
        exit();
    }
?>