<?php

    $is_invalid = false;
    
    //Clears the session when the page is reached
        session_start();
        session_destroy();
        session_start();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $mysqli = require __DIR__ ."/db.php";

        $sql = sprintf("SELECT * FROM student_profile 
                    WHERE email = '%s'",
                    $mysqli->real_escape_string($_POST["email"]));

        $result = $mysqli->query($sql);    

        $user = $result->fetch_assoc();
        
        $student_id = $user["student_id"];

        if ($user) {

            //Check if a session_instance has been created

            $sqlSession = sprintf("SELECT * FROM session_instance
                                    WHERE student_id = '%s'
                                    AND active_session = 'A'",
                                //$mysqli->real_escape_string($_POST["$student_id"]));
                                $mysqli->real_escape_string($student_id));
            
            $resultSession = $mysqli->query($sqlSession);

            $sessionInstance = $resultSession->fetch_assoc();

            $session_id = $sessionInstance["session_id"];

            if ($sessionInstance) {
                //If there is an existing session_instance for this user

                $sqlActive = sprintf("SELECT * FROM session_instance
                                        WHERE student_id = '%s'
                                        AND active_session = 'A'
                                        AND expiration_dt > current_timestamp()",
                                    //$mysqli->real_escape_string($_POST["$student_id"]));
                                    $mysqli->real_escape_string($student_id));
                
                $resultActive = $mysqli->query($sqlActive);

                $sessionActive = $resultActive->fetch_assoc();

                //if (! $sessionActive) {
                    //Set to expired session_instance to inactive
                    $sqlSetInactive = sprintf("UPDATE session_instance
                                                SET active_session = 'I'
                                                WHERE session_id = '%s';",
                                                $mysqli->real_escape_string($session_id));

                    $resultSetInactive = $mysqli->query($sqlSetInactive);

                    //Create a new session for the user
                    $sqlNewSession = sprintf("INSERT INTO session_instance (student_id,  
                                                                        active_session, 
                                                                        expiration_dt)
                                            VALUES ('%s', 'A', current_timestamp() + interval 1 day)", 
                                        //$mysqli->real_escape_string($_POST["$student_id"]));
                                        $mysqli->real_escape_string($student_id));

                    $resultNewSessionQuerry= $mysqli->query($sqlNewSession);

                    //I do not need to fetch the results of the insert querry, there are no results to fetch.
                    //$newActiveSession = $resultNewSessionQuerry->fetch_assoc();

                    $sqlRetrieveNewSession = sprintf("SELECT * FROM session_instance
                                                    WHERE student_id = '%s'
                                                    AND active_session = 'A'
                                                    AND expiration_dt > current_timestamp()",
                                                //$mysqli->real_escape_string($_POST["$student_id"]));
                                                $mysqli->real_escape_string($student_id));

                    $resultRetrievedNewSession = $mysqli->query($sqlRetrieveNewSession);

                    $resultFetchSession = $resultRetrievedNewSession->fetch_assoc();

                    session_start();
                    $_SESSION["user_id"] = $user["student_id"];
                    $_SESSION["session_id"] = $resultFetchSession["session_id"];

                //}
                
                header("Location: studentEnterCode.php");
                exit;
            } else {
                //If there is NOT an existing session_instance for this user

                //Create a new session for the user
                $sqlNewSession = sprintf("INSERT INTO session_instance (student_id,  
                                                                        active_session, 
                                                                        expiration_dt)
                                            VALUES ('%s', 'A', current_timestamp() + interval 1 day)", 
                                        //$mysqli->real_escape_string($_POST["$student_id"]));
                                        $mysqli->real_escape_string($student_id));

                $resultNewSessionQuerry= $mysqli->query($sqlNewSession);

                //I do not need to fetch the results of the insert querry, there are no results to fetch.
                //$newActiveSession = $resultNewSessionQuerry->fetch_assoc();

                $sqlRetrieveNewSession = sprintf("SELECT * FROM session_instance
                                                    WHERE student_id = '%s'
                                                    AND active_session = 'A'
                                                    AND expiration_dt > current_timestamp()",
                                                //$mysqli->real_escape_string($_POST["$student_id"]));
                                                $mysqli->real_escape_string($student_id));

                $resultRetrievedNewSession = $mysqli->query($sqlRetrieveNewSession);

                $resultFetchSession = $resultRetrievedNewSession->fetch_assoc();

                session_start();
                $_SESSION["user_id"] = $user["student_id"];
                $_SESSION["session_id"] = $resultFetchSession["session_id"];

                header("Location: studentEnterCode.php");
                exit;
            }
        } else {
            $is_invalid = true;
        }

    }

?>

<!DOCTYPE html>
<html>
<head><meta charset="utf-8">
<title>Student Sign On</title>
<link rel="stylesheet" type="text/css" href="/CSSGametheory/css/TeacherSignOn.css">
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
</body>
</html>




















