<?php
    //Connect to database
    $mysqli = require __DIR__ ."/db.php";

    //Update all active sessions that are not from today to 'I'
    $sql = "UPDATE game_session
	            SET is_active = 'I'
                WHERE is_active = 'A'
                    AND expiration_dt < SYSDATE();";
    $result = $mysqli->query($sql);
?>