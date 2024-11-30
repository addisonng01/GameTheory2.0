<?php
    // Start the session if not already started
        session_start();
    //Pulls game_session_id from the session into a variable to be used
        $game_session_id = $_SESSION["game_session_id"];
        $round_num = $_SESSION["round_num"];
    //Connect to database
        $mysqli = require __DIR__ ."/db.php";

    //Gather all valid Pairing IDs
        $sql = sprintf("SELECT DISTINCT p.pairing_id
                            FROM session_partner p
                                JOIN student_game_session g
                                    ON g.link_id = p.link_id
                            WHERE g.game_session_id = '%s'
                            AND p.round_num = '%s'",
        $mysqli->real_escape_string($game_session_id),
        $mysqli->real_escape_string($round_num));
        $result = $mysqli->query($sql);
        if ($result->num_rows > 0) {
            //Gets all valid pairing IDs
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[] = $row['pairing_id'];
            }
            //print_r($data);

            $mysqli->close();

            //Loop(For each pairing ID in $data) SQL querry to get each partner pairing in this round
            

        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8">
</head>
<style>
    .horizontal-row {
        display: flex; /* Use flexbox to create a horizontal row */
        justify-content: space-between; /* Space items evenly along the row */
        /*margin: 10px 0; /* Add some spacing between rows */
    }
    /* Style for the container */
    .container {
        display: flex;
    }

    /* Style for the left column */
    .column {
        flex: 1; /* Make the column take up all available space */
        padding: 20px;
        border: 1px solid #ccc;
    }
    button {
        height: 65px;
        width: 150px;
    }
    /* Apply basic styles to the table */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-family: Arial, sans-serif;
    }

    /* Style the table header */
    th {
        background-color: #f2f2f2;
        color: #333;
        font-weight: bold;
        padding: 10px;
        text-align: left;
        border-bottom: 2px solid #ddd;
    }

    /* Style the table rows */
    td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    /* Add a hover effect to the table rows */
    tr:hover {
        background-color: #f5f5f5;
    }

    /* Style the first column differently (optional) */
    td:first-child {
        font-weight: bold;
        color: #007bff; /* Change the color as needed */
    }

</style>
<body>
    <div class="horizontal-row">
        <h1>Round: <?php echo $round_num;?></h1>
        <form action="nextRound.php" method="POST">
            <button>Next Round</button>
        </form>
    </div>
    <div class="container">
    <div class="column">
    <table>
        <tr>
            <th>Ranking</th>
            <th>Name</th>
            <th>Total Score</th>
        </tr>
        <?php
            //Connect to database
                $mysqli = require __DIR__ ."/db.php";
            //Runs and gets the results for the total of Red and Black cards played per round query
                $sql = sprintf("SELECT CONCAT(p.first_nm, ' ', p.last_nm) AS full_nm,
                                        SUM(round_score) AS total_score
                                        FROM player_round_history h
                                            JOIN student_game_session g
                                                ON h.link_id = g.link_id
                                            JOIN student_profile p
                                                ON p.student_id = g.user_id
                                        WHERE g.game_session_id = '%s'
                                        GROUP BY p.first_nm
                                        ORDER BY total_score DESC",
                                        $mysqli->real_escape_string($game_session_id));
                $result = $mysqli->query($sql);

                if ($result->num_rows > 0) {
                    //If the query function's properly

                    //Var to keep track of each row entry
                    $rowCount = 0;

                    while ($row = $result->fetch_assoc()) {
                        $rowCount++;
                        echo "<tr><td>" . '#' . $rowCount . "</td><td>" . $row["full_nm"] . "</td><td>". $row["total_score"] . "</td></tr>";
                    }
                    echo "</table>";
                } else {
                    echo "No Results";
                }
                $mysqli-> close();
            ?>
    </table>
    </div>
    <div class="column">
    <table>
        <tr>
            <th>Group</th>
            <th>Player 1</th>
            <th>Player 2</th>
            <th>P1 Selected</th>
            <th>P2 Selected</th>
            <th>P1 Score</th>
            <th>P2 Score</th>
        </tr>
        <?php
            //Connect to database
                $mysqli = require __DIR__ ."/db.php";

                $rowCount = 0;

                foreach ($data as $pairing_id) {
                    // SQL query with placeholders
                        // 11/28: Added new subquery that counts how many users share the partner_id
                    $sql = sprintf("SELECT 
                    (SELECT CONCAT(s.first_nm, ' ', s.last_nm) AS full_nm FROM session_partner p
                    JOIN student_game_session g ON g.link_id = p.link_id
                    JOIN student_profile s ON s.student_id = g.user_id
                    WHERE p.pairing_id = '%s' AND p.link_id = (SELECT MAX(link_id) FROM session_partner WHERE pairing_id = '%s') LIMIT 1) AS player_1,
                    (SELECT CONCAT(s.first_nm, ' ', s.last_nm) AS full_nm FROM session_partner p
                    JOIN student_game_session g ON g.link_id = p.link_id
                    JOIN student_profile s ON s.student_id = g.user_id
                    WHERE p.pairing_id = '%s' AND p.link_id = (SELECT MIN(link_id) FROM session_partner WHERE pairing_id = '%s') LIMIT 1) AS player_2,
                    (SELECT h.card_selected FROM session_partner p
                    JOIN student_game_session g ON g.link_id = p.link_id
                    JOIN player_round_history h ON h.link_id = g.link_id
                    WHERE p.pairing_id = '%s' AND p.link_id = (SELECT MAX(link_id) FROM session_partner WHERE pairing_id = '%s') AND h.round_num = '%s' LIMIT 1) AS p1_selected,
                    (SELECT h.card_selected FROM session_partner p
                    JOIN student_game_session g ON g.link_id = p.link_id
                    JOIN player_round_history h ON h.link_id = g.link_id
                    WHERE p.pairing_id = '%s' AND p.link_id = (SELECT MIN(link_id) FROM session_partner WHERE pairing_id = '%s') AND h.round_num = '%s' LIMIT 1) AS p2_selected,
                    (SELECT SUM(round_score) FROM player_round_history
                    WHERE link_id = (SELECT MAX(link_id) FROM session_partner WHERE pairing_id = '%s') AND round_num <= '%s') AS p1_score,
                    (SELECT SUM(round_score) FROM player_round_history
                    WHERE link_id = (SELECT MIN(link_id) FROM session_partner WHERE pairing_id = '%s') AND round_num <= '%s') AS p2_score,
                    (SELECT COUNT(link_id) FROM session_partner
                    WHERE pairing_id = '%s') AS num_partners,
                    (SELECT p.link_id AS full_nm FROM session_partner p
                    WHERE p.pairing_id = '%s' AND p.link_id = (SELECT MAX(link_id) FROM session_partner WHERE pairing_id = '%s') LIMIT 1) AS p1_link_id
                    FROM dual",
                    $mysqli->real_escape_string($pairing_id),
                    $mysqli->real_escape_string($pairing_id),
                    $mysqli->real_escape_string($pairing_id),
                    $mysqli->real_escape_string($pairing_id),
                    $mysqli->real_escape_string($pairing_id),
                    $mysqli->real_escape_string($pairing_id),
                    $mysqli->real_escape_string($round_num),
                    $mysqli->real_escape_string($pairing_id),
                    $mysqli->real_escape_string($pairing_id),
                    $mysqli->real_escape_string($round_num),
                    $mysqli->real_escape_string($pairing_id),
                    $mysqli->real_escape_string($round_num),
                    $mysqli->real_escape_string($pairing_id),
                    $mysqli->real_escape_string($round_num),
                    $mysqli->real_escape_string($pairing_id),
                    $mysqli->real_escape_string($pairing_id),
                    $mysqli->real_escape_string($pairing_id)
                    );

                    // Get the result
                    $result = $mysqli->query($sql);

                    // Process the data
                    while ($row = $result->fetch_assoc()) {
                        $rowCount++;

                        //Checks if the scores are null, sets them to 0
                        $score1;
                        $score2;
                        if ($row["p1_score"] == NULL) {
                            $score1 = 0;
                        } else {
                            $score1 = $row["p1_score"];
                        }
                        if ($row["p2_score"] == NULL) {
                            $score2 = 0;
                        } else {
                            $score2 = $row["p2_score"];
                        }

                        //Check's if the row results are the same, if they are
                        if ($row["num_partners"] == 1) {
                            //Get the total score before this round
                            $sqlPrevScore = sprintf("SELECT SUM(round_score) AS prev_score FROM player_round_history
                                                WHERE link_id = '%s' AND round_num < '%s';",
                                            $mysqli->real_escape_string($row["p1_link_id"]),
                                            $mysqli->real_escape_string($round_num));
                            $resultPrevScore = $mysqli->query($sqlPrevScore);
                            $rowPrevScore = $resultPrevScore->fetch_assoc();

                            $previous_round_score = $rowPrevScore["prev_score"];

                            //Get the card selected by this player
                            $sqlPrevCard = sprintf("SELECT card_selected FROM player_round_history
                                                        WHERE link_id = '%s' AND round_num = '%s';",
                                            $mysqli->real_escape_string($row["p1_link_id"]),
                                            $mysqli->real_escape_string($round_num));
                            $resultPrevCard = $mysqli->query($sqlPrevCard);
                            $rowPrevCard = $resultPrevCard->fetch_assoc();

                            $previous_card = $rowPrevCard["card_selected"];

                            //Determines what card the BOT played
                            $cur_card_value;
                            if ($row["p1_selected"] == 'RED') {
                                $cur_card_value = 50;
                            } else {
                                $cur_card_value = 0;
                            }

                            $points_gained_from_partner = ($previous_round_score + $cur_card_value) - $score1;

                            $bot_card_played = 'N/A';

                            if ($points_gained_from_partner == 0) {
                                $bot_card_played = 'RED';
                            } else {
                                $bot_card_played = 'BLACK';
                            }

                            //Table results if 1 person did not have a partner
                            echo "<tr><td>" . '#' . $rowCount . "</td><td>" . $row["player_1"] . "</td><td>"
                            . "BOT" . "</td><td>" . $row["p1_selected"] . "</td><td>" . $bot_card_played . "</td><td>" . $score1 . "</td><td>"
                            . "N/A" . "</td></tr>";
                        } else {
                            //Normal table results for 2 partners
                            
                            //Checking if a player did not select their card
                            $p1_sel;
                            if ($row["p1_selected"] != NULL) {
                                $p1_sel = $row["p1_selected"];
                            } else {
                                $p1_sel = "NO SELECTION";
                            }
                            
                            
                            $p2_sel;
                            if ($row["p2_selected"] != NULL) {
                                $p2_sel = $row["p2_selected"];
                            } else {
                                $p2_sel = "NO SELECTION";
                            }
                            
                            //Print out the table
                            echo "<tr><td>" . '#' . $rowCount . "</td><td>" . $row["player_1"] . "</td><td>"
                            . $row["player_2"] . "</td><td>" . $p1_sel . "</td><td>" . $p2_sel . "</td><td>" . $score1 . "</td><td>"
                            . $score2 . "</td></tr>";
                        }
                    }
                    }
                    echo "</table>";

                    // Close the statement
                    $mysqli->close();
            ?>
    </table>
    </div>
    </div>
</body>
</html>