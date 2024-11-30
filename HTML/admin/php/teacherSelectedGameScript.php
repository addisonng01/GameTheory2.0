<?php
	//Connect to database
    $mysqli = require __DIR__ ."/db.php";

	// Start the session if not already started
	session_start();

    $teacher_id = $_SESSION["teacher_id"];
    $lobby_code = $_SESSION["lobby_code"];

    // Fires when a teacher selects a game
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["submit"])) {
            //If the form was submitted
            $gameSelected = $_POST["submit"];

            //If the game is redBlack
            if ($gameSelected == "redBlack") {
                //Create a game_session record in the database
                $sql = sprintf("INSERT INTO game_session (teacher_id, game_id, red_black_param, is_active, expiration_dt)
                                    VALUES ('%s', -- session
                                            1, -- redBlack game
                                            1, -- Default redBlack parameters
                                            'A', -- signals active
                                            current_timestamp() + interval 1 day
                                            );",
                        $mysqli->real_escape_string((int)$teacher_id)
                    );
                $result = $mysqli->query($sql);

                $sql = sprintf("SELECT MAX(game_session_id) AS game_session_id
                                    FROM game_session
                                    WHERE teacher_id = '%s';",
                                    $mysqli->real_escape_string((int)$teacher_id)
                                    );
                $result = $mysqli->query($sql);

                $row = $result->fetch_assoc();

                $_SESSION["game_session_id"] = $row['game_session_id'];

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
                
                $_SESSION["lobby_code"] = $lobbyCode;
                //Add code to join game
                $sql = sprintf("UPDATE game_session
                                    SET join_game_code = '%s'
                                    WHERE game_session_id = '%s'",
                                    $mysqli->real_escape_string($lobbyCode),
                                    $mysqli->real_escape_string($_SESSION["game_session_id"])
                                    );
                $result = $mysqli->query($sql);

                //Create red_black_session tracking record
                $sql = sprintf("INSERT INTO red_black_session (game_session_id)
                                    VALUES ('%s')",
                                    $mysqli->real_escape_string($_SESSION["game_session_id"]));

                //Send to the game lobby
                header("Location: studentWaitingRoom.php");
                exit;
            }
        }
    }
?>