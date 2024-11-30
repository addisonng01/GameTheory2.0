<?php
    session_start();
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/CSSGametheory/css/TeacherSignOn.css" rel="stylesheet" />
    <title>Enter Game Code</title>
</head>
<body>
    <div class="container"> <!-- This div acts as the container for your centered content -->
        <h1 id="gameHeader">Red Card Black Card</h1>
        <h2 id="gameName">Enter Code to Join Game Room</h2>
        <div id="codeInput" class="input-container"> <!-- Added class input-container -->
            <?php if($_SESSION["game_exists"] == 'F') { ?>
                <h1>The game code is invalid, try again</h1>
            <?php }; ?>
            <form method="POST" action="StudentEnteredCode.php">
                <input type="text" maxlength="6" size="12" id="gameCode" name="gameCode" class="input-field"> <!-- Added class input-field -->
                <input id="codeSubmit" type="submit" value="submit" class="input-field"> <!-- Added class input-field -->
            </form>
            <?php if (isset($_SESSION["user_id"])): ?>
                <p>You are logged in.</p>
            <?php else: ?>
                <p>You are not logged in.</p>
            <?php endif; ?>
        </div>
    </div>
    <!-- The rest of your HTML -->
</body>
</html>