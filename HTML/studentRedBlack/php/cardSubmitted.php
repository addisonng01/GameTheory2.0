<?php
//The page is refreshing, so the POST is not being called anymore?
//if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //if (isset($_POST["submit"])) {
        $buttonValue = $_POST["submit"]; // button value is passed in from the form, which means it disappears once the form refreshes w/ Chris' solution
        //Connect to database
        $mysqli = require __DIR__ ."/db.php";

        // Start the session if not already started
        session_start();
        $link_id = $_SESSION["link_id"];
        $game_session_id = $_SESSION["game_session_id"];
        $round_num = $_SESSION["round_num"];
        $session_id = $_SESSION["session_id"];
        $user_id = $_SESSION["user_id"];

        //Checks if the play history has been recorded
        $sql = sprintf("SELECT * FROM player_round_history WHERE link_id = '%s' AND round_num = '%s'",
                            $mysqli->real_escape_string($link_id),
                            $mysqli->real_escape_string($round_num)
                        );
        $result = $mysqli->query($sql);

        if ($result->num_rows == 0) {
            //Add to play history
            $sql = sprintf("INSERT INTO player_round_history (user_id, session_id, link_id, round_num, card_selected)
	                            VALUES ('%s', 
			                            '%s', 
                                        '%s', 
                                        '%s', 
                                        '%s' 
                                        )", 
                            $mysqli->real_escape_string($user_id),
                            $mysqli->real_escape_string($session_id),
                            $mysqli->real_escape_string($link_id),
                            $mysqli->real_escape_string($round_num),
                            $mysqli->real_escape_string($buttonValue) // button value gets saved to the DB
                        );

            $result = $mysqli->query($sql);
        }
        else{
            // grabs selected card from DB
            $sqlCard = sprintf("SELECT card_selected FROM player_round_history WHERE link_id = '%s' AND round_num = '%s'",
                $mysqli->real_escape_string($link_id),
                $mysqli->real_escape_string($round_num)
            );
            $result = $mysqli->query($sqlCard);
            $resultCard = $result->fetch_assoc();
            $buttonValue = $resultCard['card_selected'];
        }
        
        //Check if the the student's round_num is equal to or greater than the number of rounds_completed in the red_back_session, if so, it should redirect to the roundResults page
            //Get the param
        $sql = sprintf("SELECT * FROM red_black_session WHERE game_session_id = '%s' ORDER BY rb_session_id DESC LIMIT 1;",
                            $mysqli->real_escape_string($game_session_id)
                        );
        $result = $mysqli->query($sql);
        $round_concluded;

        //Check if the round is over
        $row = $result->fetch_assoc();
        if ($row['rounds_complete'] >= $round_num) {
            //echo '<br><br><br><br>The round is OVER, go to the next round<br>';
            //Redirect to the round complete page
            header ("Location: studentPartnerResult.php");
            exit();
        }
    //}
//}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="/CSSGametheory/css/cardSubmitted.css">
  <script>
        //Refreshes the page, shows new students in the lobby
		function autoRefresh() {
			window.location = window.location.href;
		}
		setInterval('autoRefresh()', 5000);
    </script>
</head>
<body>
<link href="https://cssgametheory.com/CSSGametheory/css/header.css" rel="stylesheet" />
<p style="margin: 0 auto; width: 200px"><img id="logo" src="https://cssgametheory.com/CSSGametheory/Img/logo.svg"></p> 
<br> <br>

  <!--- meant to be brief --->
    <div class="card-container">
    <div id="cards">
        <h1><u>You Have Selected:</u></h1>
        <input type="hidden" value="<?php echo htmlspecialchars($buttonValue);?>" id="cardType"> 
        <img src="/CSSGametheory/Img/kingHearts.svg" id="chosenCard" alt="image of red or black card">
    </div>
    <div id="pleaseWait">
        <h2><strong>Please wait until card selection has concluded.</strong></h2>
        <div class="loader"></div>
    </div>
</div>
</body>
<script src="/CSSGametheory/JavaScript/cardRetrieve.js" defer></script>
</html>