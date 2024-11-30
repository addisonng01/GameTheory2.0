<?php
    //Connect to database
    $mysqli = require __DIR__ ."/db.php";

    // Start the session if not already started
    session_start();

    //Get variables from the session
    $game_session_id = $_SESSION['game_session_id'];
    $_SESSION["round_num"] = $_SESSION["round_num"] + 1;
    $round_num = $_SESSION["round_num"];

    //TODO: Change this to a dynamic value from the session when rulesets are implemented
    $total_rounds = 12;

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        //IF there are more rounds to be played in this game
        if ($round_num <= $total_rounds) {
            //Generate student pairings for the next round
                //Fetch all the students that are in the game session
                //Records are randomized with ORDER BY RAND()
            $sql = sprintf("SELECT link_id FROM student_game_session WHERE game_session_id = '%s' ORDER BY RAND();",
                            $mysqli->real_escape_string($game_session_id)
                            );
            $result = $mysqli->query($sql);       
            
            if ($result) {
                //Get number of records
                $num_records;
                $count = 0;
                $player_list = []; //A list of link_id's from the players in the room

                //Load all of the current players into the player_list
                while ($row = $result->fetch_assoc()) {
                    $player_list[] = $row["link_id"];
                }

                //Set the number of records
                $num_records = count($player_list);
                error_log("Number of records: " . $num_records);
                /*
                //Creates the pairs
                while ($count < ($num_records)) {
                    //Create the pairing ID
                    $sqlP = sprintf("INSERT INTO partner_pairing (round_num) VALUES ('%s');",
                    $mysqli->real_escape_string($round_num));
                    $resultP = $mysqli->query($sqlP);
                    $last_inserted_id = $mysqli->insert_id;

                    //Insert session_partner records
                        //Partner 1
                    $sqlL = sprintf("INSERT INTO session_partner (link_id, pairing_id, round_num) VALUES ('%s', '%s', '%s');",
                                        $mysqli->real_escape_string($player_list[$count]),
                                        $mysqli->real_escape_string($last_inserted_id),
                                        $mysqli->real_escape_string($round_num) 
                                        );
                    $resultL = $mysqli->query($sqlL);
                    error_log("Partner 1 Inserted: ". $player_list[$count]);
                    error_log("Count: ". $count);
                        //Partner 2
                    if (($count + 1) <= $num_records) {
                        //Ensure that NULL values are not being inserted into the DB
                        $sqlL = sprintf("INSERT INTO session_partner (link_id, pairing_id, round_num) VALUES ('%s', '%s', '%s');",
                                        $mysqli->real_escape_string($player_list[$count + 1]),
                                        $mysqli->real_escape_string($last_inserted_id),
                                        $mysqli->real_escape_string($round_num)
                                    );
                        $resultL = $mysqli->query($sqlL);
                        error_log("Partner 2 Inserted: ". $player_list[$count + 1]);
                        error_log("Count: ". $count);
                    }

                    //Increments the count to the next 2 players
                    $count += 2;
                }
                */
            }

            //Change round_concluded to 'N' to signal the students to move to the next page
            $sql = sprintf("UPDATE red_black_session SET round_concluded = 'N'
                                WHERE game_session_id = '%s'
                                ORDER BY rb_session_id DESC 
                                LIMIT 1;",
                            $mysqli->real_escape_string($game_session_id)
                            );
            $result = $mysqli->query($sql);

            header("Location: teacherGamePrompt.php");
            exit();
        } else if ($round_num > $total_rounds) {
            //IF the game is over
            //Change round_concluded to 'N' to signal the students to move to the next page
            $sql = sprintf("UPDATE red_black_session SET round_concluded = 'N'
                                WHERE game_session_id = '%s'
                                ORDER BY rb_session_id DESC 
                                LIMIT 1;",
                            $mysqli->real_escape_string($game_session_id)
                            );
            $result = $mysqli->query($sql);

            header("Location: teacherFinalResults.php");
            exit();
        }
    }
?>