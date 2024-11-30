<?php
    //Connect to database
    $mysqli = require __DIR__ ."/db.php";

    // Start the session if not already started
    session_start();
    
    //Get values from the session
    $game_session_id = $_SESSION["game_session_id"];

    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        //Increment the rounds complete by 1, singaling the students to move to the next round
        $sql = sprintf("UPDATE red_black_session
                            SET rounds_complete = rounds_complete + 1,
                                round_concluded = 'Y'
                            WHERE game_session_id = '%s'
                            ORDER BY rb_session_id DESC 
                            LIMIT 1;",
                        $mysqli->real_escape_string($game_session_id)
                        );
        $result = $mysqli->query($sql);

        //Redirect to the round complete page
        header ("Location: scoreCalcWaitingPage.php");
        exit();
    }
?>