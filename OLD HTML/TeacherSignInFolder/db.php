<?php

$host = "localhost";
$dbname = "txlxmqol_Game_Theory_DB";
$username = "txlxmqol_webToDB";
$password = "ItsBeginingToLookLikeChristmas";

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_errno) {
    die("Connection error: ". $mysqli->connect_error);
}

return $mysqli;