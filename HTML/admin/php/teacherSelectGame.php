<?php
    // Start the session if not already started
	session_start();

	//Connect to database
    $mysqli = require __DIR__ ."/db.php";

    // Get required data from the session
    $teacher_id = $_SESSION["teacher_id"];

	//Get data from the database
	$sqlGameCatalog = "SELECT * FROM game_catalog";

	$resultCatalog = $mysqli->query($sqlGameCatalog);

	$mysqli->close();
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="/CSSGametheory/css/teacherGameSelect.css">
</head>

<body style="overflow: hidden;">
    <link href="https://cssgametheory.com/CSSGametheory/css/header.css" rel="stylesheet" />
    <p style="margin: 0 auto; width: 200px"><img id="logo" src="https://cssgametheory.com/CSSGametheory/Img/logo.svg"></p> 

    <div style="width: 100%; height: 100%; position: relative; display: flex; justify-content: center; align-items: center; flex-direction: column;">
        <div class="select-game">Select Game</div>
        
        <div class="button-container">
            <div class="game-wrapper">
                <form method="POST" action="teacherSelectedGameScript.php">
                    <?php 
                        // LOOP TILL END OF DATA
                        while($rows = $resultCatalog->fetch_assoc()) {
                            $game_id = $rows['game_id'];
                            $game_title = $rows['game_title'];
                            if ($game_id == "1") {
                    ?>
                        <button type="submit" name="submit" value="redBlack" class="game-button" id="redBlackSubmit">
                            <img src="/CSSGametheory/Img/RedCardBlackCard.jpeg" alt="Red Card Black Card" class="game-button" id="redBlackIcon">
                        </button>
                        <h3 class="game-title" style="width: 350px;" id="gameOneTitle"><?php echo $game_title; ?></h3>
                    <?php
                            }
                        }
                    ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Blue Button Linking to All Games Page -->
   <div style="position: fixed; bottom: 20px; right: 880px;">
    <a href="https://cssgametheory.com/CSSGametheory/HTML/admin/allGames.php">
        <button style="background-color: blue; color: white; padding: 14px 28px; border: none; border-radius: 5px; font-size: 20px;">
            Admin Panel
        </button>
    </a>
</div>

</body>
</html>
