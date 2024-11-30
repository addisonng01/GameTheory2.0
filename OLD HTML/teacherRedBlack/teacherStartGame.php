<?php
    //Connect to database
    $mysqli = require __DIR__ ."/db.php";

    // Start the session if not already started
    session_start();

    //Get values from the session
    $game_session_id = $_SESSION["game_session_id"];

    //Fetch all the students that are in the game session
        //Records are randomized with ORDER BY RAND()
    $sql = sprintf("SELECT link_id FROM student_game_session WHERE game_session_id = '%s' ORDER BY RAND();",
                    $mysqli->real_escape_string($game_session_id)
                    );
    $result = $mysqli->query($sql);       
    
    if ($result) {
        //Get number of records
        $num_records;
        $player_list = []; //A list of link_id's from the players in the room

        //Load all of the current players into the player_list
        while ($row = $result->fetch_assoc()) {
            $player_list[] = $row["link_id"];
        }

        //Set the number of records
        $num_records = count($player_list);

        //Loop to generate the partners for each round
            //Hardcoded to 12
        for ($i = 1; $i <= 12; $i++) { //Commented out to not make all pairings right away
            //Creates the pairs
            shuffle($player_list);
            $count = 0;
            while ($count < ($num_records)) {
                //Create the pairing ID
                $sqlP = sprintf("INSERT INTO partner_pairing (round_num) VALUES ('%s');",
                $mysqli->real_escape_string($i));
                $resultP = $mysqli->query($sqlP);
                $last_inserted_id = $mysqli->insert_id;

                //Insert session_partner records
                    //Partner 1
                $sqlL = sprintf("INSERT INTO session_partner (link_id, pairing_id, round_num) VALUES ('%s', '%s', '%s');",
                                    $mysqli->real_escape_string($player_list[$count]),
                                    $mysqli->real_escape_string($last_inserted_id),
                                    $mysqli->real_escape_string($i)
                                    );
                $resultL = $mysqli->query($sqlL);
                error_log("Partner 1 Inserted: ". $player_list[$count]);
                error_log("Count: ". $count);
                    //Partner 2
                if (($count + 1) < $num_records) {
                    //Ensure that NULL values are not being inserted into the DB
                    error_log("SECOND PERSON FOR PARTNER HAS BEEN RUN");
                    error_log("Count + 1:" . $count + 1);
                    error_log("Number of Records: " . $num_records);
                    $sqlL = sprintf("INSERT INTO session_partner (link_id, pairing_id, round_num) VALUES ('%s', '%s', '%s');",
                                    $mysqli->real_escape_string($player_list[$count + 1]),
                                    $mysqli->real_escape_string($last_inserted_id),
                                    $mysqli->real_escape_string($i)
                                );
                    $resultL = $mysqli->query($sqlL);
                    error_log("Partner 2 Inserted: ". $player_list[$count + 1]);
                    error_log("Count: ". $count);
                }

                //Increments the count to the next 2 players
                $count += 2;
            }
        }
        //Sets the red_black_session rounds_complete to 0, singaling the student's end that it is time to start
        $sql = sprintf("INSERT INTO red_black_session (game_session_id, rounds_complete, round_concluded) VALUES ('%s', 0, 'N');",
                        $mysqli->real_escape_string($game_session_id)
                        );
        $result = $mysqli->query($sql);

        //Adds the round_num to the session
        $_SESSION['round_num'] = 1;

        //Send user to the teacherGamePrompt.html
        header("Location: /CSSGametheory/HTML/teacherRedBlack/teacherGamePrompt.php");
        $mysqli->close();
    }

?>