<?php
    //Connect to database
    $mysqli = require __DIR__ ."/db.php";

    // Start the session if not already started
    session_start();

    //Save values from session as variables
    $link_id = $_SESSION["link_id"];
    $game_session_id = $_SESSION["game_session_id"];
    $round_num = $_SESSION["round_num"];

    //Get required values for POST
    $sql = sprintf("SELECT session_id, user_id
	                    FROM student_game_session
                        WHERE link_id = '%s'",
                    $mysqli->real_escape_string($link_id))
                    ;

    $result = $mysqli->query($sql);

    $cred = $result->fetch_assoc();
    $session_id = $cred["session_id"];
    $user_id = $cred["user_id"];
    $_SESSION["session_id"] = $session_id;
    $_SESSION["user_id"] = $user_id;

    //Get the game score as a variable
    $sql = sprintf("SELECT SUM(h.round_score) AS total_score
	                    FROM player_round_history h
		                    JOIN student_game_session g
			                    ON h.user_id = g.user_id
	                    WHERE g.game_session_id = '%s'
                            AND h.link_id = '%s'",
                    $mysqli->real_escape_string($game_session_id),
                    $mysqli->real_escape_string($link_id))
                    ;

    $result = $mysqli->query($sql);

    $totalScoreResult = $result->fetch_assoc();
    if ($totalScoreResult["total_score"] != NULL) {
        $total_score = $totalScoreResult["total_score"];
    } else {
        $total_score = 0;
    }

    //Gets this round's pairing ID
    $sql = sprintf("SELECT pairing_id FROM session_partner WHERE link_id = '%s' AND round_num = '%s';",
                        $mysqli->real_escape_string($link_id),
                        $mysqli->real_escape_string($round_num));
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();

    $pairing_id = $row["pairing_id"];
    error_log("Pairing_id: ". $pairing_id);
    
    //Gets user's partner for the current round
    $sql = sprintf("SELECT CONCAT(s.first_nm, ' ', s.last_nm) AS partner_full_nm
                        FROM session_partner p
                            JOIN student_game_session g
                                ON g.link_id = p.link_id
                            JOIN student_profile s
                                ON s.student_id = g.user_id
                        WHERE p.round_num = '%s'
                            AND p.link_id != '%s'
                            AND g.game_session_id = '%s'
                            AND p.pairing_id = '%s';",
                    $mysqli->real_escape_string($round_num),
                    $mysqli->real_escape_string($link_id),
                    $mysqli->real_escape_string($game_session_id),
                    $mysqli->real_escape_string($pairing_id)
                    );

    error_log("round_num: ". $round_num);
    error_log("link_id: ". $link_id);
    error_log("game_session_id: ". $game_session_id);
    $result = $mysqli->query($sql);

    $partnerResult = $result->fetch_assoc();
    $partner_full_nm = $partnerResult["partner_full_nm"];
    error_log("partner_full_nm: ". $partner_full_nm);
    
    //Gets the game instructions
    $sql = sprintf("SELECT p.game_instruction_txt
	                    FROM red_black_card_param p
		                    JOIN game_session g
			                    ON g.red_black_param = p.red_black_id
	                    WHERE g.game_session_id = '%s'",
                        $mysqli->real_escape_string($game_session_id))
                        ;

    $result = $mysqli->query($sql);

    $instResults = $result->fetch_assoc();
    $inst_txt = $instResults["game_instruction_txt"];

    //If the partner is NULL
    if ($partner_full_nm == NULL) {
        $partner_full_nm = "BOT ACT.";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8">
	<title></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
	<link href="/CSSGametheory/css/studentRedBlack.css" rel="stylesheet" />
</head>
<style>
    /* Add some basic styling to the tooltip */
    .tooltip {
        position: relative;
        display: inline-block;
    }

    /* Hide the tooltip text by default */
    .tooltip .tooltiptext {
        visibility: hidden;
        width: 120px;
        background-color: #555;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        top: 100%;
        left: 50%;
        margin-left: -60px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    /* Show the tooltip text when hovering over the button */
    .tooltip:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }
</style>
<body>

<div id="cards">
<form method="POST" action="cardSubmitted.php">
    <link href="https://cssgametheory.com/CSSGametheory/css/header.css" rel="stylesheet" />
    <p style="margin: 0 auto; width: 200px"><img id="logo" src="https://cssgametheory.com/CSSGametheory/Img/logo.svg"></p> 
    <h1><u>Select a Card:</u></h1>
    <div class="cardsFlex">
    <div class="tooltip">
    <button type="submit" name="submit" value="RED">
        <img src="/CSSGametheory/Img/kingHearts.svg" alt="Red Card Image">
    </button>
    <div class="tooltiptext">This card gives you 50 points</div>
    </div>
    <div class="tooltip">
    <button type="submit" name="submit" value="BLACK">
        <img src="/CSSGametheory/Img/kingSpades.svg" alt="Black Card Image">
    </button>
    <div class="tooltiptext">This card gives your partner 150 points</div>
    </div>
    </div>
</form>
<div id="instructions" class="testing">
<table>
	<tbody>
		<tr>
			<td><strong>Round <?php echo $round_num; ?>:</strong></td>
			<td><?php echo $total_score; ?></td>
		</tr>
		<tr>
			<td><strong>Partner:</strong></td>
			<td><?php if ($round_num > 4) {echo $partner_full_nm;} else {echo "Hidden Partner";} ?></td>
		</tr>
		<tr>
			<td colspan="2" style="color:white;"><strong>Instructions:</strong> <?php echo $inst_txt; ?></td>
		</tr>
	</tbody>
</table>
</div>
</div>
</body>
</html>