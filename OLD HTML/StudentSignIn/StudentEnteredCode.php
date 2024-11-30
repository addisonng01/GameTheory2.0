<?php
    //Connect to database
    $mysqli = require __DIR__ ."/db.php";

	// Start the session if not already started
	session_start();

    //Get values from the session
    $session_id = $_SESSION["session_id"];
    $user_id = $_SESSION["user_id"];
    $game_code = $_POST["gameCode"];

    // Fires when a teacher selects a game
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        //Check if there is a game session with the code from POST
        $sql = sprintf("SELECT game_session_id
                            FROM game_session
                            WHERE join_game_code = '%s' -- Value entered by the user
                            AND is_active = 'A';",
                            $mysqli->real_escape_string($game_code)
                        );
        $result = $mysqli->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $game_session_id = $row["game_session_id"];

            //Set the game_session_id for the student's session
            $_SESSION["game_session_id"] = $game_session_id;

            //Create a record that links the student to the game session
            $sql = sprintf("INSERT INTO student_game_session (game_session_id, session_id, user_id, active_participant)
                                VALUES ('%s', -- From previous query that checks for the session
                                '%s', -- session
                                '%s', -- session
                                'A'
                                );",
                                $mysqli->real_escape_string($game_session_id),
                                $mysqli->real_escape_string($session_id),
                                $mysqli->real_escape_string($user_id)
                                );
            
            $result = $mysqli->query($sql);

            //Get the link_id from the newly created record
            $sql = sprintf("SELECT link_id
                                FROM student_game_session
                                WHERE user_id = '%s' -- session
                                    AND session_id = '%s' -- session
                                    AND game_session_id = '%s' -- from previous query
                                    AND active_participant = 'A'
                                ORDER BY link_id DESC;",
                                $mysqli->real_escape_string($user_id),
                                $mysqli->real_escape_string($session_id),
                                $mysqli->real_escape_string($game_session_id)
                                );
            $result = $mysqli->query($sql);
            $row = $result->fetch_assoc();

            $link_id = $row["link_id"];

            //Add the new link_id to the session
            $_SESSION["link_id"] = $link_id;

            //Send the user to the studentWaitingPage.php for them to wait for the instructor to start the game
            header("Location: /CSSGametheory/HTML/StudentSignIn/studentWaitingPage.php");
            exit;
        } else {
            //Send the user back to the code entry screen with the a message telling them to try another code
                //Add a value to the session that checks if the code is correct, when it is set to 'F', display a message on the code entry screen
            $_SESSION["game_exists"] = "F";
            header("Location: /CSSGametheory/HTML/StudentSignIn/studentEnterCode.php");
            exit;
        }
    }
?>