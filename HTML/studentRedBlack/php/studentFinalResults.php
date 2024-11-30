<?php
    // Start the session if not already started
    session_start();
    //Pulls game_session_id from the session into a variable to be used
        $game_session_id = $_SESSION["game_session_id"];
        $link_id = $_SESSION["link_id"];
    //Connect to database
        $mysqli = require __DIR__ ."/db.php";

    //Gather all valid Pairing IDs
        $sql = sprintf("SELECT pairing_id
                            FROM session_partner p
                                JOIN student_game_session g
                                    ON g.link_id = p.link_id
                            WHERE g.game_session_id = '%s'
                                AND p.link_id = '%s'
                            ORDER BY round_num ASC",
        $mysqli->real_escape_string($game_session_id),
        $mysqli->real_escape_string($link_id));
        $result = $mysqli->query($sql);
        if ($result->num_rows > 0) {
            //Gets all valid pairing IDs
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[] = $row['pairing_id'];
            }
            //print_r($data);
            
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        
        //Get the question text for the form
        $sql = sprintf("SELECT question_txt
                            FROM game_session s
                                JOIN question_for_student q
                                    ON q.game_id = s.game_id
                            WHERE s.game_session_id = '%s'",
        $mysqli->real_escape_string($game_session_id));

        $result = $mysqli->query($sql);

        $question_array = [];
        while( $row = $result->fetch_assoc() ) {
            $question_array[] = $row["question_txt"];
		}

        $is_submitted = false;
        //POST request, processing and saving of question responses
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $sql = sprintf("SELECT session_id, user_id
                                FROM student_game_session
                                WHERE link_id = '%s'",
                                $mysqli->real_escape_string($link_id));

            $result = $mysqli->query($sql);
            $row2 = $result->fetch_assoc();

            $session_id = $row2["session_id"];
            $user_id = $row2["user_id"];

            //Submits response for question 1
            $sql = sprintf("INSERT INTO question_submission (session_id, 
                                                                user_id, 
                                                                game_session_id, 
                                                                question_txt, 
                                                                response_txt)
                                VALUES ('%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s')",
                                $mysqli->real_escape_string($session_id),
                                $mysqli->real_escape_string($user_id),
                                $mysqli->real_escape_string($game_session_id),
                                $mysqli->real_escape_string($question_array[0]),
                                $mysqli->real_escape_string($_POST['beginningStrategy'])
                            );

            $result = $mysqli->query($sql);

            //Submits response for question 2
            $sql = sprintf("INSERT INTO question_submission (session_id, 
                                                                user_id, 
                                                                game_session_id, 
                                                                question_txt, 
                                                                response_txt)
                                VALUES ('%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s')",
                                $mysqli->real_escape_string($session_id),
                                $mysqli->real_escape_string($user_id),
                                $mysqli->real_escape_string($game_session_id),
                                $mysqli->real_escape_string($question_array[1]),
                                $mysqli->real_escape_string($_POST['endingStrategy'])
                            );

            $result = $mysqli->query($sql);

            //Set's parameter to true, hiding the form
            $is_submitted = true;
        }

    //Close the connection
    $mysqli->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<title></title>
	<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="/CSSGametheory/css/studentFinalResults.css" rel="stylesheet" />
    <script>
        function validateForm() {
            var beginningStrategy = document.getElementById("beginningStrategy").value;
            var endingStrategy = document.getElementById("endingStrategy").value;

            // Check if either textarea is empty
            if (beginningStrategy === "" || endingStrategy === "") {
                alert("Please fill in all fields");
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }
    </script>
</head>
<body>
    
<link href="https://cssgametheory.com/CSSGametheory/css/header.css" rel="stylesheet" />
<p style="margin: 0 auto; width: 200px"><img id="logo" src="https://cssgametheory.com/CSSGametheory/Img/logo.svg"></p> 
<br><br><br><br><br>

<!--- style with CSS --->
<div id="playHistory">
<h1 id="playHistoryHeader"><u>Play History:</u></h1>

<table><!--- 9 rows, 4 cols --->
	<tbody>
		<tr>
			<td>#</td>
			<td>You</td>
			<td>Partner</td>
			<td>Score</td>
		</tr>
		<?php
            //Connect to database
            $mysqli = require __DIR__ ."/db.php";
            $round_num = 1;
            
            foreach ($data as $row) {
            // Get the round score for you
                $sqlUser = sprintf("SELECT h.card_selected, h.round_score
                                FROM player_round_history h
                                    JOIN student_game_session l
                                        ON l.link_id = h.link_id
                                WHERE l.game_session_id = '%s' -- session
                                    AND l.link_id = '%s' -- session
                                    AND h.round_num = '%s'
                                    AND round_score IS NOT NULL
                                ORDER BY h.round_num ASC",
                $mysqli->real_escape_string($game_session_id),
                $mysqli->real_escape_string($link_id),
                $mysqli->real_escape_string($round_num)
                );

                // Get the result
                $resultUser = $mysqli->query($sqlUser);

                // Get the row of data
                $rowUser = $resultUser->fetch_assoc();

            // Get the card played by partner
                $sqlPartner = sprintf("SELECT h.card_selected
                                    FROM session_partner p
                                        JOIN player_round_history h
                                            ON p.link_id = h.link_id
                                    WHERE h.link_id != '%s'
                                        AND p.pairing_id = '%s' -- Itterated for each pairing ID
                                        AND h.round_num = '%s' -- Itterated in loop",
                                $mysqli->real_escape_string($link_id),
                                $mysqli->real_escape_string($data[$round_num - 1]),
                                $mysqli->real_escape_string($round_num)
                            );

                // Get the result
                $resultPartner = $mysqli->query($sqlPartner);

                // Get the row of data
                $rowPartner = $resultPartner->fetch_assoc();

            // Display the row
                    echo "<tr><td>" . '#' . $round_num . "</td><td>" . $rowUser["card_selected"] . "</td><td>" . $rowPartner["card_selected"] . "</td><td>"
                    . $rowUser["round_score"] . "</td></tr>";
                //echo "</table>";
                $round_num++;
            }
                // Close the statement
                $mysqli->close();
        ?>
	</tbody>
</table>
</div>
<!--- style with CSS --->

<div id="reflectionQuestions"><!---placeholder submit page; replace when needed for PHP --->
<!-- Hides form when the question is submitted -->
<?php if ($is_submitted == false): ?>
<form onsubmit="return validateForm()" method="POST">
<h1 id="reflectionQuestionsHeader"><u>Reflection Questions:</u></h1>

<table><!--- 5 rows, 1 col --->
	<tbody>
		<tr>
			<td><label for="beginningStrategy"><?php echo $question_array[0]; ?></label></td>
		</tr>
		<tr>
			<td><!--- what do we want the character limit to be? setting to 500 for now ---><textarea cols="48" id="beginningStrategy" maxlength="500" name="beginningStrategy" rows="6"></textarea></td>
		</tr>
		<tr>
			<td><label for="endingStrategy"><?php echo $question_array[1]; ?></label></td>
		</tr>
		<tr>
			<td><!--- same character limit here? ---><textarea cols="48" id="endingStrategy" maxlength="500" name="endingStrategy" rows="6"></textarea></td>
		</tr>
		<tr>
			<td><input type="submit" value="Submit" /></td>
		</tr>
	</tbody>
</table>
</form>
<?php else: ?>
    <!-- TODO: Customize this area of the HTML for the answers submission page -->
    <h1>Thank you for submitting your answers!!</h1>
    <form action="studentEndGame.php" method="POST">
        <button>End Game</button>
    </form>
<?php endif; ?>
</div>
</body>
</html>