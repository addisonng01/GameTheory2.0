<?php

$host = "localhost";
$dbname = "txlxmqol_GameTheory_2.0";
$username = "txlxmqol_webToDB";
$password = "ItsBeginingToLookLikeChristmas";

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_errno) {
    die("Connection error: ". $mysqli->connect_error);
}

return $mysqli;