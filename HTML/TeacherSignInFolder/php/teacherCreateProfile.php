<?php

    $is_invalid = false;

    $passwordHash = password_hash($_POST["password"], PASSWORD_DEFAULT);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $mysqli = require __DIR__ ."/db.php";

    $sql = sprintf("SELECT * FROM teacher_profile
                    WHERE email = '%s'",
                    $mysqli->real_escape_string($_POST["email"]));

    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();

    if (!$user) {
        //If the user is not found
        $sql = sprintf("INSERT INTO teacher_profile (first_nm, last_nm, email) 
                        VALUES ('%s', '%s', '%s')", 
                        $mysqli->real_escape_string($_POST["firstName"]),
                        $mysqli->real_escape_string($_POST["lastName"]),
                        $mysqli->real_escape_string($_POST["email"]),
                    );

        $result = $mysqli->query($sql);

        $selSql = sprintf("SELECT * FROM teacher_profile
                            WHERE email = '%s'",
                            $mysqli->real_escape_string($_POST["email"]));
        
        $selResult = $mysqli->query($selSql);

        $user = $selResult->fetch_assoc();
        $teacher_id = $user["teacher_id"];

        $loginSql = sprintf("INSERT INTO login_credential (teacher_id, username, user_password)
                                VALUES ('%s', '%s', '%s')",
                                $mysqli->real_escape_string($teacher_id),
                                $mysqli->real_escape_string($_POST["email"]),
                                $mysqli->real_escape_string($passwordHash)
                                );
        
        $loginResult = $mysqli->query($loginSql);

        session_start();
        $_SESSION["firstName"] = $user["first_nm"];
        $_SESSION["lastName"] = $user["last_nm"];
        $_SESSION["email"] = $user["email"];

        header("Location: teacherProfileCreated.php");
        exit;

            
    } else {
        //If the user is found
        $is_invalid = true;
    }
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="/CSSGametheory/css/TeacherSignOn.css">
        <script type="text/javascript">
            function validateForm() {
                var email = document.getElementById("email").value;
                var firstName = document.getElementById("firstName").value;
                var lastName = document.getElementById("lastName").value;
                var password = document.getElementById("password").value;
                var verifyPassword = document.getElementById("verifyPassword").value;

                if(email === "" || firstName === "" || lastName === "" || password === "" || verifyPassword === "") {
                    alert("All fields are required!");
                    return false;
                }
                if(password !== verifyPassword) {
                    alert("Passwords do not match!");
                    return false;
                }

                return true;
            }
        </script>
    </head>
    <body>

        <div class="container">
            <h2 id="signInHeader">Create a Profile Teacher</h2>

            <?php if ($is_invalid): ?>
                <em style="color:red;">That email is in use</em>
            <?php endif; ?>

            <form method="POST" onsubmit="return validateForm()">
                <div class="input-container">
                    <label for="email" class="input-label">Email:</label>
                    <input id="email" name="email" required type="email">
                </div>

                <div class="input-container">
                    <label for="firstName" class="input-label">First Name:</label>
                    <input id="firstName" name="firstName" required type="text">
                </div>

                <div class="input-container">
                    <label for="lastName" class="input-label">Last Name:</label>
                    <input id="lastName" name="lastName" required type="text">
                </div>

                <div class="input-container">
                    <label for="password" class="input-label">Password:</label>
                    <input id="password" name="password" required type="password">
                </div>

                <div class="input-container">
                    <label for="verifyPassword" class="input-label">Verify Password:</label>
                    <input id="verifyPassword" name="verifyPassword" required type="password">
                </div>

                <input type="submit" class="input" value="Create Profile">
            </form>
        </div>

    </body>
</html>