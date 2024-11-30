<?php

    $mysqli = require __DIR__ ."/db.php";

    //Required variable declaration
    $session_id;
    $student_id;
    $first_nm;
    $last_nm;
    $email;
    $num_rb_games;
    $high_score;
    $low_score;
    $rb_point_average;

    //Parsing the game_session_id from the URL
        session_start();
        //print_r($_SESSION);
        $var = $_SERVER['QUERY_STRING'];
        //print_r($var);
        $parts = explode('=', $var);
        if (count($parts) > 1) {
            $session_id = $parts[1];
        } else {
            echo'';
        }
        //print_r($game_session_id);
    
    //Save the Game Date to a variable
        $sql = sprintf("SELECT student_id FROM session_instance
                            WHERE session_id = '%s';",
                                $mysqli->real_escape_string($session_id));
                                $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $student_id = $row['student_id'];
            } else {
                $student_id = NULL;
            }
        } else {
            $create_dt = "Error: " . $mysqli->error;
        }
        $result->close();

    //Save the First Name to a variable
        $sql = sprintf("SELECT first_nm FROM student_profile
                            WHERE student_id = '%s';",
                                $mysqli->real_escape_string($student_id));
                                $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $first_nm = $row['first_nm'];
            } else {
                $first_nm = "There is no value for this with the ID of: " . $session_id;
            }
        } else {
            $first_nm = "Error: " . $mysqli->error;
        }
        $result->close();

    //Save the Last Name to a variable
        $sql = sprintf("SELECT last_nm FROM student_profile
                            WHERE student_id = '%s';",
                                $mysqli->real_escape_string($student_id));
                                $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $last_nm = $row['last_nm'];
            } else {
                $last_nm = "There is no value for this with the ID of: " . $session_id;
            }
        } else {
            $last_nm = "Error: " . $mysqli->error;
        }
        $result->close();

    //Save the Email to a variable
        $sql = sprintf("SELECT email FROM student_profile
                            WHERE student_id = '%s';",
                                $mysqli->real_escape_string($student_id));
                                $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $email = $row['email'];
            } else {
                $email = "There is no value for this with the ID of: " . $session_id;
            }
        } else {
            $email = "Error: " . $mysqli->error;
        }
        $result->close();
    
    //Save the Number of RB Games Played to a variable
        $sql = sprintf("SELECT COUNT(game_session_id) AS num_games 
                            FROM student_game_session
                            WHERE session_id = '%s';",
                                $mysqli->real_escape_string($session_id));
                                $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $num_rb_games = $row['num_games'];
            } else {
                $num_rb_games = "There is no value for this with the ID of: " . $session_id;
            }
        } else {
            $num_rb_games = "Error: " . $mysqli->error;
        }
        $result->close();

    //Save the high score to a variable
        $sql = sprintf("SELECT user_id, session_id, SUM(round_score) AS total_score
                            FROM player_round_history
                            WHERE user_id = '%s'
                            GROUP BY user_id, session_id
                            ORDER BY total_score DESC
                            LIMIT 1;",
                                $mysqli->real_escape_string($student_id));
                                $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $high_score = $row['total_score'];
            } else {
                $high_score = "There is no value for this with the ID of: " . $session_id;
            }
        } else {
            $high_score = "Error: " . $mysqli->error;
        }
        $result->close();

    //Save the high score to a variable
        $sql = sprintf("SELECT user_id, session_id, SUM(round_score) AS total_score
                            FROM player_round_history
                            WHERE user_id = '%s'
                            GROUP BY user_id, session_id
                            ORDER BY total_score ASC
                            LIMIT 1;",
                                $mysqli->real_escape_string($student_id));
                                $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $low_score = $row['total_score'];
            } else {
                $low_score = "There is no value for this with the ID of: " . $session_id;
            }
        } else {
            $low_score = "Error: " . $mysqli->error;
        }
        $result->close();

    //Save the game point average to a variable
        $sql = sprintf("SELECT user_id, session_id, SUM(round_score) AS average_score
                            FROM player_round_history
                            WHERE user_id = '%s';",
                                $mysqli->real_escape_string($student_id));
                                $result = $mysqli->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $rb_point_average = $row['average_score'];
                if ($num_rb_games > 0) {
                    $rb_point_average = $rb_point_average/$num_rb_games;
                } else {
                    $rb_point_average = "Student has not played any games";
                }
                
            } else {
                $rb_point_average = "There is no value for this with the ID of: " . $session_id;
            }
        } else {
            $rb_point_average = "Error: " . $mysqli->error;
        }
        $result->close();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student View</title>
    <link href="/CSSGametheory/css/admin.css" rel="stylesheet" />
    <script src="/CSSGametheory/JavaScript/studentRetrieve.js" defer></script>
    <script src="/CSSGametheory/JavaScript/createGame.js" defer></script>
</head>
<body>
  <link href="https://cssgametheory.com/CSSGametheory/css/header.css" rel="stylesheet" />
<p style="margin: 0 auto; width: 200px"><img id="logo" src="https://cssgametheory.com/CSSGametheory/Img/logo.svg"></p> 

    <h1 id="studentHeader" class="centeredTitle">Student (<?php echo $first_nm; ?> <?php echo $last_nm; ?>)</h1>

    <table class="infoTable">
        <tr>
            <th>First Name:</th>
            <td><?php echo $first_nm; ?></td>
        </tr>
        <tr>
            <th>Last Name:</th>
            <td><?php echo $last_nm; ?></td>
        </tr>
        <tr>
            <th>Email:</th>
            <td><?php echo $email; ?></td>
        </tr>
        <tr>
            <th>Number of Red/Black Games Played:</th>
            <td><?php echo $num_rb_games; ?></td>
        </tr>
        <tr>
            <th>Highest Final Score:</th>
            <td><?php echo $high_score; ?></td>
        </tr>
        <tr>
            <th>Lowest Final Score:</th>
            <td><?php echo $low_score; ?></td>
        </tr>
        <tr>
            <th>Game Point Average:</th>
            <td><?php echo $rb_point_average; ?></td>
        </tr>
        <tr>
            <th>Number of Wheat &amp; Steel Games Played:</th>
            <td><span id="wheatSteelNum">0</span></td>
        </tr>
        <tr>
            <th>Highest Total Wheat Production:</th>
            <td><span id="wheatHigh">0</span></td>
        </tr>
        <tr>
            <th>Wheat Consumption Goals Met:</th>
            <td><span id="wheatGoalNum">0</span></td>
        </tr>
        <tr>
            <th>Highest Total Steel Production:</th>
            <td><span id="steelHigh">0</span></td>
        </tr>
        <tr>
            <th>Steel Consumption Goals Met:</th>
            <td><span id="steelGoalNum">0</span></td>
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
</html>

