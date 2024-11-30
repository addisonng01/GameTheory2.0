<?php
//Create the session
session_start();

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $mysqli = require __DIR__ ."/db.php";

    $sql = sprintf("SELECT * FROM student_profile WHERE email = '%s'",
                   $mysqli->real_escape_string($_POST["Email"]));

    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();

    if (!$user) {
        //Save user information to the session
        $_SESSION["first_nm"] = $_POST["firstName"];
        $_SESSION["last_nm"] = $_POST["lastName"];
        $_SESSION["Email"] = $_POST["Email"];
        
        //Add user to the DB
        $sql = "INSERT INTO student_profile (first_nm, last_nm, email) VALUES (?,?,?)";

        $stmt = $mysqli->stmt_init();

        if (!$stmt->prepare($sql)) {
            die("SQL error: " . $mysqli->error);
        }

        $stmt->bind_param("sss", $_POST["firstName"], $_POST["lastName"], $_POST["Email"]);

        if ($stmt->execute()) {
            header("Location: studentProfileCreated.php");
            exit;
        } else {
            die($mysqli->error . " " . $mysqli->errno);
        }
    } else {
        $is_invalid = true;
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="/CSSGametheory/css/TeacherSignOn.css">
    </head>
    <body>

        <div class="container">
            <h2 id="signInHeader">Create a Profile</h2>

            <form method="POST">
                <?php if ($is_invalid): ?>
                    <em style="color:red;">That email is in use</em>
                <?php endif; ?>

                <div class="input-container">
                    <label for="Email" class="input-label">Email</label>
                    <input name="Email" id="Email" required type="email" value="<?= htmlspecialchars($_POST["Email"] ?? "") ?>">
                </div>

                <div class="input-container">
                    <label for="firstName" class="input-label">First Name</label>
                    <input name="firstName" id="firstName" required type="text">
                </div>

                <div class="input-container">
                    <label for="lastName" class="input-label">Last Name</label>
                    <input name="lastName" id="lastName" required type="text">
                </div>

                <input type="submit" class="input" value="Create Profile">
            </form>
        </div>

    </body>
</html>
