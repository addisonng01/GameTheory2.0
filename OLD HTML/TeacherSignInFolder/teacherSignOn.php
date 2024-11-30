<?php
    $is_invalid = false;

    //Clears the session when the page is reached
    session_start();
    session_destroy();
    session_start();

    $passwordHash = password_hash($_POST["password"], PASSWORD_DEFAULT);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $mysqli = require __DIR__ ."/db.php";

        $sql = sprintf("SELECT * FROM teacher_profile p
                        JOIN login_credential l
                            ON l.teacher_id = p.teacher_id
                        WHERE p.email = '%s'",
                        $mysqli->real_escape_string($_POST["email"])
                        );

        $result = $mysqli->query($sql);    

        $user = $result->fetch_assoc();
        
        $teacher_id = $user["teacher_id"];
        
        //Included checking for the hashed passwords
        if ($user && password_verify($_POST["password"], $user["user_password"])) {

            //Check if a session_instance has been created

            $sqlSession = sprintf("SELECT * FROM session_instance
                                    WHERE teacher_id = '%s'
                                    AND active_session = 'A'",
                                //$mysqli->real_escape_string($_POST["$teacher_id"]));
                                $mysqli->real_escape_string($teacher_id));
            
            $resultSession = $mysqli->query($sqlSession);

            $sessionInstance = $resultSession->fetch_assoc();

            $session_id = $sessionInstance["session_id"];

            if ($sessionInstance) {
                //If there is an existing session_instance for this user

                $sqlActive = sprintf("SELECT * FROM session_instance
                                        WHERE teacher_id = '%s'
                                        AND active_session = 'A'
                                        AND expiration_dt > current_timestamp()",
                                    //$mysqli->real_escape_string($_POST["$teacher_id"]));
                                    $mysqli->real_escape_string($teacher_id));
                
                $resultActive = $mysqli->query($sqlActive);

                $sessionActive = $resultActive->fetch_assoc();

                if (! $sessionActive) {
                    //Set to expired session_instance to inactive
                    $sqlSetInactive = sprintf("UPDATE session_instance
                                                SET active_session = 'I'
                                                WHERE session_id = '%s';",
                                                $mysqli->real_escape_string($session_id));

                    $resultSetInactive = $mysqli->query($sqlSetInactive);

                    //Create a new session for the user
                    $sqlNewSession = sprintf("INSERT INTO session_instance (teacher_id,  
                                                                        active_session, 
                                                                        expiration_dt)
                                            VALUES ('%s', 'A', current_timestamp() + interval 1 day)", 
                                        //$mysqli->real_escape_string($_POST["$teacher_id"]));
                                        $mysqli->real_escape_string($teacher_id));

                    $resultNewSessionQuerry= $mysqli->query($sqlNewSession);

                    //I do not need to fetch the results of the insert querry, there are no results to fetch.
                    //$newActiveSession = $resultNewSessionQuerry->fetch_assoc();

                    $sqlRetrieveNewSession = sprintf("SELECT * FROM session_instance
                                                    WHERE teacher_id = '%s'
                                                    AND active_session = 'A'
                                                    AND expiration_dt > current_timestamp()",
                                                //$mysqli->real_escape_string($_POST["$teacher_id"]));
                                                $mysqli->real_escape_string($teacher_id));

                    $resultRetrievedNewSession = $mysqli->query($sqlRetrieveNewSession);

                    $resultFetchSession = $resultRetrievedNewSession->fetch_assoc();

                    //session_start();
                    $_SESSION["teacher_id"] = $user["teacher_id"];
                    $_SESSION["session_id"] = $resultFetchSession["session_id"];
                } else {
                    //session_start();
                    $_SESSION["teacher_id"] = $user["teacher_id"];
                    $_SESSION["session_id"] = $sessionActive["session_id"];
                }

                header("Location: /CSSGametheory/HTML/admin/teacherSelectGame.php");
                exit;

            } else {
                //If there is NOT an existing session_instance for this user

                //Create a new session for the user
                $sqlNewSession = sprintf("INSERT INTO session_instance (teacher_id,  
                                                                        active_session, 
                                                                        expiration_dt)
                                            VALUES ('%s', 'A', current_timestamp() + interval 1 day)", 
                                        //$mysqli->real_escape_string($_POST["$teacher_id"]));
                                        $mysqli->real_escape_string($teacher_id));

                $resultNewSessionQuerry= $mysqli->query($sqlNewSession);

                //I do not need to fetch the results of the insert querry, there are no results to fetch.
                //$newActiveSession = $resultNewSessionQuerry->fetch_assoc();

                $sqlRetrieveNewSession = sprintf("SELECT * FROM session_instance
                                                    WHERE teacher_id = '%s'
                                                    AND active_session = 'A'
                                                    AND expiration_dt > current_timestamp()",
                                                //$mysqli->real_escape_string($_POST["$teacher_id"]));
                                                $mysqli->real_escape_string($teacher_id));

                $resultRetrievedNewSession = $mysqli->query($sqlRetrieveNewSession);

                $resultFetchSession = $resultRetrievedNewSession->fetch_assoc();

                //session_start();
                $_SESSION["teacher_id"] = $user["teacher_id"];
                $_SESSION["session_id"] = $resultFetchSession["session_id"];

                header("Location: /CSSGametheory/HTML/admin/teacherSelectGame.php");
                exit;
            }
        } else {
            $is_invalid = true;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" type="text/css" href="/CSSGametheory/css/TeacherSignOn.css"/>
</head>
<body>
<div class="container">
    <div class="logo"></div>
    <h2>The College of St. Scholastica</h2>
    <form class="form" method="POST">
        <div class="input-container">
            <input id="email" name="email" required="" placeholder="Email"/>
        </div>

        <div class="input-container">
            <input type="password" id="password" name="password" required="" placeholder="Password"/>
        </div>

        <div class="input-container">
            <input id="signOnsubmit" name="signOnsubmit" onclick="return submitForm()" type="submit" value="Login" />
        </div>

        <?php if ($is_invalid): ?>
            <em style="color:red; text-align:center;">Invalid Login Credentials</em>
        <?php endif; ?>
    </form>
    <div class="menu">
        <a href="teacherCreateProfile.php">Create a profile</a>
    </div>
</div>
</body>
</html>