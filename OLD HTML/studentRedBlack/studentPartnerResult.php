<?php
    //Connect to database
    $mysqli = require __DIR__ ."/db.php";

    // Start the session if not already started
    session_start();

    //Save values from session as variables
    $link_id = $_SESSION["link_id"];
    $round_num = $_SESSION["round_num"];
    $game_session_id = $_SESSION["game_session_id"];
    $total_num_rounds = 12; //TODO: When adding functionality for multiple rulesets, make this dynamic

    //Get this round's pairing ID
    $sql = sprintf("SELECT pairing_id
	                    FROM session_partner p
                        WHERE p.round_num = '%s' 
                            AND p.link_id = '%s'",
                    $mysqli->real_escape_string($round_num),
                    $mysqli->real_escape_string($link_id)
                    );

    $result = $mysqli->query($sql);

    $result = $result->fetch_assoc();
    $pairing_id = $result["pairing_id"];

    //Get partner's link_id
    $sql = sprintf("SELECT link_id
	                    FROM session_partner
                        WHERE pairing_id = '%s'
		                    AND link_id != '%s'",
                    $mysqli->real_escape_string($pairing_id),
                    $mysqli->real_escape_string($link_id)
                    );

    $result = $mysqli->query($sql);

    $result = $result->fetch_assoc();
    $partner_link_id = $result["link_id"];
    
    //Fetches the partner's name
    $partner_name = "NONE";
    
    $sql = sprintf("SELECT CONCAT(p.first_nm, ' ', p.last_nm) AS full_nm FROM student_profile p JOIN student_game_session g ON g.user_id = p.student_id WHERE g.link_id = '%s';", $mysqli->real_escape_string($partner_link_id));
    
    $result = $mysqli->query($sql);
    
    $row = $result->fetch_assoc();
    if ($row != NULL) {
        $partner_name = $row["full_nm"];
    }
    
    //Check if the partner has not played a card
    $sql = sprintf("SELECT card_selected
                        FROM player_round_history
                        WHERE link_id = '%s'
                            AND round_num = '%s';",
                    $mysqli->real_escape_string($partner_link_id),
                    $mysqli->real_escape_string($round_num)
                    );
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    
    //Bool parameter that checks if the partner's card has been selected or not
    $partner_card_check;
    
    if ($row["card_selected"] != NULL) {
        $partner_card_check = true;
    } else {
        $partner_card_check = false;
    }
    

    //Declare variables that will be used later
    $partner_previous_total_score;


    //Checks if this user has a partner and if they submitted their selection
    if ($partner_link_id != NULL && $partner_card_check) {
        //Get partner's card played this round
        $sql = sprintf("SELECT card_selected
	                        FROM player_round_history
	                        WHERE link_id = '%s'
		                        AND round_num = '%s'",
                        $mysqli->real_escape_string($partner_link_id),
                        $mysqli->real_escape_string($round_num)
                        );
        $result = $mysqli->query($sql);
        $result = $result->fetch_assoc();
        $partner_card_selected = $result["card_selected"];

        //Fetches your partner's total before this round
        $sql = sprintf("SELECT SUM(round_score) AS total
                            FROM player_round_history
                            WHERE link_id = '%s'
                            AND round_num < '%s'",
                        $mysqli->real_escape_string($partner_link_id),
                        $mysqli->real_escape_string($round_num)
                        );

        $result = $mysqli->query($sql);
        $result = $result->fetch_assoc();

        if ($result["total"] == NULL) {
            $partner_previous_total_score = 0;
        } else {
            $partner_previous_total_score = $result["total"];
        }
    } else {
        //Checks if the BOT selection has not been made, ensures that the selection does not change on refresh
        if (!isset($_SESSION["bot_select"])){
            //If there is an odd number of people, someone will not have a partner
            //Bot that picks randomly RED or BLACK
            // Generate a random number (0 or 1)
            $randomNumber = rand(0, 1);

            // Define two values
            $value1 = "RED";
            $value2 = "BLACK";

            // Use the random number to choose between the two values
            $partner_card_selected = ($randomNumber == 0) ? $value1 : $value2;

            //Sets the session variable so this does not run again
            $_SESSION["bot_select"] = $partner_card_selected;
        } else {
            $partner_card_selected = $_SESSION["bot_select"];
        }
        //Previous Score setting
        $partner_previous_total_score = 0;
    }    

    //Get your card from this round
    $sql = sprintf("SELECT card_selected
	                    FROM player_round_history
	                    WHERE link_id = '%s'
		                    AND round_num = '%s'",
                    $mysqli->real_escape_string($link_id),
                    $mysqli->real_escape_string($round_num)
                    );

    $result = $mysqli->query($sql);

    $result = $result->fetch_assoc();
    $my_card_selected = $result["card_selected"];

    //Calculate and set returns
    $my_returns = 0;
    $partner_returns = 0;

    //What did you do
    if ($my_card_selected == 'RED'){
        $my_returns += 50;
    } else if ($my_card_selected == 'BLACK') {
        $partner_returns += 150;
    }

    //What did your partner do
    if ($partner_card_selected == 'RED') {
        $partner_returns += 50;
    } else if ($partner_card_selected == 'BLACK') {
        $my_returns += 150;
    }

    //Fetches your score total before this round
    $sql = sprintf("SELECT SUM(round_score) AS total
	                    FROM player_round_history
                        WHERE link_id = '%s'
		                AND round_num < '%s'",
                    $mysqli->real_escape_string($link_id),
                    $mysqli->real_escape_string($round_num)
                    );

    $result = $mysqli->query($sql);

    $result = $result->fetch_assoc();
    
    $my_previous_total_score;

    if ($result["total"] == NULL) {
        $my_previous_total_score = 0;
    } else {
        $my_previous_total_score = $result["total"];
    }


    //Creates a vairable to store the new score total for you and your partner
    $my_total = $my_previous_total_score + $my_returns;

    $partner_total = $partner_previous_total_score + $partner_returns;

    //Updates your(user)'s score
        //Need to ensure that this does not run multiple times on refresh
    $sql = sprintf("SELECT round_score FROM player_round_history WHERE link_id = '%s' AND round_num = '%s'",
                    $mysqli->real_escape_string($link_id),
                    $mysqli->real_escape_string($round_num)
                    );
    $result = $mysqli->query($sql);
    $result = $result->fetch_assoc();
    if ($result["round_score"] == NULL) {
        $sql = sprintf("UPDATE player_round_history
	                    SET round_score = '%s'
                        WHERE link_id = '%s'
		                AND round_num = '%s'",
                    $mysqli->real_escape_string($my_returns),
                    $mysqli->real_escape_string($link_id),
                    $mysqli->real_escape_string($round_num)
                    );
        $result = $mysqli->query($sql);
    }

    //Checks if the teacher has begun the next round, round_concluded == 'N'
    $sql = sprintf("SELECT * FROM red_black_session WHERE game_session_id = '%s' ORDER BY rb_session_id DESC LIMIT 1;",
                            $mysqli->real_escape_string($game_session_id)
                        );
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();

    if ($row["round_concluded"] == 'N') {
        
        //Unsets the session variable for next time
        unset($_SESSION['bot_select']);
        
        if ($total_num_rounds > $round_num) {
            //If there are still more rounds to be played
            $_SESSION['round_num'] = $round_num + 1;
            header('Location: studentCardSelect.php');
        } else {
            //If the last round has been reached
            header('Location: studentFinalResults.php');
        }
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title></title>
	<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
	<link href="/CSSGametheory/css/studentPartnerResult.css" rel="stylesheet" />
    <script>
        //Refreshes the page, shows new students in the lobby
		function autoRefresh() {
			window.location = window.location.href;
		}
		setInterval('autoRefresh()', 10000);
    </script>
</head>
<body>
    
<link href="https://cssgametheory.com/CSSGametheory/css/header.css" rel="stylesheet" />
<p style="margin: 0 auto; width: 200px"><img id="logo" src="https://cssgametheory.com/CSSGametheory/Img/logo.svg"></p> 
<br> <br>

<div class="everythingContainer">
<div id="results">
<table>
	<tbody>
		<tr>
			<td colspan="2">
			<h1 class="bottomHalf" id="roundNumHead">Round: <?php echo $round_num; ?></h1>
			<!--- make dynamic ---></td>
		</tr>
		<tr>
			<td>
			<h1 class="short">You Played</h1>
			</td>
			<td>
			<h1 class="short">Your Partner (<?php echo $partner_name; ?>) Played: <?php if (!$partner_card_check) {echo "Partner did not select in time, BOT selection";} ?></h1>
			</td>
		</tr>
		<!--- the wireframes have an off-white filter over the cards ---><!--- might be difficult to display different image depending on round info-->
		<tr>
            <?php if ($my_card_selected == 'RED'): ?>
                <td class="card"><img id="youPlayed" src="/CSSGametheory/Img/kingHearts.svg" /></td>
            <?php elseif ($my_card_selected == 'BLACK'): ?>
                <td class="card"><img id="youPlayed" src="/CSSGametheory/Img/kingSpades.svg" /></td>
            <?php endif; ?>
            <?php if ($partner_card_selected == 'RED'): ?>
                <td class="card"><img id="partnerPlayed" src="/CSSGametheory/Img/kingHearts.svg" /></td>
            <?php elseif ($partner_card_selected == 'BLACK'): ?>
                <td class="card"><img id="partnerPlayed" src="/CSSGametheory/Img/kingSpades.svg" /></td>
            <?php endif; ?>
		</tr>
        
	</tbody>
</table>
<div class="resultsContainer">
<div id="roundResults">
<table>
	<tbody>
		<tr>
			<td colspan="2"><u>Returns:</u></td>
		</tr>
		<tr>
			<td><strong>You:</strong></td>
			<td><?php echo $my_returns; ?></td>
		</tr>
		<tr>
			<td><strong>Partner:</strong></td>
			<td><?php echo $partner_returns; ?></td>
		</tr>
		<tr>
		</tr>
	</tbody>
</table>
</div>

<div id="totalScores">
<table>
	<tbody>
		<tr>
			<td colspan="2"><u>Total Scores:</u></td>
		</tr>
		<tr>
			<td><strong>You:</strong></td>
			<td><?php echo $my_total; ?></td>
		</tr>
		<tr>
			<td><strong>Partner:</strong></td>
			<td><?php echo $partner_total; ?></td>
		</tr>
		<tr>
		</tr>
	</tbody>
</table>
</div>
</div>
</div>
<!--- make points dynamic ---><!--- fix styling with CSS --->

</div>
</body>
</html>