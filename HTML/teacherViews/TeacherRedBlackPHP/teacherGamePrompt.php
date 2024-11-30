<?php
    //Connect to database
    $mysqli = require __DIR__ ."/db.php";

    // Start the session if not already started
    session_start();

    //Get values from the session
    $game_session_id = $_SESSION["game_session_id"];
    $round_num = $_SESSION["round_num"];

    //Required values
    $total_num_players = 0;
    $total_num_submit = 0;

    //Gets all link_id that are tied to this game
    $sql = sprintf("SELECT link_id FROM student_game_session WHERE game_session_id = '%s'", 
                        $mysqli->real_escape_string($game_session_id));
    $result = $mysqli->query($sql);
    // Check if the query was successful
    if ($result) {
        // Initialize an empty array to store the results
        $list = array();

        // Fetch each row from the result set
        while ($row = $result->fetch_assoc()) {
            // Add each row to the list
            $list[] = $row["link_id"];
        }
        //Set the count of players
        $total_num_players = count($list);

        //Checks how many players have submitted their response
        for ($i = 0; $i < $total_num_players; $i++) {
            $sql = sprintf("SELECT card_selected FROM player_round_history WHERE link_id = '%s' AND round_num = '%s'",
                            $mysqli->real_escape_string($list[$i]), $mysqli->real_escape_string($round_num)
                        );
            $result = $mysqli->query($sql);
            $row = $result->fetch_assoc();

            if ($row["card_selected"] != NULL) {
                $total_num_submit++;
            }
        }

    } else {
        // If the query fails, handle the error
        echo "Error: " . $mysqli->error;
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Card Game</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="../../css/teacherRedBlack.css" rel="stylesheet">
    <script>
        //Refreshes the page, shows new students in the lobby
		function autoRefresh() {
			window.location = window.location.href;
		}
		setInterval('autoRefresh()', 5000);
    </script>
</head>

<body>
    <div class="container">
        <div class="instructions">
            <h2>Instructions:</h2>
            <p>In all rounds, if you play a red card you receive $50 and the other person receives nothing. If you play
                a black card you receive nothing and the other person receives $150. Participating in this exercise and
                answering the questions at the end of this is worth ten points.</p>
            <p>The person who has the greatest total receives 10 additional points and $10 and the person who has the
                second greatest total receives 5 additional points and $5 dollars.</p>
            <p>If you both have the same total, you will split the additional points and money.</p>
        </div>
        <div class="cards">
            <div class="card">
                <img src="../../Img/kingHearts.svg" alt="King of Hearts">
                <input type="text" value="Red gives you 50 points" readonly>
            </div>
            <div class="card">
                <img src="../../Img/kingSpades.svg" alt="King of Spades">
                <input type="text" value="Black gives your partner 150 points" readonly>
            </div>
        </div>
        <div class="game-info">
            <h1>Players Ready: <?php echo $total_num_submit . "/" . $total_num_players; ?></h1>
            <form action="/CSSGametheory/HTML/teacherRedBlack/teacherCompleteRound.php" method="POST">
                <button class="finish-round">Finish Round</button>
            </form>
        </div>
    </div>
</body>

</html>