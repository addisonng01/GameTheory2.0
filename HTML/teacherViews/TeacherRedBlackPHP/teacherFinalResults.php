<?php
    // Start the session if not already started
        session_start();
    //Pulls game_session_id from the session into a variable to be used
        $game_session_id = $_SESSION["game_session_id"];
?>

<!DOCTYPE html>
<html>
<head><meta charset="utf-8">
</head>
<style type="text/css">
    table {
        border-collapse: collapse;
        width: 45%;
        color: #588c7e;
        font-family: monospace;
        font-size: 25px;
        text-align: left;
        border: 1px solid;
    }
    th, td {
        border: 1px solid;
    }
    
</style>
<body>
    <form action="gameEnded.php" method="POST">
        <button>End Game</button>
    </form>
    <h1>Cards Played Per Round</h1>
    <table>
        <tr>
            <th>Round</th>
            <th>Red</th>
            <th>Black</th>
        </tr>
        <?php
                //Connect to database
                    $mysqli = require __DIR__ ."/db.php";

                //Runs and gets the results for the total of Red and Black cards played per round query
                    $sqlCount = sprintf("SELECT round_num,
                                            SUM(CASE WHEN card_selected = 'RED' THEN 1 ELSE 0 END) AS red_count,
                                            SUM(CASE WHEN card_selected = 'BLACK' THEN 1 ELSE 0 END) AS black_count
                                            FROM player_round_history h
                                                JOIN student_game_session g
                                                    ON h.link_id = g.link_id
                                            WHERE g.game_session_id = '%s'
                                            GROUP BY round_num;",
                                            $mysqli->real_escape_string($game_session_id));
                    $resultCount = $mysqli->query($sqlCount);

                    //echo $resultCount;
        
                    if ($resultCount->num_rows > 0) {
                        //If the query function's properly
                        while ($row = $resultCount->fetch_assoc()) {
                            echo "<tr><td>" . $row["round_num"] . "</td><td>" . $row["red_count"] ."</td><td>". $row["black_count"] . "</td></tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "No Results";
                    }
                    $mysqli-> close();
        ?>
    </table>
    <h1>Final Leader Board</h1>
    <table>
        <tr>
            <th>Ranking</th>
            <th>Name</th>
            <th>Score</th>
            <th>R : B</th>
        </tr>
        <?php
                //Connect to database
                    $mysqli = require __DIR__ ."/db.php";

                //Gets the results for the final leader board
                    $sqlLeader = sprintf("SELECT CONCAT(p.first_nm, ' ', p.last_nm) AS full_nm,
                                                    SUM(round_score) AS total_score, 
                                                    CONCAT(SUM(CASE WHEN h.card_selected = 'RED' THEN 1 ELSE 0 END), 
                                                    ' : ', 
                                                    SUM(CASE WHEN h.card_selected = 'BLACK' THEN 1 ELSE 0 END)) AS ratio
                                            FROM player_round_history h
                                                JOIN student_game_session g
                                                    ON h.link_id = g.link_id
                                                JOIN student_profile p
                                                    ON p.student_id = g.user_id
                                            WHERE g.game_session_id = '%s'
                                            GROUP BY p.first_nm
                                            ORDER BY total_score DESC",
                                            $mysqli->real_escape_string($game_session_id));
                    $resultLeader = $mysqli->query($sqlLeader);

                    if ($resultLeader->num_rows > 0) {
                        //If the query function's properly

                        //Var to keep track of each row entry
                        $rowCount = 0;

                        while ($row = $resultLeader->fetch_assoc()) {
                            $rowCount++;
                            echo "<tr><td>" . '#' . $rowCount . "</td><td>" . $row["full_nm"] . "</td><td>" . $row["total_score"] ."</td><td>". $row["ratio"] . "</td></tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "No Results";
                    }
                    $mysqli-> close();
        ?>
    </table>
</body>
</html>