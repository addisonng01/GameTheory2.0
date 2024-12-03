<?php
$is_invalid = false;

// Session initialization
session_start();
if (isset($_SESSION)) {
    session_destroy();
}

// Check for POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mysqli = require __DIR__ . "./db.php";

    $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $is_invalid = true;
    } else {
        $stmt = $mysqli->prepare("SELECT * FROM student_profile WHERE email = ?");
        $stmt->bind_param("s", $email);

        if (!$stmt->execute()) {
            die("Error executing query: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $student_id = $user["student_id"];

            // Expire existing active sessions
            $stmtExpire = $mysqli->prepare(
                "UPDATE session_instance SET active_session = 'I' WHERE student_id = ? AND active_session = 'A'"
            );
            $stmtExpire->bind_param("s", $student_id);
            $stmtExpire->execute();

            // Create new session
            $stmtInsert = $mysqli->prepare(
                "INSERT INTO session_instance (student_id, active_session, expiration_dt)
                 VALUES (?, 'A', current_timestamp() + interval 1 day)"
            );
            $stmtInsert->bind_param("s", $student_id);
            $stmtInsert->execute();

            // Retrieve the new session
            $stmtRetrieve = $mysqli->prepare(
                "SELECT * FROM session_instance WHERE student_id = ? AND active_session = 'A'"
            );
            $stmtRetrieve->bind_param("s", $student_id);
            $stmtRetrieve->execute();
            $newSession = $stmtRetrieve->get_result()->fetch_assoc();

            session_start();
            $_SESSION["user_id"] = $student_id;
            $_SESSION["session_id"] = $newSession["session_id"];

            header("Location: studentEnterCode.php");
            exit;
        } else {
            $is_invalid = true;
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head><meta charset="utf-8">
<title>Student Sign On</title>
<link rel="stylesheet" type="text/css" href="https://cssgametheory.com/CSSGametheory/css/TeacherSignOn.css">
</head>
<body>
<div class="container">
            <h2 id="signInHeader">Sign In</h2>



    <form method="POST">
        <div class="input-container">
           <label for="Email" class="input-label">Email</label>
            <input id="email" name="email" required/>
            </div>
                <?php if ($is_invalid): ?>
        <em style="color:red; text-align:center;">Unrecognized Email: Please Try Again</em>
    <?php endif; ?>
      
            <input id="signOnsubmit" name="signOnsubmit" class="input" onclick="return submitForm()" type="submit" value="Submit" /></form>
            <a href="studentCreateProfile.php" class="menu">Create New Account</a>
              </div>
    <script src="https://cssgametheory.com/CSSGametheory/JavaScript/script.js"></script>
</body>
</html>




















