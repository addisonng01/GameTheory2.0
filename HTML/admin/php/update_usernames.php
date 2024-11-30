<?php
// Connect to database
$mysqli = require __DIR__ . "/db.php";

// Start the session if not already started
session_start();

// TODO: Change this out once the teacher creates a new session
$_SESSION["game_session_id"] = 1;
$game_session_id = $_SESSION["game_session_id"];

// Function to get updated data from the database
function getUpdatedUsernames($mysqli, $game_session_id)
{
    $sql = sprintf("SELECT CONCAT(p.first_nm, ' ', p.last_nm) AS full_nm
                        FROM student_game_session s
                        JOIN student_profile p ON p.student_id = s.user_id
                        WHERE s.game_session_id = '%s'",
                        $mysqli->real_escape_string($game_session_id)
    );

    $result = $mysqli->query($sql);

    $usernames = [];
    while ($row = $result->fetch_assoc()) {
        $usernames[] = $row["full_nm"];
    }

    return $usernames;
}

// Fetch updated data
$usernames = getUpdatedUsernames($mysqli, $game_session_id);

// Distribute usernames into rows
$row1 = [];
$row2 = [];
$row3 = [];

foreach ($usernames as $index => $name) {
    if ($index % 3 == 0) {
        $row1[] = $name;
    } elseif ($index % 3 == 1) {
        $row2[] = $name;
    } else {
        $row3[] = $name;
    }
}

$mysqli->close();

// Return data in JSON format
echo json_encode([
    'row1' => implode("", array_map(function ($name) {
        return "<div class='username'>$name</div>";
    }, $row1)),
    'row2' => implode("", array_map(function ($name) {
        return "<div class='username'>$name</div>";
    }, $row2)),
    'row3' => implode("", array_map(function ($name) {
        return "<div class='username'>$name</div>";
    }, $row3)),
]);
?>
