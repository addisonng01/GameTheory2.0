<?php

include('../../../env.php');

$dbHost = $_ENV['DB_HOST'] ?? 'default_host';
$dbName = $_ENV['DB_NAME'] ?? 'default_name';
$dbUser = $_ENV['DB_USER'] ?? 'default_user';
$dbPass = $_ENV['DB_PASS'] ?? 'default_password';

$mysqli = new mysqli($dbhost, $dbUser, $dbPass, $dbname);

if ($mysqli->connect_errno) {
    die("Connection error: ". $mysqli->connect_error);
}

return $mysqli;