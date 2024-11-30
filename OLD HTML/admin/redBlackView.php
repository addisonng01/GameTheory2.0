<?php

    $mysqli = require __DIR__ ."/db.php";

    //Required variable declaration
    $game_session_id;
    $create_dt;
    $total_players;
    $total_rounds;
    $total_black_cards;
    $average_black_cards_per_round;
    $total_red_cards;
    $average_red_cards_per_round;
    $player_top_score;
    $player_bottom_score;
    $player_top_black_cards;
    $player_bottom_black_cards;
    $player_top_red_card;
    $player_bottom_red_cards;

    //Parsing the game_session_id from the URL
        session_start();
        //print_r($_SESSION);
        $var = $_SERVER['QUERY_STRING'];
        //print_r($var);
        $parts = explode('=', $var);
        if (count($parts) > 1) {
            $game_session_id = $parts[1];
        } else {
            //echo'No "=" found in the string.';
            // this messes up the header, so I commented it out
        }
        //print_r($game_session_id);
    
    //Save the Game Date to a variable
        $sql = sprintf("SELECT create_dt 
                                    FROM game_session 
                                    WHERE game_session_id = '%s';",
                                $mysqli->real_escape_string($game_session_id));
                                $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $create_dt = $row['create_dt'];
            } else {
                $create_dt = "No rows found for game_session_id: $game_session_id";
            }
        } else {
            $create_dt = "Error: " . $mysqli->error;
        }
        $result->close();

    //Total Number of Players
        $sql = sprintf("SELECT COUNT(game_session_id) AS total FROM student_game_session WHERE game_session_id = '%s';",
                                $mysqli->real_escape_string($game_session_id));
        $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $total_players = $row['total'];
            } else {
                $total_players = "No rows found for game_session_id: $game_session_id";
            }
        } else {
            $total_players = "Error: " . $mysqli->error;
        }
        $result->close();

    //Total Number of Rounds
        $sql = sprintf("SELECT p.total_round_num 
                                    FROM red_black_card_param p
                                        JOIN game_session g
                                            ON g.red_black_param = p.red_black_id
                                    WHERE g.game_session_id = '%s';",
        $mysqli->real_escape_string($game_session_id));
        $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $total_rounds = (int)$row['total_round_num'];
                $total_rounds = (int)$total_rounds;
            } else {
                $total_rounds = 1;
            }
        } else {
            $total_rounds = "Error: " . $mysqli->error;
        }
        $result->close();
    //Total Number of Black Cards Played
        $sql = sprintf("SELECT COUNT(card_selected) AS total
                            FROM player_round_history h
                                JOIN student_game_session s
                                    ON h.session_id = s.session_id
                            WHERE card_selected = 'BLACK'
                                AND s.game_session_id = '%s';",
                        $mysqli->real_escape_string($game_session_id));
        $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $total_black_cards = (int)$row['total'];
            } else {
                $total_black_cards = "No rows found for game_session_id: $game_session_id";
            }
        } else {
            $total_black_cards = "Error: " . $mysqli->error;
        }
        $result->close();
    
    //Average # of black cards played per round
        $average_black_cards_per_round = $total_black_cards/$total_rounds;
    
    //Total Number of red Cards Played
        $sql = sprintf("SELECT COUNT(card_selected) AS total
                            FROM player_round_history h
                                JOIN student_game_session s
                                    ON h.session_id = s.session_id
                            WHERE card_selected = 'RED'
                                AND s.game_session_id = '%s';",
                        $mysqli->real_escape_string($game_session_id));
        $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $total_red_cards = (int)$row['total'];
            } else {
                $total_red_cards = "No rows found for game_session_id: $game_session_id";
            }
        } else {
            $total_red_cards = "Error: " . $mysqli->error;
        }
        $result->close();

    //Average # of red cards played per round
        $average_red_cards_per_round = $total_red_cards/$total_rounds;

    //Player with the highest score
        $sql = sprintf("SELECT p.first_nm, h.round_score 
		                    FROM player_round_history h 
                                JOIN student_profile p
			                        ON h.user_id = p.student_id
		                        JOIN student_game_session g
			                        ON g.session_id = h.session_id
		                    WHERE g.game_session_id = '%s'
		                    ORDER BY h.round_score DESC
                            LIMIT 1;",
                        $mysqli->real_escape_string($game_session_id));
        $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $name = $row["first_nm"];
                $score = $row["round_score"];
                $player_top_score = $name . " - " . $score;
            } else {
                $total_red_cards = "No rows found for game_session_id: $game_session_id";
            }
        } else {
            $total_red_cards = "Error: " . $mysqli->error;
        }
        $result->close();

    //Player with the lowest score
        $sql = sprintf("SELECT p.first_nm, h.round_score 
		                    FROM player_round_history h 
                                JOIN student_profile p
			                        ON h.user_id = p.student_id
		                        JOIN student_game_session g
			                        ON g.session_id = h.session_id
		                    WHERE g.game_session_id = '%s'
		                    ORDER BY h.round_score ASC
                            LIMIT 1;",
                        $mysqli->real_escape_string($game_session_id));
        $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $name = $row["first_nm"];
                $score = $row["round_score"];
                $player_bottom_score = $name . " - " . $score;
            } else {
                $total_red_cards = "No rows found for game_session_id: $game_session_id";
            }
        } else {
            $total_red_cards = "Error: " . $mysqli->error;
        }
        $result->close();

    //Player with the highest number of Black cards played
        $sql = sprintf("SELECT h.session_id, COUNT(card_selected) AS card_count, p.first_nm
		                    FROM player_round_history h
			                    JOIN student_profile p
				                    ON h.user_id = p.student_id
			                    JOIN student_game_session g
				                    ON g.session_id = h.session_id
		                    WHERE h.card_selected = 'BLACK'
                                AND g.game_session_id = '%s'
		                    GROUP BY h.session_id
		                    ORDER BY card_count DESC
		                    LIMIT 1;",
                        $mysqli->real_escape_string($game_session_id));
        $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $name = $row["first_nm"];
                $card_count = $row["card_count"];
                $player_top_black_cards = $name . " - " . $card_count;
            } else {
                $total_red_cards = "No rows found for game_session_id: $game_session_id";
            }
        } else {
            $total_red_cards = "Error: " . $mysqli->error;
        }
        $result->close();

    //Player with the lowest number of Black cards played
        $sql = sprintf("SELECT h.session_id, COUNT(card_selected) AS card_count, p.first_nm
		                    FROM player_round_history h
			                    JOIN student_profile p
				                    ON h.user_id = p.student_id
			                    JOIN student_game_session g
				                    ON g.session_id = h.session_id
		                    WHERE h.card_selected = 'BLACK'
                                AND g.game_session_id = '%s'
		                    GROUP BY h.session_id
		                    ORDER BY card_count ASC
		                    LIMIT 1;",
                        $mysqli->real_escape_string($game_session_id));
        $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $name = $row["first_nm"];
                $card_count = $row["card_count"];
                $player_bottom_black_cards = $name . " - " . $card_count;
            } else {
                $total_red_cards = "No rows found for game_session_id: $game_session_id";
            }
        } else {
            $total_red_cards = "Error: " . $mysqli->error;
        }
        $result->close();

    //Player with the highest number of Red cards played
        $sql = sprintf("SELECT h.session_id, COUNT(card_selected) AS card_count, p.first_nm
		                    FROM player_round_history h
			                    JOIN student_profile p
				                    ON h.user_id = p.student_id
			                    JOIN student_game_session g
				                    ON g.session_id = h.session_id
		                    WHERE h.card_selected = 'RED'
                                AND g.game_session_id = '%s'
		                    GROUP BY h.session_id
		                    ORDER BY card_count DESC
		                    LIMIT 1;",
                        $mysqli->real_escape_string($game_session_id));
        $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $name = $row["first_nm"];
                $card_count = $row["card_count"];
                $player_top_red_cards = $name . " - " . $card_count;
            } else {
                $total_red_cards = "No rows found for game_session_id: $game_session_id";
            }
        } else {
            $total_red_cards = "Error: " . $mysqli->error;
        }
        $result->close();

    //Player with the lowest number of Red cards played
        $sql = sprintf("SELECT h.session_id, COUNT(card_selected) AS card_count, p.first_nm
		                    FROM player_round_history h
			                    JOIN student_profile p
				                    ON h.user_id = p.student_id
			                    JOIN student_game_session g
				                    ON g.session_id = h.session_id
		                    WHERE h.card_selected = 'RED'
                                AND g.game_session_id = '%s'
		                    GROUP BY h.session_id
		                    ORDER BY card_count ASC
		                    LIMIT 1;",
                        $mysqli->real_escape_string($game_session_id));
        $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $name = $row["first_nm"];
                $card_count = $row["card_count"];
                $player_bottom_red_cards = $name . " - " . $card_count;
            } else {
                $total_red_cards = "No rows found for game_session_id: $game_session_id";
            }
        } else {
            $total_red_cards = "Error: " . $mysqli->error;
        }
        $result->close();
?>

<!DOCTYPE html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Red &amp; Black Game View</title>
    <link href="/CSSGametheory/css/admin.css" rel="stylesheet" />
    <script src="/CSSGametheory/JavaScript/gameRetrieve.js" defer></script>
    <script src="/CSSGametheory/JavaScript/createGame.js" defer></script>
</head>
<body>
<link href="https://cssgametheory.com/CSSGametheory/css/header.css" rel="stylesheet" />
<p style="margin: 0 auto; width: 200px"><img id="logo" src="https://cssgametheory.com/CSSGametheory/Img/logo.svg"></p> 

    <h1>Game (Red Card Black Card)</h1>

    <table class="infoTable">
        <tr>
            <th>Game Name</th>
            <td>Red Card Black Card</td>
        </tr>
        <tr>
            <th>Game Date</th>
            <td><?php echo $create_dt; ?></td>
        </tr>
    </table>

    <table class="infoTable">
        <tr>
            <th>Players</th>
            <td><?php echo $total_players; ?></td>
        </tr>
        <tr>
            <th>Rounds</th>
            <td><?php echo $total_rounds; ?></td>
        </tr>
    </table>

    <table class="infoTable">
        <tr>
            <th>Total Number of Black Cards PLayed</th>
            <td><?php echo $total_black_cards; ?></td>
        </tr>
        <tr>
            <th>Average Number of Black Cards Played per Round</th>
            <td><?php echo $average_black_cards_per_round; ?></td>
        </tr>
        <tr>
            <th>Total Number of Red Cards Played</th>
            <td><?php echo $total_red_cards; ?></td>
        </tr>
        <tr>
            <th>Average Number of Red Cards Played per Round</th>
            <td><?php echo $average_red_cards_per_round; ?></td>
        </tr>
    </table>

    <table class="infoTable">
        <tr>
            <th>Player with Highest Score</th>
            <td><?php echo $player_top_score; ?></td>
        </tr>
        <tr>
            <th>Player with Lowest Score</th>
            <td><?php echo $player_bottom_score; ?></td>
        </tr>
        <tr>
            <th>Player with Highest Number of Black Cards</th>
            <td><?php echo $player_top_black_cards; ?></td>
        </tr>
        <tr>
            <th>Player with Lowest Number of Black Cards</th>
            <td><?php echo $player_bottom_black_cards; ?></td>
        </tr>
        <tr>
            <th>Player with Highest Number of Red Cards</th>
            <td><?php echo $player_top_red_cards; ?></td>
        </tr>
        <tr>
            <th>Player with Lowest Number of Red Cards</th>
            <td><?php echo $player_bottom_red_cards; ?></td>
        </tr>
    </table>

    <table id="links">
        <tbody>
            <tr>
                <td><a href="/CSSGametheory/HTML/admin/allStudents.php">All Students</a></td>
                <td><a href="https://cssgametheory.com/">Back to Home</a></td>
                <td><a href="/CSSGametheory/HTML/admin/allGames.php">All Games</a></td>
            </tr>
        </tbody>
    </table>
</body>
<script language="javascript">
    window.onload = function(e){ 
        displayInfoRedBlack();
    }
</script>
</html>
